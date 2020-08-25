<?php

/**
 * ycom.
 *
 * oauth 2.0 Auth
 *
 * Needs data/addons/project/oauth2.php data for SP ( ServiceProvider )
 * Dummy here src/addons/ycom/plugins/auth/install/oauth2.php
 *
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericProvider;

class rex_yform_value_ycom_auth_oauth2 extends rex_yform_value_abstract
{
    use rex_yform_trait_value_auth_extern;
    private $auth_requestFunctions = ['init', 'code', 'state'];
    private $auth_directLink = false;
    private $auth_SessionVars = ['OAUTH2_oauth2state'];
    private $auth_ClassKey = 'oauth2';

    public function enterObject()
    {
        if (rex::isFrontend()) {
            $this->auth_directLink = 1 == $this->getElement(5) ? true : false;
        }

        if (PHP_SESSION_ACTIVE !== session_status()) {
            session_start();
        }

        $settings = $this->auth_loadSettings();
        $returnTo = $this->auth_getReturnTo();
        $this->auth_FormOutput(rex_getUrl('', '', ['rex_ycom_auth_mode' => 'oauth2', 'rex_ycom_auth_func' => 'init', 'returnTo' => $returnTo]));

        $requestMode = rex_request('rex_ycom_auth_mode', 'string', '');
        $requestFunction = rex_request('rex_ycom_auth_func', 'string', '');
        if (!in_array($requestFunction, $this->auth_requestFunctions, true) || $this->auth_ClassKey != $requestMode) {
            if ($this->auth_directLink) {
                $requestFunction = 'init';
            } else {
                return '';
            }
        }

        if ('' == $settings['redirectUri']) {
            echo 'use this URL for redirect';
            dump(rex_yrewrite::getFullUrlByArticleId(rex_article::getCurrentId(), '', ['rex_ycom_auth_mode' => 'oauth2', 'rex_ycom_auth_func' => 'code'], '&'));
            return '';
        }

        $provider = new GenericProvider($settings);

        $Userdata = [];
        switch ($requestFunction) {
            case 'code':
                $code = rex_request('code', 'string');
                if ('' != $code) {
                    if ('' == rex_ycom_auth::getSessionVar('OAUTH2_oauth2state') || empty($_GET['state']) || $_GET['state'] != rex_ycom_auth::getSessionVar('OAUTH2_oauth2state')) {
                        if ($this->params['debug']) {
                            echo 'OAuth session saved state != OAuth State';
                            dump(rex_ycom_auth::getSessionVar('OAUTH2_oauth2state'));
                            dump($_GET['state']);
                        }
                        $this->auth_redirectToFailed('{{ oauth.error.state_code }}');
                        $this->params['warning_messages'][] = ('' != $this->getElement(2)) ? $this->getElement(2) : '{{ oauth.error.state_code }}';
                        return '';
                    }

                    try {
                        $accessToken = $provider->getAccessToken('authorization_code', [
                            'code' => $code,
                        ]);

                        if ($accessToken->hasExpired()) {
                            $this->auth_redirectToFailed('{{ oauth.error.access_expired }}');
                            $this->params['warning_messages'][] = ('' != $this->getElement(2)) ? $this->getElement(2) : '{{ oauth.error.access_expired }}';
                            return '';
                        }
                        $resourceOwner = $provider->getResourceOwner($accessToken);
                        $Userdata = $resourceOwner->toArray();
                        $returnTo = rex_ycom_auth::getSessionVar('OAUTH2_oauth2returnTo');
                        rex_ycom_auth::unsetSessionVar('OAUTH2_oauth2returnTo');
                    } catch (IdentityProviderException $e) {
                        $this->auth_redirectToFailed('{{ oauth.error.code }}');
                        $this->params['warning_messages'][] = ('' != $this->getElement(2)) ? $this->getElement(2) : '{{ oauth.error.code }}';
                        return '';
                    }
                } else {
                    $this->auth_redirectToFailed('{{ oauth.error.no_code }}');
                    $this->params['warning_messages'][] = ('' != $this->getElement(2)) ? $this->getElement(2) : '{{ oauth.error.no_code }}';
                    return '';
                }
                break;

            case 'init':
                $authorizationUrl = $provider->getAuthorizationUrl();
                rex_ycom_auth::setSessionVar('OAUTH2_oauth2state', $provider->getState());
                rex_ycom_auth::setSessionVar('OAUTH2_oauth2returnTo', $returnTo);
                rex_response::sendRedirect($authorizationUrl);
                exit;
        }

        return $this->auth_createOrUpdateYComUser($Userdata, $returnTo);
    }

    public function getDescription()
    {
        return 'ycom_auth_oauth2|label|error_msg|[allowed returnTo domains: DomainA,DomainB]|default Userdata as Json{"ycom_groups": 3, "termsofuse_accepted": 1}|direct_link 0,1';
    }
}
