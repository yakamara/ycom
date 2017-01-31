<?php

class rex_ycom_auth
{

    static $debug = false;
    static $me = NULL;
    static $perms = [
        '0' => 'translate:ycom_perm_extends',
        '1' => 'translate:ycom_perm_only_logged_in',
        '2' => 'translate:ycom_perm_only_not_logged_in',
        '3' => 'translate:ycom_perm_all'
    ];

    //
    static function init()
    {

        $loginName = rex_request(rex_config::get('ycom', 'auth_request_name'), 'string');
        $loginPassword = rex_request(rex_config::get('ycom', 'auth_request_psw'), 'string');
        $loginStay = rex_request(rex_config::get('ycom', 'auth_request_stay'), 'string');
        $referer = rex_request(rex_config::get('ycom', 'auth_request_ref'), 'string');
        $logout = rex_request(rex_config::get('ycom', 'auth_request_logout'), 'int');

        $redirect = '';

        ## Check for Login / Logout
        /*
          login_status
          0: not logged in
          1: logged in
          2: has logged in
          3: has logged out
          4: login failed
        */
        $login_status = self::login($loginName, $loginPassword, $loginStay, $logout);

        ## set redirect after Login
        if ($login_status == 2) {
            if ($referer) {
                $redirect = urldecode($referer);
            } else {
                $redirect = rex_getUrl(rex_addon::get('ycom')->getConfig('article_id_jump_ok'));
            }
        }

        /*
         * Checking page permissions
         */
        $currentId = rex_article::getCurrentId();
        if ($article = rex_article::get($currentId)) {
            if (!self::checkPerm($article) && !$redirect  && rex_addon::get('ycom')->getConfig('article_id_jump_denied') != rex_article::getCurrentId()) {
                $params = [];

                ## Adding referer only if target is not login_ok Article
                if (rex_addon::get('ycom')->getConfig('article_id_jump_ok') != rex_article::getCurrentId()) {
                    $params = array(rex_addon::get('ycom')->getConfig('auth_request_ref') => urlencode($_SERVER['REQUEST_URI']));
                }
                $redirect = rex_getUrl(rex_addon::get('ycom')->getConfig('article_id_jump_denied'), '', $params, '&');
            }
        }

        if ($login_status == 3 && $redirect == '') {
            $redirect = rex_getUrl(rex_addon::get('ycom')->getConfig('article_id_jump_logout'), '', [], '&');
        }

        if ($login_status == 4 && $redirect == '') {
            $params = [rex_config::get('ycom', 'auth_request_name') => $loginName, rex_config::get('ycom', 'auth_request_ref') => $referer, rex_config::get('ycom', 'auth_request_stay') => $loginStay];
            $redirect = rex_getUrl(rex_addon::get('ycom')->getConfig('article_id_jump_not_ok'), '', $params, '&');
        }

        return $redirect;

    }

    static function login($loginName = '', $loginPassword = '', $loginStay = '', $logout = false, $query_extras = ' and status > 0', $ignorePassword = false)
    {
        rex_login::startSession();

        $loginStatus = 0; // not logged in
        $sessionKey = NULL;
        $sessionUserID = NULL;
        $me = NULL;

        if (isset($_SESSION[self::getLoginKey()])) {
            $sessionUserID = $_SESSION[self::getLoginKey()];
        }

        if (rex_addon::get('ycom')->getConfig('auth_request_stay')) {
            if (isset($_COOKIE[self::getLoginKey()])) {
                $sessionKey = rex_cookie(self::getLoginKey(), 'string');

            }
        }

        if (($loginName && $loginPassword) || $sessionUserID || $sessionKey) {

            // TODO:
            echo 'noch einarbeiten 1';
            var_dump($query_extras);

            $loginUsers = rex_ycom_user::query()
                ->where(rex_addon::get('ycom')->getConfig('login_field'), $loginName)
                ->find();

            if (count($loginUsers) == 1) {

                if ($ignorePassword || self::checkPassword($loginPassword, $loginUsers[0]->getValue('id'))) {
                    $me = $loginUsers[0];

                }

            }

            if (!$me && $sessionUserID) {

                // TODO:
                echo 'noch einarbeiten 2';
                var_dump($query_extras);

                $loginUsers = rex_ycom_user::query()
                    ->where('id', $sessionUserID)
                    ->find();

                if (count($loginUsers) == 1) {
                    $me = $loginUsers[0];

                }
            }

            if ($me) {

                self::setUser($me);
                $loginStatus = 1; // is logged in

                if (rex_addon::get('ycom')->getProperty('auth_request_stay')) {
                    if ($loginStay == 1) {
                        $sessionKey = uniqid ('ycom_user', true);
                        $me->setValue('session_key', $sessionKey);
                    }
                    setcookie(self::getLoginKey(), $sessionKey, time() + (3600 * 24 * rex_addon::get('ycom')->getConfig('cookie_ttl')), '/' );
                }

                $me->setValue('last_action_time', date("Y-m-d H:i:s"));

                if ($loginName) {
                    $loginStatus = 2; // has just logged in
                    $me = rex_extension::registerPoint(new rex_extension_point('YCOM_AUTH_LOGIN_SUCCESS', $me, []));
                    $me->setValue('last_login_time', date("Y-m-d H:i:s"));

                }

                $me = rex_extension::registerPoint(new rex_extension_point('YCOM_AUTH_LOGIN', $me, []));

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
                    'query_extras' => $query_extras
                ]
                ));

            }
        }

        /*
         * Logout process
         */
        if ($logout && isset($me)) {
            $loginStatus = 3;
            rex_extension::registerPoint(new rex_extension_point('YCOM_AUTH_LOGOUT', $me, [] ));
            self::clearUserSession();
        }

        return $loginStatus;

    }

    static function checkPassword($password, $user_id)
    {
        if (trim($password) == '') {
            return false;
        }

        $user = rex_ycom_user::get($user_id);
        if ($user) {
            if ( rex_login::passwordVerify($password, $user->password)) {
                return true;
            }

        }

        return false;

    }

    static function setUser($me)
    {
        rex_login::startSession();
        $_SESSION[self::getLoginKey()] = $me->id;
        self::$me = $me;
    }

    static function getUser()
    {
        return self::$me;

    }

    static function checkPerm(&$article)
    {
        $me = self::getUser();

        if (rex_addon::get('ycom')->getConfig('auth_active') != '1') {
            return true;

        }

        /*
        static $perms = [
            '0' => 'translate:ycom_perm_extends',
            '1' => 'translate:ycom_perm_only_logged_in',
            '2' => 'translate:ycom_perm_only_not_logged_in',
            '3' => 'translate:ycom_perm_all'
        ];*/

        $permType = (int) $article->getValue('ycom_auth_type');

        if ($permType == 3) {
            return true;

        }

        // 0 - parent perms
        if ($permType < 1) {
            if ($o = $article->getParent()) {
                return self::checkPerm($o);

            }

            // no parent, no perm set -> for all accessible
            return true;
        }

        // 2 - only if not logged in
        if ($permType == 2) {
            if ($me) {
                return false;

            } else {
                return true;

            }

        }


        // 1 - only if logged in .. further group perms
        if ($permType == 1 && !$me) {
            return false;
        }

        // if logged in perms - check group perms

        $article_group_type = (int) $article->getValue('ycom_group_type');

        if ($article_group_type < 1) {
            return true;

        }

        switch ($article_group_type) {

            // user in every group
            case 1:
                $art_groups = explode(',', $article->getValue('ycom_groups'));
                $user_groups = explode(',', $me->ycom_groups);
                foreach ($art_groups as $ag) {
                    if ($ag != '' && !in_array($ag, $user_groups)) {
                        return false;
                    }
                }
                return true;

            // user in at least one group
            case 2:
                $art_groups = explode(',', $article->getValue('ycom_groups'));
                $user_groups = explode(',', $me->ycom_groups);
                foreach ($art_groups as $ag) {
                    if ($ag != '' && in_array($ag, $user_groups)) {
                        return true;
                    }
                }
                return false;

            // user is not in one of the groups
            case 3:
                $user_groups = explode(',', $me->ycom_groups);
                if (count($user_groups) == 0) {
                    return true;

                }
                return false;

            default:
                return false;
        }

        return false;

    }

    /*
     * returns Login-Key used for Sessions and Cookies
     */
    static function getLoginKey()
    {
        return 'rex_ycom';
    }

    static function clearUserSession()
    {
        unset($_SESSION[self::getLoginKey()]);
        unset($_COOKIE[self::getLoginKey()]);
        setcookie(self::getLoginKey(), '0', time() - 3600, '/');
    }

    function deleteUser($id)
    {
        $id = (int) $id;

        $delete = true;
        $delete = rex_register_extension_point('YCOM_AUTH_USER_DELETE', $delete, ['id' => $id]);
        if (!$delete) {
            return false;
        }

        rex_ycom_user::query()->where('id',$id)->find()->delete();

        rex_register_extension_point('YCOM_AUTH_USER_DELETED', '', ['id' => $id]);

        return true;
    }


    static function loginWithParams($params, $query_extras = '')
    {

        // TODO:
        echo 'noch einarbeiten 4';
        var_dump($query_extras);

        $userQuery = rex_ycom_user::query();
        foreach ($params as $l => $v) {
            $userQuery->where($l, $v);
        }

        $Users = $userQuery->find();

        if (count($Users) != 1) {
            return false;
        }

        $user = $Users[0];

        $loginField = rex_config::get('ycom', 'login_field');

        $loginName = $user->$$loginField;
        $loginPassword = $user->password;

        self::login($loginName, $loginPassword, '', false, '', true);

        return self::getUser();

    }

}
