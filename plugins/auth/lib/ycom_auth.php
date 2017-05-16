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

    public static function init()
    {
        $loginName = rex_request(rex_config::get('ycom', 'auth_request_name'), 'string');
        $loginPassword = rex_request(rex_config::get('ycom', 'auth_request_psw'), 'string');
        $loginStay = rex_request(rex_config::get('ycom', 'auth_request_stay'), 'string');
        $referer = rex_request(rex_config::get('ycom', 'auth_request_ref'), 'string');
        $logout = rex_request(rex_config::get('ycom', 'auth_request_logout'), 'int');

        $redirect = '';

        //# Check for Login / Logout
        /*
          login_status
          0: not logged in
          1: logged in
          2: has logged in
          3: has logged out
          4: login failed
        */
        $login_status = self::login($loginName, $loginPassword, $loginStay, $logout);

        //# set redirect after Login
        if ($login_status == 2) {
            if ($referer) {
                $redirect = urldecode($referer);
            } else {
                $redirect = rex_getUrl(rex_addon::get('ycom')->getPlugin('auth')->getConfig('article_id_jump_ok'));
            }
        }

        /*
         * Checking page permissions
         */
        $currentId = rex_article::getCurrentId();
        if ($article = rex_article::get($currentId)) {
            if (!self::checkPerm($article) && !$redirect && rex_addon::get('ycom')->getPlugin('auth')->getConfig('article_id_jump_denied') != rex_article::getCurrentId()) {
                $params = [];

                //# Adding referer only if target is not login_ok Article
                if (rex_addon::get('ycom')->getPlugin('auth')->getConfig('article_id_jump_ok') != rex_article::getCurrentId()) {
                    $params = [rex_addon::get('ycom')->getPlugin('auth')->getConfig('auth_request_ref') => urlencode($_SERVER['REQUEST_URI'])];
                }
                $redirect = rex_getUrl(rex_addon::get('ycom')->getPlugin('auth')->getConfig('article_id_jump_denied'), '', $params, '&');
            }
        }

        if ($login_status == 3 && $redirect == '') {
            $redirect = rex_getUrl(rex_addon::get('ycom')->getPlugin('auth')->getConfig('article_id_jump_logout'), '', [], '&');
        }

        if ($login_status == 4 && $redirect == '') {
            $params = [rex_config::get('ycom', 'auth_request_name') => $loginName, rex_config::get('ycom', 'auth_request_ref') => $referer, rex_config::get('ycom', 'auth_request_stay') => $loginStay];
            $redirect = rex_getUrl(rex_addon::get('ycom')->getPlugin('auth')->getConfig('article_id_jump_not_ok'), '', $params, '&');
        }

        return $redirect;
    }

    public static function login($loginName = '', $loginPassword = '', $loginStay = '', $logout = false, $filter_query = 'status > 0', $ignorePassword = false)
    {
        rex_login::startSession();

        $loginStatus = 0; // not logged in
        $sessionKey = null;
        $sessionUserID = null;
        $me = null;

        $filter = null;
        if ($filter_query != '') {
            $filter = function (rex_yform_manager_query $query) use ($filter_query) {
                $query->whereRaw($filter_query);
            };
        }

        if (isset($_SESSION[self::getLoginKey()])) {
            $sessionUserID = $_SESSION[self::getLoginKey()];
        }

        if (rex_addon::get('ycom')->getPlugin('auth')->getConfig('auth_request_stay')) {
            if (isset($_COOKIE[self::getLoginKey()])) {
                $sessionKey = rex_cookie(self::getLoginKey(), 'string');
            }
        }

        if (($loginName && $loginPassword) || $sessionUserID || $sessionKey) {
            $userQuery = rex_ycom_user::query()
            ->where(rex_addon::get('ycom')->getPlugin('auth')->getConfig('login_field'), $loginName);

            if ($filter) {
                $filter($userQuery);
            }

            $loginUsers = $userQuery->find();

            if (count($loginUsers) == 1) {
                $user = $loginUsers[0];

                if ($user->login_tries > 10) {
                    ++$user->login_tries;
                    $user->save();
                } elseif ($ignorePassword || self::checkPassword($loginPassword, $user->id)) {
                    $me = $user;
                    $me->setValue('login_tries', 0);
                } else {
                    ++$user->login_tries;
                    $user->save();
                }
            }

            if (!$me && $sessionUserID) {
                $userQuery = rex_ycom_user::query()
                ->where('id', $sessionUserID);

                if ($filter) {
                    $filter($userQuery);
                }

                $loginUsers = $userQuery->find();

                if (count($loginUsers) == 1) {
                    $me = $loginUsers[0];
                }
            }

            if ($me) {
                self::setUser($me);
                $loginStatus = 1; // is logged in

                if (rex_addon::get('ycom')->getProperty('auth_request_stay')) {
                    if ($loginStay == 1) {
                        $sessionKey = uniqid('ycom_user', true);
                        $me->setValue('session_key', $sessionKey);
                    }
                    setcookie(self::getLoginKey(), $sessionKey, time() + (3600 * 24 * rex_addon::get('ycom')->getPlugin('auth')->getConfig('cookie_ttl')), '/');
                }

                $me->setValue('last_action_time', date('Y-m-d H:i:s'));

                if ($loginName) {
                    $loginStatus = 2; // has just logged in
                    $me = rex_extension::registerPoint(new rex_extension_point('YCOM_AUTH_LOGIN_SUCCESS', $me, []));
                    $me->setValue('last_login_time', date('Y-m-d H:i:s'));
                }

                $me = rex_extension::registerPoint(new rex_extension_point('YCOM_AUTH_LOGIN', $me, []));

                // TODO: Passwort wird überschrieben. FEHLER !
                $me->save();
            } else {
                $loginStatus = 0; // not logged in

                if ($loginName) {
                    $loginStatus = 4; // login failed
                }

                $loginStatus = rex_extension::registerPoint(new rex_extension_point('YCOM_AUTH_LOGIN_FAILED', $loginStatus, [
                    'login_name' => $loginName,
                    'login_psw' => $loginPassword,
                    'login_stay' => $loginStay,
                    'logout' => $logout,
                    'filter_query' => $filter_query,
                ]
                ));
            }
        }

        if ($logout && isset($me)) {
            $loginStatus = 3;
            rex_extension::registerPoint(new rex_extension_point('YCOM_AUTH_LOGOUT', $me, []));
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
        $_SESSION[self::getLoginKey()] = $me->id;
        self::$me = $me;
    }

    public static function getUser()
    {
        return self::$me;
    }

    public static function checkPerm(&$article)
    {
        $me = self::getUser();

        if (rex_addon::get('ycom')->getPlugin('auth')->getConfig('auth_active') != '1') {
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
            'me' => $me
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
        unset($_SESSION[self::getLoginKey()]);
        unset($_COOKIE[self::getLoginKey()]);
        setcookie(self::getLoginKey(), '0', time() - 3600, '/');

        self::$me = null;
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

        $loginField = rex_config::get('ycom', 'login_field');

        $loginName = $user->$loginField;
        $loginPassword = $user->password;

        self::login($loginName, $loginPassword, '', false, '', true);

        return self::getUser();
    }
}
