<?php

/**
 * ycom.
 *
 * Saml 2.0 Auth
 *
 * Needs data/addons/project/saml.php data for SP ( ServiceProvider )
 * Dummy here src/addons/ycom/plugins/auth/install/saml.php
 *
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

use OneLogin\Saml2\Auth;
use OneLogin\Saml2\IdPMetadataParser;
use OneLogin\Saml2\Utils;

class rex_yform_value_ycom_auth_saml extends rex_yform_value_abstract
{
    use rex_yform_trait_value_auth_extern;
    private $auth_requestFunctions = ['auth', 'sso', 'acs', 'slo', 'sls'];
    private $auth_directLink = false;
    private $auth_SessionVars = ['SAML_Userdata', 'SAML_NameId', 'SAML_SessionIndex', 'SAML_AuthNRequestID', 'SAML_LogoutRequestID', 'SAML_NameIdFormat', 'SAML_ssoDate'];
    private $auth_ClassKey = 'saml';

    public function enterObject()
    {
        if (rex::isFrontend()) {
            $this->auth_directLink = 1 == $this->getElement(5);
        }

        if (PHP_SESSION_ACTIVE !== session_status()) {
            session_start();
        }

        $settings = $this->auth_loadSettings();
        // load external Metadata if possible
        try {
            $idpSettings = IdPMetadataParser::parseRemoteXML($settings['idp']['entityId']);
            $settings = IdPMetadataParser::injectIntoSettings($settings, $idpSettings);
        } catch (Exception $e) {
        }

        $returnTo = $this->auth_getReturnTo();
        $this->auth_FormOutput(rex_getUrl('', '', ['rex_ycom_auth_mode' => 'saml', 'rex_ycom_auth_func' => 'sso', 'returnTo' => $returnTo]));

        $requestMode = rex_request('rex_ycom_auth_mode', 'string', '');
        $requestFunction = rex_request('rex_ycom_auth_func', 'string', '');
        if (!in_array($requestFunction, $this->auth_requestFunctions, true) || $this->auth_ClassKey != $requestMode) {
            if ($this->auth_directLink) {
                $requestFunction = 'sso';
            } else {
                return '';
            }
        }

        // Auth
        try {
            $auth = new Auth($settings);
        } catch (Exception $e) {
            dump($e);
            dump('Please use following ServiceProvider Settings in your config');
            $sp = [
                'entityid' => rex_yrewrite::getFullUrlByArticleId(rex_article::getCurrentId(), '', [], '&'),
                'assertionConsumerService' => [
                    'url' => rex_yrewrite::getFullUrlByArticleId(rex_article::getCurrentId(), '', ['rex_ycom_auth_mode' => 'saml', 'rex_ycom_auth_func' => 'acs'], '&'),
                ],
                'singleLogoutService' => [
                    'url' => rex_yrewrite::getFullUrlByArticleId(rex_article::getCurrentId(), '', ['rex_ycom_auth_mode' => 'saml', 'rex_ycom_auth_func' => 'slo'], '&'),
                ],
            ];
            dump($sp);
            return '';
        }

        switch ($requestFunction) {
            // init login
            case 'sso':
                $returnToUrl = rex_yrewrite::getFullUrlByArticleId('', '', ['rex_ycom_auth_mode' => 'saml', 'rex_ycom_auth_func' => 'auth', 'returnTo' => $returnTo], '&');
                $ssoBuiltUrl = $auth->login($returnToUrl, [], false, false, true);
                rex_ycom_auth::setSessionVar('SAML_AuthNRequestID', $auth->getLastRequestID());
                rex_ycom_auth::setSessionVar('SAML_ssoDate', date('Y-m-d H:i:s'));
                rex_response::sendRedirect($ssoBuiltUrl);
                break;

            // process login
            case 'acs':
                $requestID = rex_ycom_auth::getSessionVar('SAML_AuthNRequestID', 'string', null);

                try {
                    $auth->processResponse($requestID);
                } catch (Throwable $e) {
                    if ($this->params['debug']) {
                        dump($e);
                    }
                    $this->params['warning_messages'][] = ('' != $this->getElement(2)) ? $this->getElement(2) : '{{ saml.error.acs }}';
                    return '';
                }

                $errors = $auth->getErrors();
                if (!empty($errors) || !$auth->isAuthenticated()) {
                    if ($this->params['debug']) {
                        dump($errors);
                    }
                    $this->params['warning_messages'][] = ('' != $this->getElement(2)) ? $this->getElement(2) : '{{ saml.error.acs }}';
                    return '';
                }

                rex_ycom_auth::setSessionVar('SAML_Userdata', $auth->getAttributes());
                rex_ycom_auth::setSessionVar('SAML_NameId', $auth->getNameId());
                rex_ycom_auth::setSessionVar('SAML_SessionIndex', $auth->getSessionIndex());
                rex_ycom_auth::setSessionVar('SAML_AuthNRequestID', '');

                session_write_close();

                if (isset($_POST['RelayState']) && Utils::getSelfURL() != $_POST['RelayState']) {
                    $auth->redirectTo($_POST['RelayState']);
                }

                rex_redirect(rex_plugin::get('ycom', 'auth')->getConfig('article_id_jump_not_ok'));
                break;

            // init Logout processs with returnTo or redirect from idp
            case 'slo':

                $returnToURL = rex_yrewrite::getFullUrlByArticleId('', '', ['rex_ycom_auth_mode' => 'saml', 'rex_ycom_auth_func' => 'sls', 'returnTo' => $returnTo], '&');
                $parameters = [];

                $nameId = rex_ycom_auth::getSessionVar('SAML_NameId', 'string', null);
                $sessionIndex = rex_ycom_auth::getSessionVar('SAML_SessionIndex', 'string', null);
                $nameIdFormat = rex_ycom_auth::getSessionVar('SAML_NameIdFormat', 'string', null);

                rex_ycom_auth::clearUserSession();
                self::auth_clearUserSession();

                $sloBuiltUrl = $auth->logout($returnToURL, $parameters, $nameId, $sessionIndex, true, $nameIdFormat);
                rex_ycom_auth::setSessionVar('SAML_LogoutRequestID', $auth->getLastRequestID());

                session_write_close(); // wirklich nötig ?

                rex_response::sendRedirect($sloBuiltUrl);
                break;

            // process Logout without returnTo
            case 'sls':

                $requestID = rex_ycom_auth::getSessionVar('SAML_LogoutRequestID', 'string', null);

                try {
                    $auth->processSLO(false, $requestID); // true => keep local session
                } catch (Throwable $e) {
                    if ($this->params['debug']) {
                        dump($e);
                    }
                    $this->params['warning_messages'][] = ('' != $this->getElement(2)) ? $this->getElement(2) : '{{ saml.error.acs }}';
                    return '';
                }

                rex_ycom_auth::clearUserSession();
                self::auth_clearUserSession();

                // returnTo setzen auf LogoutSeite
                $errors = $auth->getErrors();
                if (empty($errors)) {
                    // hier wird davon aufgegangen, dass immer ein returnTo gesetzt ist.
                    // \rex_yrewrite::getFullUrlByArticleId(\rex_plugin::get('ycom', 'auth')->getConfig('article_id_jump_logout'));
                    rex_response::sendRedirect($returnTo);
                } else {
                    if ($this->params['debug']) {
                        dump($errors);
                    }
                    $this->params['warning_messages'][] = ('' != $this->getElement(2)) ? $this->getElement(2) : '{{ saml.error.sls }}';
                    return '';
                }
                break;
        }

        if (0 == count(rex_ycom_auth::getSessionVar('SAML_Userdata', 'array', []))) {
            // direkt durchschleifen zu SAML AUTH .. Hier könnte man auch eine abfrage machen.
            rex_response::sendRedirect(rex_getUrl('', '', ['rex_ycom_auth_mode' => 'saml', 'rex_ycom_auth_func' => 'sso', 'returnTo' => $returnTo], '&'));
            exit;
        }

        $Userdata = rex_ycom_auth::getSessionVar('SAML_Userdata', 'array', []);

        return $this->auth_createOrUpdateYComUser($Userdata, $returnTo);
    }

    public function getDescription()
    {
        return 'ycom_auth_saml|label|error_msg|[allowed returnTo domains: DomainA,DomainB]|default Userdata as Json{"ycom_groups": 3, "termsofuse_accepted": 1}|direct_link 0,1';
    }
}
