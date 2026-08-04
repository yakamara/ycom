<?php

namespace Yakamara\YCom\YForm\Value;

use OneLogin\Saml2\Auth as SamlAuth;
use OneLogin\Saml2\Constants as SamlConstants;
use OneLogin\Saml2\IdPMetadataParser;
use OneLogin\Saml2\Utils;
use Override;
use Redaxo\Core\Content\Article;
use Redaxo\Core\Core;
use Redaxo\Core\Filesystem\Url;
use Redaxo\Core\Http\Request;
use Redaxo\Core\Http\Response;
use Redaxo\Core\Log\Logger;
use Redaxo\Core\Security\Login;
use Redaxo\Core\Translation\I18n;
use Throwable;
use Yakamara\YCom\Auth\Auth;
use Yakamara\YCom\Config;
use Yakamara\YForm\Attribute\AsValue;
use Yakamara\YRewrite\YRewrite;

use function count;
use function date;
use function implode;
use function libxml_clear_errors;
use function libxml_use_internal_errors;
use function preg_match;
use function session_write_close;

use function Redaxo\Core\View\escape;

use const E_WARNING;

/**
 * SAML 2.0 single sign-on, backed by `onelogin/php-saml`.
 *
 * The service provider configuration lives in `var/data/addons/ycom/saml.php`; `install/saml.php` is
 * the annotated template. Rendering the field without a usable configuration prints the SP settings
 * the identity provider needs, so the two ends can be wired up.
 *
 * `ycom_auth_saml|label|error_msg|[allowed returnTo domains]|[default userdata as JSON]|[direct_link 0,1]`
 */
#[AsValue('ycom_auth_saml')]
class AuthSaml extends AbstractExternalAuth
{
    /** The steps of the flow, as `rex_ycom_auth_func`. */
    private const array FUNCTIONS = ['auth', 'sso', 'acs', 'slo', 'sls'];

    #[Override]
    protected function authKey(): string
    {
        return 'saml';
    }

    #[Override]
    protected function sessionVars(): array
    {
        return [
            'SAML_Userdata',
            'SAML_NameId',
            'SAML_NameIdFormat',
            'SAML_SessionIndex',
            'SAML_AuthNRequestID',
            'SAML_LogoutRequestID',
            'SAML_ssoDate',
        ];
    }

    #[Override]
    public function enterObject()
    {
        if (Core::isFrontend()) {
            $this->directLink = '1' === (string) $this->getElement(5);
        }

        Login::startSession();

        $settings = $this->loadSettings();
        $settings = $this->injectIdpMetadata($settings);

        $returnTo = $this->getReturnToUrl();
        $this->renderLoginLink($this->getFlowUrl('sso', $returnTo));

        $function = $this->getRequestedFunction(self::FUNCTIONS, 'sso');

        if (null === $function) {
            return;
        }

        try {
            $auth = new SamlAuth($settings);
        } catch (Throwable $e) {
            // Almost always an incomplete `sp` section. Show what the IdP has to be told.
            $this->showServiceProviderSettings($e);

            return;
        }

        match ($function) {
            'sso' => $this->startLogin($auth, $returnTo),
            'acs' => $this->processLogin($auth, $returnTo),
            'slo' => $this->startLogout($auth, $returnTo),
            'sls' => $this->processLogout($auth, $returnTo),
            default => null,
        };

        // 'auth' — the step the identity provider sends the browser back to via RelayState.
        $userdata = Auth::getSessionVar('SAML_Userdata', 'array', []);

        if (1 > count($userdata)) {
            // Nothing in the session: start over rather than showing an empty page.
            Response::sendCacheControl();
            Response::sendRedirect($this->getFlowUrl('sso', $returnTo));
        }

        $this->createOrUpdateUser($userdata, $returnTo);
    }

    /**
     * Pulls the IdP's published metadata (certificate, endpoints) into the settings.
     *
     * Two deviations from REDAXO 5, both about trusting that fetch:
     *  - the TLS peer certificate is validated (php-saml defaults to *not* validating). The metadata
     *    carries the signing certificate, so an attacker who can MITM this request can substitute
     *    their own and forge assertions. Set `idp.metadataValidatePeer` to false to opt out.
     *  - a failed fetch is logged instead of swallowed silently, because the result is a confusing
     *    "invalid settings" further down.
     *
     * The URL comes from the project's own config file, never from a request, so php-saml's SSRF
     * warning about third-party metadata URLs does not apply.
     *
     * Without a `metadataUrl` the entityId is used, which is a plain identifier in many setups -
     * only an address that looks like a URL is worth a request. And when the response is not valid
     * XML - a HTML error page, a login redirect, an empty body - libxml would report that as PHP
     * warnings from inside Utils::loadXML() rather than through the exception caught below, so they
     * are collected internally here.
     *
     * @param array<string, mixed> $settings
     * @return array<string, mixed>
     */
    private function injectIdpMetadata(array $settings): array
    {
        $url = (string) ($settings['idp']['metadataUrl'] ?? $settings['idp']['entityId'] ?? '');

        if (1 !== preg_match('@^https?://@i', $url)) {
            return $settings;
        }

        $validatePeer = (bool) ($settings['idp']['metadataValidatePeer'] ?? true);

        $useInternalErrors = libxml_use_internal_errors(true);

        try {
            $idpSettings = IdPMetadataParser::parseRemoteXML(
                $url,
                null,
                null,
                SamlConstants::BINDING_HTTP_REDIRECT,
                SamlConstants::BINDING_HTTP_REDIRECT,
                $validatePeer,
            );

            return IdPMetadataParser::injectIntoSettings($settings, $idpSettings);
        } catch (Throwable $e) {
            Logger::factory()->log(E_WARNING, 'ycom saml: could not read the IdP metadata from ' . $url . ': ' . $e->getMessage());

            // The static configuration may well be complete on its own.
            return $settings;
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($useInternalErrors);
        }
    }

    /**
     * Step `sso`: builds the AuthnRequest and sends the browser to the identity provider.
     */
    private function startLogin(SamlAuth $auth, string $returnTo): never
    {
        // RelayState: where the IdP sends the browser back to. The 'auth' step picks the identity up
        // out of the session from there.
        $relayState = $this->getAbsoluteFlowUrl('auth', ['returnTo' => $returnTo]);

        $ssoUrl = (string) $auth->login($relayState, [], false, false, true);

        Auth::setSessionVar('SAML_AuthNRequestID', $auth->getLastRequestID());
        Auth::setSessionVar('SAML_ssoDate', date('Y-m-d H:i:s'));

        Response::sendCacheControl();
        Response::sendRedirect($ssoUrl);
    }

    /**
     * Step `acs`: consumes the IdP's assertion.
     */
    private function processLogin(SamlAuth $auth, string $returnTo): never
    {
        $requestId = Auth::getSessionVar('SAML_AuthNRequestID', 'string', null);

        try {
            $auth->processResponse($requestId);
        } catch (Throwable $e) {
            Logger::factory()->log(E_WARNING, 'ycom saml ACS error: ' . $e->getMessage());
            $this->failAndStop('ycom_auth_saml_error_acs', $returnTo);
        }

        $errors = $auth->getErrors();

        if (0 < count($errors) || !$auth->isAuthenticated()) {
            Logger::factory()->log(E_WARNING, 'ycom saml ACS error: ' . implode(', ', $errors));
            $this->failAndStop('ycom_auth_saml_error_acs', $returnTo);
        }

        Auth::setSessionVar('SAML_Userdata', $auth->getAttributes());
        Auth::setSessionVar('SAML_NameId', $auth->getNameId());
        Auth::setSessionVar('SAML_NameIdFormat', $auth->getNameIdFormat());
        Auth::setSessionVar('SAML_SessionIndex', $auth->getSessionIndex());
        Auth::setSessionVar('SAML_AuthNRequestID', '');

        // The IdP's response arrives on a fresh request; hand the session over before redirecting.
        session_write_close();

        Response::sendCacheControl();

        // RelayState is echoed back by the identity provider, i.e. it is attacker-influenced.
        // REDAXO 5 passed it straight to Auth::redirectTo() — an open redirect. Run it through
        // ycom's own allow-list, exactly like a `returnTo`.
        $relayState = Request::post('RelayState', 'string', '');

        if ('' !== $relayState && Utils::getSelfURL() !== $relayState) {
            $target = Auth::getReturnTo([$relayState], $this->getAllowedDomains());

            if ('' !== $target) {
                Response::sendRedirect($target);
            }

            Logger::factory()->log(E_WARNING, 'ycom saml: RelayState points outside the allowed domains, ignoring it');
        }

        // No usable RelayState: continue on this request instead of bouncing the browser.
        $this->createOrUpdateUser(Auth::getSessionVar('SAML_Userdata', 'array', []), $returnTo);

        Response::sendRedirect($returnTo);
    }

    /**
     * Step `slo`: asks the identity provider to end the session everywhere.
     */
    private function startLogout(SamlAuth $auth, string $returnTo): never
    {
        $sloReturnTo = $this->getAbsoluteFlowUrl('sls', ['returnTo' => $returnTo]);

        $nameId = Auth::getSessionVar('SAML_NameId', 'string', null);
        $sessionIndex = Auth::getSessionVar('SAML_SessionIndex', 'string', null);
        $nameIdFormat = Auth::getSessionVar('SAML_NameIdFormat', 'string', null);

        Auth::clearUserSession();
        $this->clearAuthSession();

        $sloUrl = (string) $auth->logout($sloReturnTo, [], $nameId, $sessionIndex, true, $nameIdFormat);

        Auth::setSessionVar('SAML_LogoutRequestID', $auth->getLastRequestID());
        session_write_close();

        Response::sendCacheControl();
        Response::sendRedirect($sloUrl);
    }

    /**
     * Step `sls`: consumes the IdP's logout response.
     */
    private function processLogout(SamlAuth $auth, string $returnTo): never
    {
        $requestId = Auth::getSessionVar('SAML_LogoutRequestID', 'string', null);

        try {
            $auth->processSLO(false, $requestId, false, function (): void {
                Auth::clearUserSession();
                $this->clearAuthSession();
            });
        } catch (Throwable $e) {
            Logger::factory()->log(E_WARNING, 'ycom saml SLS error: ' . $e->getMessage());
            $this->failAndStop('ycom_auth_saml_error_sls', $returnTo);
        }

        Auth::clearUserSession();
        $this->clearAuthSession();

        $errors = $auth->getErrors();

        if (0 < count($errors)) {
            Logger::factory()->log(E_WARNING, 'ycom saml SLS error: ' . implode(', ', $errors));
            $this->failAndStop('ycom_auth_saml_error_sls', $returnTo);
        }

        Response::sendCacheControl();
        Response::sendRedirect($returnTo);
    }

    /**
     * Reports a failure and ends the request.
     *
     * The SAML steps run on requests the identity provider redirected the browser into — there is no
     * form on screen that could show a field warning, so the visitor has to be sent somewhere.
     */
    private function failAndStop(string $messageKey, string $returnTo): never
    {
        $this->fail($messageKey);

        // Url::article(0) resolves to the *current* article, which would loop straight back into
        // this step — fall back on the returnTo target when no failure article is configured.
        $failArticleId = (int) Config::get('article_id_jump_not_ok');
        $target = 0 < $failArticleId ? Url::article($failArticleId) : $returnTo;

        Response::sendCacheControl();
        Response::sendRedirect($target);
    }

    /**
     * Prints the service provider settings the identity provider has to be configured with.
     */
    private function showServiceProviderSettings(Throwable $e): void
    {
        Logger::factory()->log(E_WARNING, 'ycom saml: invalid settings: ' . $e->getMessage());

        if (!$this->isDebug()) {
            $this->fail('ycom_auth_saml_error_settings');

            return;
        }

        $lines = [
            'entityId: ' . $this->toAbsoluteUrl(YRewrite::getFullUrlByArticleId(Article::getCurrentId(), null, [], '&')),
            'assertionConsumerService.url: ' . $this->getAbsoluteFlowUrl('acs'),
            'singleLogoutService.url: ' . $this->getAbsoluteFlowUrl('slo'),
        ];

        $this->params['form_output'][$this->getId()] = '<div class="alert alert-danger"><p><strong>'
            . escape($e->getMessage()) . '</strong></p><p>'
            . escape(I18n::rawMsg('ycom_auth_saml_sp_settings_hint')) . '</p><pre>'
            . escape(implode("\n", $lines)) . '</pre></div>';
    }

    #[Override]
    public function getDescription(): string
    {
        return 'ycom_auth_saml|label|error_msg|[allowed returnTo domains: DomainA,DomainB]|default Userdata as Json{"ycom_groups": 3, "termsofuse_accepted": 1}|direct_link 0,1';
    }
}
