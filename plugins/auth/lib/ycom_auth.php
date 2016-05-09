<?php

class rex_ycom_auth
{

    static $debug = false;
    static $user = NULL;
    static $perms = [
        '0' => 'translate:ycom_perm_extends',
        '1' => 'translate:ycom_perm_only_logged_in',
        '2' => 'translate:ycom_perm_only_not_logged_in',
        '3' => 'translate:ycom_perm_all'
    ];

    //
    static function init(){

        $login_name = rex_request(rex_config::get('ycom', 'auth_request_name'), "string");
        $login_psw = rex_request(rex_config::get('ycom', 'auth_request_psw'), "string");
        $login_stay = rex_request(rex_config::get('ycom', 'auth_request_stay'), "string");
        $referer = rex_request(rex_config::get('ycom', 'auth_request_ref'), "string");
        $logout = rex_request(rex_config::get('ycom', 'auth_request_logout'), "int");

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
        $login_status = self::login($login_name, $login_psw, $login_stay, $logout);

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
            if(!self::checkPerm($article) && !$redirect  && rex_addon::get('ycom')->getConfig('article_id_jump_denied') != rex_article::getCurrentId()) {
                $params = [];

                ## Adding referer only if target is not login_ok Article
                if(rex_addon::get('ycom')->getConfig('article_id_jump_ok') != rex_article::getCurrentId()) {
                    $params = array(rex_addon::get('ycom')->getConfig('auth_request_ref') => urlencode($_SERVER['REQUEST_URI']));
                }
                $redirect = rex_getUrl(rex_addon::get('ycom')->getConfig('article_id_jump_denied'), '', $params, '&');
            }
        }

        if ($login_status == 3 && $redirect == '') {
            $redirect = rex_getUrl(rex_addon::get('ycom')->getConfig('article_id_jump_logout'), '', [], '&');
        }

        if ($login_status == 4 && $redirect == '') {
            $params = [rex_config::get('ycom', 'auth_request_name') => $login_name, rex_config::get('ycom', 'auth_request_ref') => $referer, rex_config::get('ycom', 'auth_request_stay') => $login_stay];
            $redirect = rex_getUrl(rex_addon::get('ycom')->getConfig('article_id_jump_not_ok'), '', $params, '&');
        }

        return $redirect;

    }

    static function login($login_name = "", $login_psw = "", $login_stay = "", $logout = false, $query_extras = ' and status > 0', $ignore_password = false)
    {
        rex_login::startSession();

        $login_status = 0; // not logged in
        $session_key = NULL;
        $user_session = NULL;

        if(isset($_SESSION[self::getLoginKey()])) {
            $user_session = $_SESSION[self::getLoginKey()];
        }

        if(rex_addon::get('ycom')->getConfig('auth_request_stay')) {
            if(isset($_COOKIE[self::getLoginKey()])) {
                $session_key = rex_cookie(self::getLoginKey(), 'string');

            }
        }

        if (($login_name && $login_psw) || !empty($user_session) || !empty($session_key)) {

            $login_success = false;

            $user = rex_sql::factory();
            if (self::$debug) {
                $user->setDebug();
            }

            $user->setQuery('select * from '.rex_ycom_user::getTable().' where '. $user->escapeIdentifier(rex_addon::get('ycom')->getConfig('login_field')).' = '.$user->escape($login_name).' '.$query_extras);
            if ($user->getRows() == 1) {

                if ($ignore_password || self::checkPassword($login_psw, $user->getValue('id'))){
                    $login_success = true;

                }

            }

            // check for session
            if (!$login_success && !empty($user_session)){
                $user->setQuery('select * from '.rex_ycom_user::getTable().' where `id` = ' . $user->escape($user_session) . ' '.$query_extras);
                if ($user->getRows() == 1 ){
                    $login_success = true;
                }
            }

            if ($login_success) {

                self::setUser($user);
                $login_status = 1; // is logged in

                if (rex_addon::get('ycom')->getProperty('auth_request_stay')) {
                    if ($login_stay == 1) {
                        ## creating new Session-Key and write to dbase
                        $session_key = sha1($user->getValue('id').$user->getValue('firstname').$user->getValue('name').time().rand(0,1000));
                        $sql = rex_sql::factory();
                        $sql->setQuery('update '.rex_ycom_user::getTable().' set session_key = ? where id = ? ', [$session_key, $user->getValue('id')]);
                    }
                    setcookie(self::getLoginKey(), $session_key, time() + (3600 * 24 * rex_addon::get('ycom')->getConfig('cookie_ttl')), "/" );
                }

                // track last_action_time
                $u = rex_sql::factory();
                $u->setQuery('update '.rex_ycom_user::getTable().' set last_action_time = ? where id = ? ', [date("U"), $user->getValue('id')]);

                if ($login_name) {
                    $login_status = 2; // has just logged in
                    $user = rex_extension::registerPoint(new rex_extension_point('YCOM_AUTH_LOGIN_SUCESS', $user, array('id' => $user->getValue('id'), 'login' => $user->getValue(rex_addon::get('ycom')->getConfig('login_field')))));

                    // track last_login_date
                    $u = rex_sql::factory();
                    $u->setQuery('update '.rex_ycom_user::getTable().' set last_login_time = ? where id = ?', [date("U"), $user->getValue('id')]);

                }

                $user = rex_extension::registerPoint(new rex_extension_point('YCOM_AUTH_LOGIN', $user, array('id' => $user->getValue('id'), 'login' => $user->getValue(rex_addon::get('ycom')->getConfig('login_field')))));

                // Success Authentification -> Do Nothing

            } else {

                $login_status = 0; // not logged in

                unset($user);

                if($login_name) {
                    $login_status = 4; // login failed
                }

                $login_status = rex_extension::registerPoint(new rex_extension_point('YCOM_AUTH_LOGIN_FAILED', $login_status, array(
                    'login_name' => $login_name, 'login_psw' => $login_psw, 'login_stay' => $login_stay, 'logout' => $logout, 'query_extras' => $query_extras)));

            }
        }

        /*
         * Logout process
         */
        if($logout && isset($user)) {
            $login_status = 3;

            // -> EP YCOM_USER_LOGOUT
            // Use USER Object or execute functions when user logs out.
            rex_extension::registerPoint(new rex_extension_point('YCOM_AUTH_LOGOUT',$user,array('id' => $user->getValue('id'), 'login' => $user->getValue(rex_addon::get('ycom')->getConfig('login_field')))));

            ## Unset Sessions
            self::clearUserSession();
        }

        //rex_extension::registerPoint('YCOM_AUTH_LOGIN_PROCESS_END','','');
        return $login_status;

    }

    static function checkPassword($password, $user_id){
        if (trim($password) == ""){
            return false;
        }
        $user = rex_sql::factory();
        if (self::$debug) {
            $user->setDebug();
        }
        $user->setQuery('select * from '.rex_ycom_user::getTable().' where `id`= ? and `status` = 1 ', [$user_id]);
        if ($user->getRows() == 1 ) {
            if ( rex_login::passwordVerify($password, $user->getValue('password'))) {
                return true;
            }

        }

        return false;

    }

    static function setUser($user){
        rex_login::startSession();
        $_SESSION[self::getLoginKey()] = $user->getValue('id');
        self::$user = $user;
    }

    static function getUser()
    {
        if(isset(self::$user)){
            return self::$user;

        }
        return false;

    }

    static function checkPerm(&$article)
    {
        $user = self::getUser();

        if(rex_addon::get('ycom')->getConfig('auth_active') != "1") {
            return TRUE;

        }

        /*
        static $perms = [
            '0' => 'translate:ycom_perm_extends',
            '1' => 'translate:ycom_perm_only_logged_in',
            '2' => 'translate:ycom_perm_only_not_logged_in',
            '3' => 'translate:ycom_perm_all'
        ];*/

        $perm_type = (int) $article->getValue('ycom_auth_type');

        if($perm_type == 3){
            return TRUE;

        }

        // 0 - parent perms
        if($perm_type < 1) {
            if ($o = $article->getParent()) {
                return self::checkPerm($o);

            }

            // no parent, no perm set -> for all accessible
            return true;
        }

        // 2 - only if not logged in
        if($perm_type == 2) {
            if ($user == false){
                return TRUE;

            } else {
                return FALSE;

            }

        }

        // 1 - only if logged in .. further group perms
        if($perm_type == 1 && !$user) {
            return FALSE;
        }

        // if logged in perms - check group perms

        /*
        static $perms = [
            '0' => 'translate:ycom_group_forallgroups',
            '1' => 'translate:ycom_group_inallgroups',
            '2' => 'translate:ycom_group_inonegroup',
            '3' => 'translate:ycom_group_nogroups'
        ];
        */

        $article_group_type = (int) $article->getValue('ycom_group_type');

        if($article_group_type < 1) {
            return TRUE;

        }

        switch($article_group_type) {

            // user in every group
            case(1):
                $art_groups = explode(",", $article->getValue('ycom_groups'));
                $user_groups = explode(",", $user->getValue("ycom_groups"));
                foreach($art_groups as $ag) {
                    if($ag != "" && !in_array($ag, $user_groups)) {
                        return FALSE;
                    }
                }
                return TRUE;

            // user in at least one group
            case(2):
                $art_groups = explode(",", $article->getValue('ycom_groups'));
                $user_groups = explode(",", $user->getValue("ycom_groups"));
                foreach($art_groups as $ag) {
                    if($ag != "" && in_array($ag, $user_groups)) {
                        return TRUE;
                    }
                }
                return false;

            // user is not in one of the groups
            case(3):
                $user_groups = explode(",", $user->getValue("ycom_groups"));
                if(count($user_groups) == 0) {
                    return TRUE;

                }
                return false;

            default:
                return false;
        }

        return FALSE;

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
        setcookie(self::getLoginKey(), '0', time() - 3600, "/");
    }

    function deleteUser($id)
    {
        $delete = TRUE;
        $delete = rex_register_extension_point("YCOM_AUTH_USER_DELETE", $delete, array('id' => $id));
        if(!$delete) {
            return FALSE;
        }

        $id = (int) $id;
        $gu = rex_sql::factory();
        $gu->setQuery('delete from '.rex_ycom_user::getTable().' where id = ?', [$id]);

        rex_register_extension_point("YCOM_AUTH_USER_DELETED", "", array('id' => $id));

        return TRUE;
    }


    static function loginWithParams($params, $query_extras = "")
    {

        $u = rex_sql::factory();

        $s = array();
        foreach($params as $l => $v) {
            $s[] = ' '.$u->escapeIdentifier($l).' = '.$u->escape($v).' ';
        }

        if(self::$debug) {
            $u->setDebug();
        }
        $u_array = $u->getArray('select * from '.rex_ycom_user::getTable().' where '.implode(" AND ",$s).' '.$query_extras.' LIMIT 2');

        if(count($u_array) != 1) {
            return false;
        }

        $user = $u_array[0];

        $login_name = $user[rex_config::get('ycom', 'login_field')];
        $login_psw = $user["password"];

        self::login($login_name, $login_psw, "", false, "", true);

        return self::getUser();

    }

}
