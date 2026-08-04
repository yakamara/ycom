<?php

namespace Yakamara\YCom\YForm\Value;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericProvider;
use Override;
use Redaxo\Core\Core;
use Redaxo\Core\Http\Request;
use Redaxo\Core\Http\Response;
use Redaxo\Core\Log\Logger;
use Redaxo\Core\Security\Login;
use Redaxo\Core\Translation\I18n;
use Throwable;
use Yakamara\YCom\Auth\Auth;
use Yakamara\YForm\Attribute\AsValue;

use function hash_equals;

use function Redaxo\Core\View\escape;

use const E_WARNING;

/**
 * OAuth 2.0 / OpenID-Connect-style login, backed by `league/oauth2-client`'s generic provider.
 *
 * The provider configuration lives in `var/data/addons/ycom/oauth2.php`; `install/oauth2.php` is the
 * template. Leaving `redirectUri` empty makes the field print the URL to register with the provider.
 *
 * `ycom_auth_oauth2|label|error_msg|[allowed returnTo domains]|[default userdata as JSON]|[direct_link 0,1]`
 */
#[AsValue('ycom_auth_oauth2')]
class AuthOAuth2 extends AbstractExternalAuth
{
    /** The steps of the flow, as `rex_ycom_auth_func`. */
    private const array FUNCTIONS = ['init', 'code'];

    private const string SESSION_STATE = 'OAUTH2_oauth2state';
    private const string SESSION_RETURN_TO = 'OAUTH2_oauth2returnTo';

    #[Override]
    protected function authKey(): string
    {
        return 'oauth2';
    }

    #[Override]
    protected function sessionVars(): array
    {
        return [self::SESSION_STATE, self::SESSION_RETURN_TO];
    }

    #[Override]
    public function enterObject()
    {
        if (Core::isFrontend()) {
            $this->directLink = '1' === (string) $this->getElement(5);
        }

        Login::startSession();

        $settings = $this->loadSettings();
        $returnTo = $this->getReturnToUrl();
        $this->renderLoginLink($this->getFlowUrl('init', $returnTo));

        $function = $this->getRequestedFunction(self::FUNCTIONS, 'init');

        if (null === $function) {
            return;
        }

        if ('' === (string) ($settings['redirectUri'] ?? '')) {
            $this->showRedirectUriHint();

            return;
        }

        $provider = new GenericProvider($settings);

        match ($function) {
            'init' => $this->startLogin($provider, $returnTo),
            'code' => $this->processCallback($provider, $returnTo),
            default => null,
        };
    }

    /**
     * Step `init`: sends the visitor to the provider's authorization endpoint.
     */
    private function startLogin(GenericProvider $provider, string $returnTo): never
    {
        $authorizationUrl = $provider->getAuthorizationUrl();

        // The state is the CSRF defence of the authorization-code flow: the provider echoes it back
        // and `code` compares it against this session.
        Auth::setSessionVar(self::SESSION_STATE, $provider->getState());
        Auth::setSessionVar(self::SESSION_RETURN_TO, $returnTo);

        Response::sendCacheControl();
        Response::sendRedirect($authorizationUrl);
    }

    /**
     * Step `code`: exchanges the authorization code for a token and reads the identity.
     */
    private function processCallback(GenericProvider $provider, string $returnTo): void
    {
        $code = Request::get('code', 'string', '');

        if ('' === $code) {
            $this->fail('ycom_auth_oauth2_error_no_code');

            return;
        }

        $expectedState = (string) Auth::getSessionVar(self::SESSION_STATE, 'string', '');
        $receivedState = Request::get('state', 'string', '');

        // hash_equals rather than a plain comparison: the state is a secret this request is
        // authenticating itself with. REDAXO 5 used `!=`.
        if ('' === $expectedState || '' === $receivedState || !hash_equals($expectedState, $receivedState)) {
            Logger::factory()->log(E_WARNING, 'ycom oauth2: state mismatch on the authorization callback');
            $this->clearAuthSession();
            $this->fail('ycom_auth_oauth2_error_state');

            return;
        }

        // A state is single-use — drop it before doing anything else, so a replayed callback fails.
        Auth::unsetSessionVar(self::SESSION_STATE);

        try {
            $accessToken = $provider->getAccessToken('authorization_code', ['code' => $code]);

            if ($accessToken->hasExpired()) {
                $this->fail('ycom_auth_oauth2_error_access_expired');

                return;
            }

            $userdata = $provider->getResourceOwner($accessToken)->toArray();
        } catch (IdentityProviderException $e) {
            Logger::factory()->log(E_WARNING, 'ycom oauth2: the provider rejected the code: ' . $e->getMessage());
            $this->fail('ycom_auth_oauth2_error_code');

            return;
        } catch (Throwable $e) {
            Logger::factory()->log(E_WARNING, 'ycom oauth2: token exchange failed: ' . $e->getMessage());
            $this->fail('ycom_auth_oauth2_error_code');

            return;
        }

        // The provider only knows the redirect URI, not where the visitor came from — that was put
        // into the session by `init`.
        $storedReturnTo = (string) Auth::getSessionVar(self::SESSION_RETURN_TO, 'string', '');
        Auth::unsetSessionVar(self::SESSION_RETURN_TO);

        $this->createOrUpdateUser($userdata, '' !== $storedReturnTo ? $storedReturnTo : $returnTo);
    }

    /**
     * Prints the redirect URI to register with the provider.
     *
     * REDAXO 5 `dump()`ed it, which meant the hint only showed up with the debug addon active and
     * leaked a var-dump into the frontend.
     */
    private function showRedirectUriHint(): void
    {
        $redirectUri = $this->getAbsoluteFlowUrl('code');

        Logger::factory()->log(E_WARNING, 'ycom oauth2: redirectUri is not configured. Use: ' . $redirectUri);

        if (!$this->isDebug()) {
            $this->fail('ycom_auth_oauth2_error_settings');

            return;
        }

        $this->params['form_output'][$this->getId()] = '<div class="alert alert-danger"><p>'
            . escape(I18n::rawMsg('ycom_auth_oauth2_redirect_uri_hint')) . '</p><pre>'
            . escape($redirectUri) . '</pre></div>';
    }

    #[Override]
    public function getDescription(): string
    {
        return 'ycom_auth_oauth2|label|error_msg|[allowed returnTo domains: DomainA,DomainB]|default Userdata as Json{"ycom_groups": 3, "termsofuse_accepted": 1}|direct_link 0,1';
    }
}
