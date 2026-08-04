<?php

namespace Yakamara\YCom\YForm\Value;

use Redaxo\Core\Addon\Addon;
use Redaxo\Core\Content\Article;
use Redaxo\Core\Exception\RuntimeException;
use Redaxo\Core\ExtensionPoint\Extension;
use Redaxo\Core\ExtensionPoint\ExtensionPoint;
use Redaxo\Core\Filesystem\Url;
use Redaxo\Core\Http\Request;
use Redaxo\Core\Http\Response;
use Redaxo\Core\Log\Logger;
use Redaxo\Core\Translation\I18n;
use Yakamara\YCom\Auth\Auth;
use Yakamara\YCom\Config;
use Yakamara\YCom\User;
use Yakamara\YForm\Value\AbstractValue;
use Yakamara\YRewrite\YRewrite;

use function count;
use function explode;
use function implode;
use function in_array;
use function is_array;
use function is_file;
use function json_decode;
use function ltrim;
use function preg_match;
use function sprintf;

use const E_WARNING;

/**
 * Shared plumbing of the external identity providers (SAML, OAuth2).
 *
 * REDAXO 5 shipped this as `rex_yform_trait_value_auth_extern`, a trait mixed into each field class.
 * An abstract intermediate class works just as well here — both fields have to extend
 * {@see AbstractValue} anyway — and it lets the shared parts declare what a subclass must provide
 * instead of relying on undeclared properties being present.
 */
abstract class AbstractExternalAuth extends AbstractValue
{
    /**
     * Which keys of the identity provider's attribute set map onto which ycom column.
     *
     * The first key that is present wins. Everything beyond these three is a project concern —
     * hook `YCOM_AUTH_MATCHING` (or the per-provider point) to add more.
     */
    private const array ATTRIBUTE_MAP = [
        'email' => ['User.email', 'emailAddress', 'email'],
        'firstname' => ['User.FirstName', 'givenName', 'first_name'],
        'name' => ['User.LastName', 'surName', 'last_name'],
    ];

    /** Whether a request without the provider's own parameters should start the flow anyway. */
    protected bool $directLink = false;

    /** `saml` / `oauth2` — names the config file, the session vars and the i18n keys. */
    abstract protected function authKey(): string;

    /**
     * The session variables this provider owns, so a fresh attempt starts from a clean state.
     *
     * @return list<string>
     */
    abstract protected function sessionVars(): array;

    /**
     * Reads `var/data/addons/ycom/<authKey>.php`, which defines `$settings`.
     *
     * @return array<string, mixed>
     */
    protected function loadSettings(): array
    {
        $path = Addon::require('ycom')->getDataPath($this->authKey() . '.php');

        if (!is_file($path)) {
            throw new RuntimeException(sprintf(
                '%s settings file not found [%s]. It is created by ycom\'s install; copy it from install/%s.php.',
                $this->authKey(),
                $path,
                $this->authKey(),
            ));
        }

        $settings = [];
        require $path;

        if (!is_array($settings) || 1 > count($settings)) {
            throw new RuntimeException(sprintf('%s settings file [%s] does not define $settings.', $this->authKey(), $path));
        }

        return $settings;
    }

    /**
     * Where to send the visitor once the provider has authenticated them.
     *
     * Runs through {@see Auth::getReturnTo()}, so a `returnTo` from the request can only point at a
     * domain this installation serves or one the field explicitly allows.
     */
    protected function getReturnToUrl(): string
    {
        return Auth::getReturnTo(
            [
                Request::request('returnTo', 'string', ''),
                Url::article((int) Config::get('article_id_jump_ok')),
            ],
            $this->getAllowedDomains(),
        );
    }

    /** @return list<string> */
    protected function getAllowedDomains(): array
    {
        $domains = (string) $this->getElement(3);

        return '' === $domains ? [] : explode(',', $domains);
    }

    /**
     * Renders the "sign in with …" link.
     *
     * A provider-specific fragment (`ycom_auth_saml`) wins over the generic one, so a project can
     * give one provider its own markup.
     */
    protected function renderLoginLink(string $url): void
    {
        if (!$this->needsOutput()) {
            return;
        }

        $this->params['form_output'][$this->getId()] = $this->parse(
            ['ycom_auth_' . $this->authKey(), 'ycom_auth_extern'],
            [
                'url' => $url,
                'name' => I18n::rawMsg('ycom_auth_' . $this->authKey() . '_login'),
            ],
        );
    }

    /**
     * Reports a failure: as a field warning always, plus a redirect when the field is a direct link
     * and therefore has no form to show the warning in.
     */
    protected function fail(string $messageKey): void
    {
        $message = '' !== (string) $this->getElement(2) ? (string) $this->getElement(2) : I18n::msg($messageKey);

        $this->params['warning'][$this->getId()] = $this->params['error_class'];
        $this->params['warning_messages'][$this->getId()] = $message;

        // REDAXO 5 dumped the internal message to the page in debug mode. It goes to the log instead:
        // the failure path is reachable by an unauthenticated visitor.
        Logger::factory()->log(E_WARNING, 'ycom ' . $this->authKey() . ' auth failed: ' . $messageKey);

        if ($this->directLink) {
            Response::sendCacheControl();
            Response::sendRedirect(Url::article((int) Config::get('article_id_jump_not_ok')));
        }
    }

    /**
     * Logs the identity in: matches an existing ycom user by email, creates one if there is none.
     *
     * @param array<string, mixed> $userdata the provider's attribute set
     */
    protected function createOrUpdateUser(array $userdata, string $returnTo): void
    {
        $data = $this->mapAttributes($userdata);

        $data = Extension::dispatch(new ExtensionPoint('YCOM_AUTH_MATCHING', $data, [
            'Userdata' => $userdata,
            'AuthType' => $this->authKey(),
        ]));
        $data = is_array($data) ? $data : [];

        if ('' === (string) ($data['email'] ?? '')) {
            $this->fail('ycom_auth_error_no_email');

            return;
        }

        $this->clearAuthSession();

        // ----- an active user with that email: log them in and refresh their profile
        $loginStatus = Auth::login([
            'loginName' => (string) $data['email'],
            'loginPassword' => '',
            'ignorePassword' => true,
            'loginStay' => true,
            'filter' => 'status > 0',
        ]);

        if (Auth::STATUS_HAS_LOGGED_IN === $loginStatus) {
            User::updateUser($data);
            Response::sendCacheControl();
            Response::sendRedirect($returnTo);
        }

        // ----- the email exists but the account is not active: do not silently create a second one
        if (null !== User::query()->where('email', $data['email'])->findOne()) {
            $this->fail('ycom_auth_error_login_failed');

            return;
        }

        // ----- new identity
        $user = User::createUserByEmail($data);

        if (null === $user || 0 < count($user->getMessages())) {
            $this->fail('ycom_auth_error_create_user');

            return;
        }

        $loginStatus = Auth::login([
            'loginName' => (string) $user->getValue('email'),
            'loginPassword' => '',
            'ignorePassword' => true,
            'loginStay' => false,
            'filter' => [],
        ]);

        if (Auth::STATUS_HAS_LOGGED_IN !== $loginStatus) {
            $this->fail('ycom_auth_error_login_created_user');

            return;
        }

        Response::sendCacheControl();
        Response::sendRedirect($returnTo);
    }

    /**
     * Turns the provider's attribute set into ycom columns, then applies the field's default values.
     *
     * @param array<string, mixed> $userdata
     * @return array<string, mixed>
     */
    private function mapAttributes(array $userdata): array
    {
        $data = [];

        foreach (self::ATTRIBUTE_MAP as $column => $candidates) {
            $data[$column] = '';

            foreach ($candidates as $candidate) {
                if (!isset($userdata[$candidate])) {
                    continue;
                }

                $value = $userdata[$candidate];
                $data[$column] = is_array($value) ? implode(' ', $value) : (string) $value;
            }
        }

        foreach ($this->getDefaultUserAttributes() as $key => $value) {
            $data[(string) $key] = $value;
        }

        return $data;
    }

    /**
     * The field's `default Userdata as Json` element — values every identity from this provider gets,
     * e.g. `{"ycom_groups": 3, "termsofuse_accepted": 1}`.
     *
     * @return array<string, mixed>
     */
    private function getDefaultUserAttributes(): array
    {
        $json = (string) $this->getElement(4);

        if ('' === $json) {
            return [];
        }

        $decoded = json_decode($json, true);

        if (!is_array($decoded)) {
            throw new RuntimeException(sprintf(
                '%s: the default user attributes are not valid JSON [%s]',
                $this->authKey(),
                $json,
            ));
        }

        return $decoded;
    }

    protected function clearAuthSession(): void
    {
        foreach ($this->sessionVars() as $key) {
            Auth::unsetSessionVar($key);
        }
    }

    /** Whether this request addresses this provider, and with which function. */
    protected function getRequestedFunction(array $functions, string $fallback): ?string
    {
        $mode = Request::request('rex_ycom_auth_mode', 'string', '');
        $function = Request::request('rex_ycom_auth_func', 'string', '');

        if ($this->authKey() === $mode && in_array($function, $functions, true)) {
            return $function;
        }

        return $this->directLink ? $fallback : null;
    }

    /** The URL that starts this provider's flow, for the login link on the page. */
    protected function getFlowUrl(string $function, string $returnTo): string
    {
        return Url::article(null, null, [
            'rex_ycom_auth_mode' => $this->authKey(),
            'rex_ycom_auth_func' => $function,
            'returnTo' => $returnTo,
        ]);
    }

    /**
     * The same URL, but guaranteed absolute — what an identity provider has to be given.
     *
     * @param array<string, mixed> $params
     */
    protected function getAbsoluteFlowUrl(string $function, array $params = []): string
    {
        return $this->toAbsoluteUrl(YRewrite::getFullUrlByArticleId(Article::getCurrentId(), null, [
            'rex_ycom_auth_mode' => $this->authKey(),
            'rex_ycom_auth_func' => $function,
        ] + $params, '&'));
    }

    /**
     * Makes a yrewrite URL absolute.
     *
     * `YRewrite::getFullUrlByArticleId()` returns a *relative* path for the default domain — it only
     * prepends the host for the non-default ones. That is long-standing yrewrite behaviour (REDAXO 5
     * did the same), but SAML endpoints, the SAML RelayState and an OAuth2 redirect URI all have to
     * be absolute, so it cannot be relied on here.
     */
    protected function toAbsoluteUrl(string $url): string
    {
        if (preg_match('@^https?://@i', $url)) {
            return $url;
        }

        // getFullPath() already ends in the subfolder separator.
        return YRewrite::getFullPath(ltrim($url, '/'));
    }

    protected function isDebug(): bool
    {
        return (bool) ($this->params['debug'] ?? false);
    }
}
