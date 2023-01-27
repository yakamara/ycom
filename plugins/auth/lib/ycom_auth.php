<?php

class rex_ycom_auth
{
    public static bool $debug = false;
    /**
     * @var rex_ycom_user|null
     */
    public static $me;

    /**
     * @var array<int|string, mixed>
     */
    public static $perms = [
        '0' => 'translate:ycom_perm_extends',
        '1' => 'translate:ycom_perm_only_logged_in',
        '2' => 'translate:ycom_perm_only_not_logged_in',
        '3' => 'translate:ycom_perm_all',
    ];

    /**
     * @var array<string, string>
     */
    public static $DefaultRequestKeys = [
        'auth_request_stay' => 'rex_ycom_auth_stay',
        //         'auth_request_id' => 'rex_ycom_auth_id',
    ];
    public static string $sessionKey = 'ycom_login';

    public static function getRequestKey(string $requestKey): string
    {
        return rex_config::get('ycom', $requestKey, self::$DefaultRequestKeys[$requestKey]);
    }

    public static function init(): string
    {
        $params = [];
        // $params['loginStay'] = rex_request(self::getRequestKey('auth_request_stay'), 'string');
        // $params['returnTo'] = rex_request('returnTo', 'string');
        // $params['referer'] = self::cleanReferer($params['returnTo']);

        $params['redirect'] = '';

        // # Check for Login / Logout
        /*
          login_status
          0: not logged in
          1: logged in
          2: has logged in
          3: has logged out
          4: login failed
        */
        $params['filter'] = [
            'status > 0',
        ];
        $login_status = self::login($params);

        // set redirect after Login
        if (2 == $login_status) {
            // if ($params['referer']) {
            //     $params['redirect'] = urldecode($params['referer']);
            // } else {
            $params['redirect'] = rex_getUrl(rex_plugin::get('ycom', 'auth')->getConfig('article_id_jump_ok'));
            // }
        }

        // Checking page permissions
        $currentId = rex_article::getCurrentId();
        if ($article = rex_article::get($currentId)) {
            if (!$article->isPermitted() && !$params['redirect'] && rex_plugin::get('ycom', 'auth')->getConfig('article_id_jump_denied') != rex_article::getCurrentId()) {
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
                    $params = ['returnTo' => $refererURL];
                }

                $article_id_login = (int) rex_plugin::get('ycom', 'auth')->getConfig('article_id_login');
                $article_id_jump_denied = (int) rex_plugin::get('ycom', 'auth')->getConfig('article_id_jump_denied');

                if (!self::getUser() && 0 != $article_id_login) {
                    $params['redirect'] = rex_getUrl($article_id_login, '', $params, '&');
                } else {
                    $params['redirect'] = rex_getUrl($article_id_jump_denied, '', $params, '&');
                }
            }
        }

        if (3 == $login_status && '' == $params['redirect']) {
            $params['redirect'] = rex_getUrl(rex_plugin::get('ycom', 'auth')->getConfig('article_id_jump_logout'), '', [], '&');
        }

        // if (4 == $login_status && '' == $params['redirect']) {
        // $status_params = [self::getRequestKey('auth_request_name') => $params['loginName'], 'returnTo' => $params['referer'], self::getRequestKey('auth_request_stay') => $params['loginStay']];
        // $params['redirect'] = rex_getUrl(rex_plugin::get('ycom', 'auth')->getConfig('article_id_jump_not_ok'), '', $status_params, '&');
        // }

        $params['loginStatus'] = $login_status;
        $params = rex_extension::registerPoint(new rex_extension_point('YCOM_AUTH_INIT', $params, []));

        if (self::getUser()) {
            $article_id_password = (int) rex_plugin::get('ycom', 'auth')->getConfig('article_id_jump_password');
            $article_id_termsofuse = (int) rex_plugin::get('ycom', 'auth')->getConfig('article_id_jump_termsofuse');

            if (rex_plugin::get('ycom', 'auth')->getConfig('article_id_logout') == rex_article::getCurrentId()) {
                // ignore rest - because logout is always ok .
            } elseif (0 != $article_id_termsofuse && 1 != self::getUser()->getValue('termsofuse_accepted')) {
                if ($article_id_termsofuse != rex_article::getCurrentId()) {
                    $params['redirect'] = rex_getUrl($article_id_termsofuse, '', [], '&');
                }
            } elseif (0 != $article_id_password && 1 == self::getUser()->getValue('new_password_required')) {
                if ($article_id_password != rex_article::getCurrentId()) {
                    $params['redirect'] = rex_getUrl($article_id_password, '', [], '&');
                }
            }
        }

        return $params['redirect'];
    }

    /**
     * @param array<int|string, mixed> $params
     * @throws rex_exception
     */
    public static function login(array $params): int
    {
        $filter = null;
        if (isset($params['filter']) && '' != $params['filter']) {
            /**
             * @param rex_yform_manager_query<static> $query
             * @return void
             */
            $filter = static function (rex_yform_manager_query $query) use ($params): void {
                if (is_array($params['filter'])) {
                    foreach ($params['filter'] as $filter) {
                        $query->whereRaw($filter);
                    }
                } else {
                    $query->whereRaw($params['filter']);
                }
            };
        }

        rex_login::startSession();

        $loginStatus = 0; // not logged in
        // $sessionKey = null;
        $sessionUserID = null;
        $me = null;

        if (self::getSessionVar('UID', 'string', null)) {
            $sessionUserID = self::getSessionVar('UID', 'string', null);
        }

        // if (self::getCookieVar(self::$sessionKey, 'string', null)) {
        //     $sessionKey = self::getCookieVar(self::$sessionKey);
        // }

        if (
            (
                !empty($params['loginName']) &&
                (
                    (
                        isset($params['ignorePassword']) &&
                        $params['ignorePassword']
                    )
                    ||
                    !empty($params['loginPassword'])
                )
            )
            || $sessionUserID
            // || $sessionKey
        ) {
            if (!empty($params['loginName'])) {
                $userQuery =
                    rex_ycom_user::query()
                        ->where(rex_config::get('ycom/auth', 'login_field', 'email') ?? 'email', $params['loginName']);

                if ($filter) {
                    $filter($userQuery);
                }

                $loginUsers = $userQuery->find();

                if (1 == count($loginUsers)) {
                    /** @var rex_ycom_user $user */
                    $user = $loginUsers[0];

                    $auth_rules = new rex_ycom_auth_rules();

                    if (!$auth_rules->check($user, rex_config::get('ycom/auth', 'auth_rule', 'login_try_5_pause') ?? 'login_try_5_pause')) {
                    } elseif (@$params['ignorePassword'] || self::checkPassword($params['loginPassword'], $user->getId())) {
                        $me = $user;
                        $me->setValue('login_tries', 0);
                        // if (isset($params['loginStay']) && !$params['loginStay']) {
                        //     $me->setValue('session_key', '');
                        // }
                        // session fixation
                        // self::regenerateSessionId();

                        rex_ycom_user_session::getInstance()->storeCurrentSession($me);
                        rex_ycom_user_session::clearExpiredSessions();

                    } else {
                        $user->setValue('login_tries', $user->getValue('login_tries') + 1);
                        // rex_sql -> no validations on fields wanted, or datestamp updates
                        rex_sql::factory()
                            ->setTable(rex_ycom_user::table())
                            ->setWhere(['id' => $user->getId()])
                            ->setValue('login_tries', $user->getValue('login_tries'))
                            ->update();

                        rex_ycom_log::log($user, rex_ycom_log::TYPE_LOGIN_FAILED, [
                            (string) json_encode([
                                'SERVER' => $_SERVER,
                                'REQUEST' => $_REQUEST,
                            ]),
                        ]);
                    }
                } else {
                    rex_ycom_log::log($params['loginName'], rex_ycom_log::TYPE_LOGIN_NOT_FOUND, [
                        (string) json_encode([
                            'SERVER' => $_SERVER,
                            'REQUEST' => $_REQUEST,
                        ]),
                    ]);
                }

                if (!$me) {
                    // logintry with name -> if logged in this try means kill the session
                    self::clearUserSession();
                    $sessionUserID = null;
                    // $sessionKey = null;
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

                if (1 == count($loginUsers)) {
                    $me = $loginUsers[0];
                }
            }

            // if (!$me && $sessionKey) {
            //     $userQuery =
            //         rex_ycom_user::query()
            //             ->where('session_key', $sessionKey);
            //
            //     if ($filter) {
            //         $filter($userQuery);
            //     }
            //
            //     $loginUsers = $userQuery->find();
            //
            //     if (1 == count($loginUsers)) {
            //         $me = $loginUsers[0];
            //
            //         $sessionKey = bin2hex(random_bytes(16));
            //         $me->setValue('session_key', $sessionKey);
            //         self::setCookieVar(self::$sessionKey, $sessionKey, time() + (3600 * 24 * rex_plugin::get('ycom', 'auth')->getConfig('auth_cookie_ttl', 14)));
            //
            //         // session fixation
            //         self::regenerateSessionId();
            //     } else {
            //         self::clearUserSession();
            //     }
            // }

            try {
                if (!$me) {
                    throw new Exception('no user found');
                }

                rex_ycom_user_session::clearExpiredSessions();
                if (0 === count(rex_sql::factory()->getArray('SELECT 1 FROM '.rex::getTable('ycom_user_session').' where session_id = ?', [session_id()]))) {
                    rex_ycom_log::log($me, rex_ycom_log::TYPE_SESSION_EXPIRED);
                    self::clearUserSession();
                    throw new Exception('session expired or missing');
                }

                self::setUser($me);
                $loginStatus = 1; // is logged in

                // if (isset($params['loginStay']) && $params['loginStay']) {
                //     $sessionKey = bin2hex(random_bytes(16));
                //     $me->setValue('session_key', $sessionKey);
                //     self::setCookieVar(self::$sessionKey, $sessionKey, time() + (3600 * 24 * rex_plugin::get('ycom', 'auth')->getConfig('auth_cookie_ttl', 14)));
                // }

                $me->setValue('last_action_time', rex_sql::datetime(time()));

                if (!empty($params['loginName'])) {
                    $loginStatus = 2; // has just logged in
                    $me = rex_extension::registerPoint(new rex_extension_point('YCOM_AUTH_LOGIN_SUCCESS', $me, []));
                    $me->setValue('last_login_time', rex_sql::datetime(time()));
                }

                $me = rex_extension::registerPoint(new rex_extension_point('YCOM_AUTH_LOGIN', $me, []));
                $me->save();

                rex_ycom_user_session::getInstance()->updateLastActivity($me);
                rex_response::sendCacheControl('no-store');
                rex_response::setHeader('Pragma', 'no-cache');
            } catch (Throwable $e) {
                $loginStatus = 0; // not logged in

                if (!empty($params['loginName'])) {
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

    /**
     * @param string|int $user_id
     */
    public static function checkPassword(string $password, $user_id): bool
    {
        if ('' == trim($password)) {
            return false;
        }

        $user = rex_ycom_user::get((int) $user_id);
        if ($user) {
            if (rex_login::passwordVerify($password, $user->getPassword())) {
                return true;
            }
        }

        return false;
    }

    public static function setUser(rex_ycom_user $me): void
    {
        \rex_login::startSession();
        self::setSessionVar('UID', $me->getId());
        self::$me = $me;
    }

    /**
     * @return null|rex_ycom_user
     */
    public static function getUser()
    {
        return self::$me;
    }

    /**
     * @param rex_article|rex_category $article
     *
     * @deprecated
     */
    public static function checkPerm(&$article): bool
    {
        return self::articleIsPermitted($article);
    }

    /**
     * @param rex_article|rex_category $article
     */
    public static function articleIsPermitted(&$article, bool $xs = true): bool
    {
        if (!$xs) {
            return false;
        }

        $me = self::getUser();

        unset($xs);

        /*
        static $perms = [
            '0' => 'translate:ycom_perm_extends',
            '1' => 'translate:ycom_perm_only_logged_in',
            '2' => 'translate:ycom_perm_only_not_logged_in',
            '3' => 'translate:ycom_perm_all'
        ];*/

        $permType = (int) $article->getValue('ycom_auth_type');

        if (3 == $permType) {
            $xs = true;
        }

        // 0 - parent perms
        if (!isset($xs) && $permType < 1) {
            if ($o = $article->getParent()) {
                return $o->isPermitted();
            }

            // no parent, no perm set -> for all accessible
            $xs = true;
        }

        // 2 - only if not logged in
        if (!isset($xs) && 2 == $permType) {
            if ($me) {
                $xs = false;
            } else {
                $xs = true;
            }
        }

        // 1 - only if logged in .. further group perms
        if (!isset($xs) && 1 == $permType && !$me) {
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

    public static function clearUserSession(): void
    {
        rex_set_session(self::$sessionKey, null);
        // self::setCookieVar(self::$sessionKey, null);
        // self::setCookieVar(self::$sessionKey, '', time() - 3600);
        self::$me = null;

        rex_ycom_user_session::getInstance()
            ->clearCurrentSession()
            ->clearExpiredSessions();

        // TODO: Gecachte Medien löschen ?
        // rex_response::setHeader('Clear-Site-Data', '"cache", "cookies", "storage", "executionContexts"');
    }

    /**
     * @param mixed $value
     * @throws rex_exception
     */
    public static function setSessionVar(string $key, $value): void
    {
        $sessionVars = rex_session(self::$sessionKey, 'array', []);
        $sessionVars[$key] = $value;
        rex_set_session(self::$sessionKey, $sessionVars);
    }

    /**
     * @param mixed $default
     * @throws rex_exception
     * @return array|bool|float|int|mixed|object|string
     */
    public static function getSessionVar(string $key, string $varType = 'string', $default = '')
    {
        $sessionVars = rex_session(self::$sessionKey, 'array', []);

        if (array_key_exists($key, $sessionVars)) {
            return rex_type::cast($sessionVars[$key], $varType);
        }

        if ('' === $default) {
            return rex_type::cast($default, $varType);
        }

        return $default;
    }

    public static function unsetSessionVar(string $key): void
    {
        $sessionVars = rex_session(self::$sessionKey, 'array', []);
        unset($sessionVars[$key]);
        rex_set_session(self::$sessionKey, $sessionVars);
    }

    // public static function setCookieVar(string $key, string $value = null, int $time = null): void
    // {
    //     $sessionConfig = rex::getProperty('session', []);
    //     setcookie($key, $value ?? '', $time ?? (time() + 3600), '/', $sessionConfig['frontend']['cookie']['domain'] ?? ''); // $sessionConfig['frontend']['cookie']['path']
    //     $_COOKIE[$key] = $value;
    // }

    /**
     * @param mixed $default
     * @return mixed
     */
    // public static function getCookieVar(string $key, string $varType = 'string', $default = '')
    // {
    //     return rex_cookie($key, $varType, $default);
    // }

    public static function deleteUser(int $id): bool
    {
        return rex_ycom_user::query()->where('id', $id)->find()->delete();
    }

    /**
     * @param array<string, string> $params
     * @return null|false|rex_ycom_user
     */
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

        if (1 != count($Users)) {
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

    // protected static function regenerateSessionId(): void
    // {
    //     if ('' != session_id()) {
    //         session_regenerate_id(true);
    //     }
    //     $_SESSION['REX_SESSID'] = session_id();
    // }

    public static function cleanReferer(string $refererURL): string
    {
        if ('' === $refererURL) {
            return '';
        }

        $url = parse_url($refererURL) ?: []; /** @phpstan-ignore-line */
        $returnUrl = '';

        if (isset($url['host']) && rex_addon::get('yrewrite')->isInstalled()) {
            $domains = rex_yrewrite::getDomains();

            if (array_key_exists($url['host'], $domains)) {
                $returnUrl .= (rex_yrewrite::isHttps() ? 'https://' : 'http://');
                $returnUrl .= $domains[$url['host']]->getHost();
            }
        }

        if (isset($url['path']) && '' != $url['path']) {
            $returnUrl .= $url['path'];
        }

        if (isset($url['query']) && '' != $url['query']) {
            $returnUrl .= '?'. $url['query'];
        }

        $referer_to_logout = strpos($returnUrl, rex_getUrl(rex_config::get('ycom/auth', 'article_id_logout', '')));
        if (false === $referer_to_logout) {
        } else {
            $returnUrl = '';
        }

        return $returnUrl;
    }

    /**
     * @param string[] $returnTos
     * @param string[] $allowedDomains
     */
    public static function getReturnTo(array $returnTos, array $allowedDomains): string
    {
        $return = '';
        $returnTosWithDomains = [];
        foreach ($returnTos as $returnTo) {
            if ('' != $returnTo) {
                if (!preg_match('/http(s?)\:\/\//i', $returnTo)) {
                    $frontendUrl = rex_url::frontend();
                    if (false !== strpos($returnTo, $frontendUrl)) {
                        $returnTo = str_replace($frontendUrl, '/', $returnTo);
                    }
                    $returnTo = rex_yrewrite::getFullPath('/' == substr($returnTo, 0, 1) ? substr($returnTo, 1) : $returnTo);
                }
                $returnTosWithDomains[] = $returnTo;
            }
        }

        foreach (rex_yrewrite::getDomains() as $ydomain) {
            $allowedDomains[] = $ydomain->getUrl();
        }

        foreach ($returnTosWithDomains as $returnTosWithDomain) {
            if ('' != $returnTosWithDomain) {
                if (0 == count($allowedDomains)) {
                    $return = $returnTosWithDomain;
                    break;
                }
                foreach ($allowedDomains as $allowedDomain) {
                    if (substr($returnTosWithDomain, 0, strlen($allowedDomain)) == $allowedDomain) {
                        $return = $returnTosWithDomain;
                        break;
                    }
                }
            }
        }

        return $return;
    }
}
