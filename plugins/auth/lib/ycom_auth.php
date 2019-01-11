<?php

class rex_ycom_auth
{
    public static $debug = false;
    public static $me = null;
    public static $perms = [
        '0' => 'translate:ycom_perm_extends',
        '1' => 'translate:ycom_perm_only_logged_in',
        '2' => 'translate:ycom_perm_only_not_logged_in',
        '3' => 'translate:ycom_perm_all',
    ];
    public static $DefaultRequestKeys = [
        'auth_request_name' => 'rex_ycom_auth_name',
        'auth_request_psw' => 'rex_ycom_auth_psw',
        'auth_request_stay' => 'rex_ycom_auth_stay',
        'auth_request_ref' => 'rex_ycom_auth_ref',
        'auth_request_logout' => 'rex_ycom_auth_logout',
        'auth_request_id' => 'rex_ycom_auth_id',
    ];

    public static function getRequestKey($requestKey)
    {
        return rex_config::get('ycom', $requestKey, self::$DefaultRequestKeys[$requestKey]);
    }

    public static function init()
    {
        $params = [];
        $params['loginName'] = rex_request(self::getRequestKey('auth_request_name'), 'string');
        $params['loginPassword'] = rex_request(self::getRequestKey('auth_request_psw'), 'string');
        $params['loginStay'] = rex_request(self::getRequestKey('auth_request_stay'), 'string');
        $params['referer'] = rex_request(self::getRequestKey('auth_request_ref'), 'string');
        $params['logout'] = rex_request(self::getRequestKey('auth_request_logout'), 'int');
        $params['redirect'] = '';

        $params['referer'] = self::cleanReferer($params['referer']);

        //# Check for Login / Logout
        /*
          login_status
          0: not logged in
          1: logged in
          2: has logged in
          3: has logged out
          4: login failed
        */
        $params['filter'] = ['status > 0'];
        $login_status = self::login($params);

        //# set redirect after Login
        if ($login_status == 2) {
            if ($params['referer']) {
                $params['redirect'] = urldecode($params['referer']);
            } else {
                $params['redirect'] = rex_getUrl(rex_plugin::get('ycom', 'auth')->getConfig('article_id_jump_ok'));
            }
        }

        /*
         * Checking page permissions
         */
        $currentId = rex_article::getCurrentId();
        if ($article = rex_article::get($currentId)) {
            if (!self::checkPerm($article) && !$params['redirect'] && rex_plugin::get('ycom', 'auth')->getConfig('article_id_jump_denied') != rex_article::getCurrentId()) {
                $params = [];

                $ignoreRefArticles = [];
                $ignoreRefArticles[] = rex_plugin::get('ycom', 'auth')->getConfig('article_id_jump_logout');
                $ignoreRefArticles[] = rex_plugin::get('ycom', 'auth')->getConfig('article_id_jump_ok');

                if (!in_array(rex_article::getCurrentId(), $ignoreRefArticles)) {
                    if (rex_addon::get('yrewrite')->isInstalled()) {
                        $refererURL = rex_yrewrite::getFullUrlByArticleId();
                    } else {
                        $refererURL = $_SERVER['REQUEST_URI'];
                    }
                    $refererURL = self::cleanReferer($refererURL);
                    $params = [self::getRequestKey('auth_request_ref') => urlencode($refererURL)];

                }

                if (!rex_ycom_auth::getUser() && rex_plugin::get('ycom', 'auth')->getConfig('article_id_login') != "") {
                    $params['redirect'] = rex_getUrl(rex_plugin::get('ycom', 'auth')->getConfig('article_id_login'), '', $params, '&');

                } else {
                    $params['redirect'] = rex_getUrl(rex_plugin::get('ycom', 'auth')->getConfig('article_id_jump_denied'), '', $params, '&');

                }
            }
        }

        if ($login_status == 3 && $params['redirect'] == '') {
            $params['redirect'] = rex_getUrl(rex_plugin::get('ycom', 'auth')->getConfig('article_id_jump_logout'), '', [], '&');
        }

        if ($login_status == 4 && $params['redirect'] == '') {
            $status_params = [self::getRequestKey('auth_request_name') => $params['loginName'], self::getRequestKey('auth_request_ref') => $params['referer'], self::getRequestKey('auth_request_stay') => $params['loginStay']];
            // $params['redirect'] = rex_getUrl(rex_plugin::get('ycom', 'auth')->getConfig('article_id_jump_not_ok'), '', $status_params, '&');
        }

        $params['loginStatus'] = $login_status;
        $params = rex_extension::registerPoint(new rex_extension_point('YCOM_AUTH_INIT', $params, []));

        if (self::getUser()) {
            $article_id_password = rex_plugin::get('ycom', 'auth')->getConfig('article_id_jump_password');
            $article_id_termofuse = rex_plugin::get('ycom', 'auth')->getConfig('article_id_jump_termofuse');

            if ($article_id_password != '' && self::getUser()->getValue('new_password_required') == 1) {
                if ($article_id_password != rex_article::getCurrentId()) {
                    $params['redirect'] = rex_getUrl($article_id_password, '', [], '&');
                }
            } elseif ($article_id_termofuse != '' && self::getUser()->getValue('termofuse_accepted') != 1) {
                if ($article_id_termofuse != rex_article::getCurrentId()) {
                    $params['redirect'] = rex_getUrl($article_id_termofuse, '', [], '&');
                }
            }
        }

        return $params['redirect'];
    }

    public static function login($params)
    {
        rex_login::startSession();

        $loginStatus = 0; // not logged in
        $sessionKey = null;
        $sessionUserID = null;
        $me = null;

        $filter = null;
        if (isset($params['filter']) && $params['filter'] != '') {
            $filter = function (rex_yform_manager_query $query) use ($params) {
                if (is_array($params['filter'])) {
                    foreach ($params['filter'] as $filter) {
                        $query->whereRaw($filter);
                    }
                } else {
                    $query->whereRaw($params['filter']);
                }
            };
        }

        if (self::getSessionVar(self::getLoginKey(),'string',null)) {
            $sessionUserID = self::getSessionVar(self::getLoginKey(),'string',null);
        }

        if (self::getCookieVar(self::getLoginKey(), 'string', null)) {
            $sessionKey = rex_cookie(self::getLoginKey(), 'string');
        }

        if (
            (!empty($params['loginName']) && ((isset($params['ignorePassword']) && $params['ignorePassword']) || !empty($params['loginPassword'])))
            || $sessionUserID
            || $sessionKey
        ) {
            if (!empty($params['loginName'])) {
                $userQuery =
                    rex_ycom_user::query()
                        ->where(rex_plugin::get('ycom', 'auth')
                            ->getConfig('login_field', 'email'), $params['loginName']);

                if ($filter) {
                    $filter($userQuery);
                }

                $loginUsers = $userQuery->find();

                if (count($loginUsers) == 1) {
                    $user = $loginUsers[0];

                    $auth_rules = new rex_ycom_auth_rules();

                    if (!$auth_rules->check($user, rex_config::get('ycom/auth', 'auth_rule', 'login_try_5_pause'))) {
                    } elseif ((@$params['ignorePassword'] || self::checkPassword($params['loginPassword'], $user->id))) {
                        $me = $user;
                        $me->setValue('login_tries', 0);
                        if (isset($params['loginStay']) && !$params['loginStay']) {
                            $me->setValue('session_key', '');
                        }
                        // session fixation
                        self::regenerateSessionId();
                    } else {
                        ++$user->login_tries;
                        $user->save();
                    }
                }
            }

            if (!$me && $sessionUserID) {
                $userQuery =
                    rex_ycom_user::query()
                        ->where('id', $sessionUserID);

                if ($filter) {
                    $filter($userQuery);
                }

                $loginUsers = $userQuery->find();

                if (count($loginUsers) == 1) {
                    $me = $loginUsers[0];
                }
            }

            if (!$me && $sessionKey) {
                $userQuery =
                    rex_ycom_user::query()
                        ->where('session_key', $sessionKey);

                if ($filter) {
                    $filter($userQuery);
                }

                $loginUsers = $userQuery->find();

                if (count($loginUsers) == 1) {
                    $me = $loginUsers[0];

                    $sessionKey = uniqid('ycom_user', true);
                    $me->setValue('session_key', $sessionKey);

                    self::setCookieVar(self::getLoginKey(), $sessionKey, time() + (3600 * 24 * rex_addon::get('ycom')->getConfig('auth_cookie_ttl', 14)));

                    // session fixation
                    self::regenerateSessionId();
                } else {
                    self::clearUserSession();
                }
            }

            if ($me) {

                self::setUser($me);
                $loginStatus = 1; // is logged in

                if (isset($params['loginStay']) && $params['loginStay']) {
                    $sessionKey = bin2hex(random_bytes(16));
                    $me->setValue('session_key', $sessionKey);
                    self::setCookieVar(self::getLoginKey(), $sessionKey, time() + (3600 * 24 * rex_addon::get('ycom')->getConfig('auth_cookie_ttl', 14)));
                }

                $me->setValue('last_action_time', date('Y-m-d H:i:s'));

                if ($params['loginName']) {
                    $loginStatus = 2; // has just logged in
                    $me = rex_extension::registerPoint(new rex_extension_point('YCOM_AUTH_LOGIN_SUCCESS', $me, []));
                    $me->setValue('last_login_time', date('Y-m-d H:i:s'));
                }

                $me = rex_extension::registerPoint(new rex_extension_point('YCOM_AUTH_LOGIN', $me, []));
                $me->save();

            } else {
                $loginStatus = 0; // not logged in

                if ($params['loginName']) {
                    $loginStatus = 4; // login failed
                }

                $loginStatus = rex_extension::registerPoint(new rex_extension_point('YCOM_AUTH_LOGIN_FAILED', $loginStatus, $params));
            }
        }

        if (isset($params['logout']) && $params['logout'] && isset($me)) {
            $loginStatus = 3;
            rex_extension::registerPoint(new rex_extension_point('YCOM_AUTH_LOGOUT', $me, []));
            unset($me);
            self::clearUserSession();
        }

        return $loginStatus;
    }

    public static function checkPassword($password, $user_id)
    {
        if (trim($password) == '') {
            return false;
        }

        $user = rex_ycom_user::get($user_id);
        if ($user) {
            if (rex_login::passwordVerify($password, $user->password)) {
                return true;
            }
        }

        return false;
    }

    public static function setUser($me)
    {
        rex_login::startSession();
        self::setSessionVar(self::getLoginKey(), $me->id);
        self::$me = $me;
    }

    public static function getUser()
    {
        return self::$me;
    }

    public static function checkPerm(&$article)
    {
        $me = self::getUser();

        if (rex_plugin::get('ycom', 'auth')->getConfig('auth_active') != '1') {
            return true;
        }

        unset($xs);

        /*
        static $perms = [
            '0' => 'translate:ycom_perm_extends',
            '1' => 'translate:ycom_perm_only_logged_in',
            '2' => 'translate:ycom_perm_only_not_logged_in',
            '3' => 'translate:ycom_perm_all'
        ];*/

        $permType = (int) $article->getValue('ycom_auth_type');

        if ($permType == 3) {
            $xs = true;
        }

        // 0 - parent perms
        if (!isset($xs) && $permType < 1) {
            if ($o = $article->getParent()) {
                return self::checkPerm($o);
            }

            // no parent, no perm set -> for all accessible
            $xs = true;
        }

        // 2 - only if not logged in
        if (!isset($xs) && $permType == 2) {
            if ($me) {
                $xs = false;
            } else {
                $xs = true;
            }
        }

        // 1 - only if logged in .. further group perms
        if (!isset($xs) && $permType == 1 && !$me) {
            $xs = false;
        }

        if (!isset($xs)) {
            $xs = true;
        }

        // form here - you are logged in.
        $xs = rex_extension::registerPoint(new rex_extension_point('YCOM_AUTH_USER_CHECK', $xs, [
            'article' => $article,
            'me' => $me,
        ]));

        return $xs;
    }

    /*
     * returns Login-Key used for Sessions and Cookies
     */
    public static function getLoginKey()
    {
        return 'rex_ycom';
    }

    public static function clearUserSession()
    {
        self::setSessionVar(self::getLoginKey(), null);
        self::setCookieVar(self::getLoginKey(), null);
        self::setCookieVar(self::getLoginKey(), '', time() - 3600);
        self::$me = null;
        rex_response::setHeader('Clear-Site-Data', '"cache", "cookies", "storage", "executionContexts"');

    }

    public static function setSessionVar($key, $value)
    {
        rex_set_session($key, $value);
    }

    public static function getSessionVar($key, $varType = 'string', $default = '')
    {
        return rex_session($key, $varType, $default);
    }

    public static function setCookieVar($key, $value, $time = null)
    {
        $sessionConfig = rex::getProperty('session', []);

        if (!$time) {
            $time = time() + 3600;
        }
        setcookie($key, $value, $time, $sessionConfig['frontend']['cookie']['path'], $sessionConfig['frontend']['cookie']['domain']);
        $_COOKIE[$key] = $value;

    }

    public static function getCookieVar($key, $varType = 'string', $default = '')
    {
        return rex_cookie($key, $varType, $default);
    }

    public function deleteUser($id)
    {
        $id = (int) $id;
        rex_ycom_user::query()->where('id', $id)->find()->delete();
        return true;
    }

    public static function loginWithParams($params, callable $filter = null)
    {
        $userQuery = rex_ycom_user::query();
        foreach ($params as $l => $v) {
            $userQuery->where($l, $v);
        }

        if ($filter) {
            $filter($userQuery);
        }

        $Users = $userQuery->find();

        if (count($Users) != 1) {
            return false;
        }

        $user = $Users[0];

        $loginField = rex_config::get('ycom/auth', 'login_field', 'email');

        $params = [];
        $params['loginName'] = $user->$loginField;
        $params['loginPassword'] = $user->password;
        $params['ignorePassword'] = true;

        self::login($params);

        return self::getUser();
    }

    protected static function regenerateSessionId()
    {
        if ('' != session_id()) {
            session_regenerate_id(true);
        }
        $_SESSION['REX_SESSID'] = session_id();
    }

    public static function cleanReferer($url)
    {

        if ($url === '') {
            return '';
        }

        $url = parse_url($url);
        $returnUrl = '';

        if (isset($url['host']) && rex_addon::get('yrewrite')->isInstalled()) {
            $domains = rex_yrewrite::getDomains();
            dump($domains);
            if (array_key_exists($url['host'], $domains)) {
                $returnUrl .= (rex_yrewrite::isHttps() ? 'https://' : 'http://');
                $returnUrl .= $domains[$url['host']]->getHost();
            }
        }

        if (isset($url['path']) && $url['path'] != '') {
            $returnUrl .= $url['path'];
        }

        if (isset($url['query']) && $url['query'] != '') {
            $returnUrl .= '?'. $url['query'];
        }

        $referer_to_logout = strpos($returnUrl, self::getRequestKey('auth_request_logout'));
        if ($referer_to_logout === false) {
        } else {
            $returnUrl = '';
        }

        return $returnUrl;
    }

}
