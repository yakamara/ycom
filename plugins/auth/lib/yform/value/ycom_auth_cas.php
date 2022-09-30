<?php

/**
 * ycom.
 *
 * CAS Auth
 *
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_yform_value_ycom_auth_cas extends rex_yform_value_abstract
{
    /**
     * @var array|string[]
     */
    private static array $requestAuthFunctions = ['auth', 'logout'];
    private string $casFile = 'cas.php';

    public function enterObject(): void
    {
        if (PHP_SESSION_ACTIVE !== session_status()) {
            session_start();
        }

        $casConfigPath = \rex_addon::get('ycom')->getDataPath($this->casFile);
        if (!file_exists($casConfigPath)) {
            throw new rex_exception('CAS Settings file not found ['.$casConfigPath.']');
        }

        $settings = [];
        $settings['debug'] = false;
        $settings['debugPath'] = rex_path::log('ycom_auth_cas.log');
        $settings['idp'] = [];
        $settings['idp']['protocol'] = 'https://';
        $settings['idp']['ServerVersion'] = '2.0';
        $settings['idp']['host'] = '';
        $settings['idp']['port'] = 443;
        $settings['idp']['uri'] = '/cas';
        $settings['idp']['CasServerValidation'] = false;
        $settings['idp']['CasServerCACertPath'] = rex_addon::get('ycom')->getDataPath('cas_cert.pem');

        include $casConfigPath;

        $returnTos = [];
        $returnTos[] = rex_request('returnTo', 'string', ''); // wenn returnTo übergeben wurde, diesen nehmen
        $returnTos[] = rex_getUrl(rex_config::get('ycom/auth', 'article_id_jump_ok'), '', [], '&'); // Auth Ok -> article_id_jump_ok / Current Language will be selected
        $returnTo = rex_ycom_auth::getReturnTo($returnTos, ('' == $this->getElement(3)) ? [] : explode(',', $this->getElement(3)));

        $requestAuthMode = rex_request('rex_ycom_auth_mode', 'string', '');
        $requestAuthFunctions = rex_request('rex_ycom_auth_func', 'string', '');
        if ($this->needsOutput()) {
            $this->params['form_output'][$this->getId()] = $this->parse(['value.ycom_auth_cas.tpl.php', 'value.ycom_auth_saml.tpl.php'], [
                'url' => rex_getUrl('', '', ['rex_ycom_auth_mode' => 'cas', 'rex_ycom_auth_func' => 'auth', 'returnTo' => $returnTo]),
                'name' => '{{ cas_auth }}',
            ]);
        }
        if (!in_array($requestAuthFunctions, self::$requestAuthFunctions, true) || 'cas' != $requestAuthMode) {
            return;
        }

        /** @psalm-suppress TypeDoesNotContainType */
        if ($settings['debug']) { // @phpstan-ignore-line
            phpCAS::setVerbose(true);
        }

        phpCAS::client($settings['idp']['ServerVersion'], $settings['idp']['host'], $settings['idp']['port'], $settings['idp']['uri'], false);

        /** @psalm-suppress RedundantCondition */
        if ($settings['idp']['CasServerValidation']) { // @phpstan-ignore-line
            phpCAS::setCasServerCACert($settings['idp']['CasServerCACertPath']);
        } else {
            phpCAS::setNoCasServerValidation();
        }

        // ----- logout
        if ('logout' == $requestAuthFunctions) {
            $logoutUrl = rex_yrewrite::getFullUrlByArticleId(rex_plugin::get('ycom', 'auth')->getConfig('article_id_logout'), '', [], '&');
            phpCAS::logoutWithRedirectService($logoutUrl);
            exit;
        }

        // ----- auth
        try {
            phpCAS::forceAuthentication();
        } catch (Exception $e) {
            dump($e);
            exit;
        }

        $email = phpCAS::getUser();

        if (empty($email)) {
            $this->params['warning_messages'][] = ('' != $this->getElement(2)) ? $this->getElement(2) : '{{ saml.error.ycom_login_failed }}';
            return;
        }

        // ----- create, get user

        $data = [];
        $data['email'] = $email;

        if ('' != $this->getElement(4)) {
            foreach (json_decode($this->getElement(4), true) as $defaultUserAttributeKey => $defaultUserAttributeValue) {
                $data[$defaultUserAttributeKey] = $defaultUserAttributeValue;
            }
        }

        $data = rex_extension::registerPoint(new rex_extension_point('YCOM_AUTH_CAS_MATCHING', $data, []));

        // not logged in - check if available
        $params = [];
        $params['loginName'] = $data['email'];
        $params['loginStay'] = true;
        $params['filter'] = 'status > 0';
        $params['ignorePassword'] = true;

        $loginStatus = \rex_ycom_auth::login($params);
        if (2 == $loginStatus) {
            // already logged in
            rex_ycom_user::updateUser($data);
            rex_response::sendCacheControl();
            \rex_response::sendRedirect($returnTo);
        }

        // if user not found, check if exists, but no permission
        $user = \rex_ycom_user::query()->where('email', $data['email'])->findOne();
        if ($user) {
            $this->params['warning_messages'][] = ('' != $this->getElement(2)) ? $this->getElement(2) : '{{ saml.error.ycom_login_failed }}';
            return;
        }

        $user = rex_ycom_user::createUserByEmail($data);
        if (!$user || count($user->getMessages()) > 0) {
            if ($user && $this->params['debug']) {
                dump($user->getMessages());
            }
            $this->params['warning_messages'][] = ('' != $this->getElement(2)) ? $this->getElement(2) : '{{ saml.error.ycom_create_user }}';
            return;
        }

        $params = [];
        $params['loginName'] = $user->getValue('email');
        $params['ignorePassword'] = true;
        $loginStatus = \rex_ycom_auth::login($params);

        if (2 != $loginStatus) {
            if ($this->params['debug']) {
                dump($loginStatus);
                dump($user);
            }
            $this->params['warning_messages'][] = ('' != $this->getElement(2)) ? $this->getElement(2) : '{{ saml.error.ycom_login_created_user }}';
            return;
        }

        rex_response::sendCacheControl();
        rex_response::sendRedirect($returnTo);
    }

    public function getDescription(): string
    {
        return 'ycom_auth_cas|label|error_msg|[allowed returnTo domains: DomainA,DomainB]|[default Userdata as Json{"ycom_groups": 3, "termsofuse_accepted": 1}]';
    }

}
