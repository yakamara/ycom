<?php

class rex_ycom_auth
{
    public const STATUS_NOT_LOGGED_IN = 0;
    public const STATUS_IS_LOGGED_IN = 1;
    public const STATUS_HAS_LOGGED_IN = 2;
    public const STATUS_HAS_LOGGED_OUT = 3;
    public const STATUS_LOGIN_FAILED = 4;

    public static bool $debug = false;
    /** @var rex_ycom_user|null */
    public static $me;

    /** @var array<int|string, mixed> */
    public static $perms = [
        '0' => 'translate:ycom_perm_extends',
        '1' => 'translate:ycom_perm_only_logged_in',
        '2' => 'translate:ycom_perm_only_not_logged_in',
        '3' => 'translate:ycom_perm_all',
    ];

    /** @var array<string, string> */
    public static $DefaultRequestKeys = [
        'auth_request_stay' => 'rex_ycom_auth_stay',
        //         'auth_request_id' => 'rex_ycom_auth_id',
    ];
    public static string $sessionKey = 'ycom_login';

    public static array $injections = [];

    public static function addInjection(rex_ycom_injection_abtract $injection): void
    {
        self::$injections[] = $injection;
    }

    public static function getInjections(): array
    {
        return self::$injections;
    }

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
        $params['loginStay'] = false;
        $params['loginName'] = '';
        $params['loginPassword'] = '';
        $params['ignorePassword'] = true;
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
        if (self::STATUS_HAS_LOGGED_IN == $login_status) {
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

        if (self::STATUS_HAS_LOGGED_OUT == $login_status && '' == $params['redirect']) {
            $params['redirect'] = rex_getUrl(rex_plugin::get('ycom', 'auth')->getConfig('article_id_jump_logout'), '', [], '&');
        }

        // if (4 == $login_status && '' == $params['redirect']) {
        // $status_params = [self::getRequestKey('auth_request_name') => $params['loginName'], 'returnTo' => $params['referer'], self::getRequestKey('auth_request_stay') => $params['loginStay']];
        // $params['redirect'] = rex_getUrl(rex_plugin::get('ycom', 'auth')->getConfig('article_id_jump_not_ok'), '', $status_params, '&');
        // }

        $params['loginStatus'] = $login_status;
        $params = rex_extension::registerPoint(new rex_extension_point('YCOM_AUTH_INIT', $params, []));

        if (rex_plugin::get('ycom', 'auth')->getConfig('article_id_logout') == rex_article::getCurrentId()) {
            // ignore rest - because logout is always ok .
        } else {
            foreach(self::getInjections() as $injection) {
                $rewrite = $injection->getRewrite();
                if ($rewrite && '' != $rewrite) {
                    return $rewrite;
                }
            }
        }

        return $params['redirect'];
    }

    public static function logout(rex_ycom_user $me): void
    {
        rex_ycom_log::log($me, rex_ycom_log::TYPE_LOGOUT);
        rex_response::cleanOutputBuffers();
        self::clearUserSession();
        self::deleteStayLoggedInCookie();
        self::$me = null;
    }

    /**
     * Use provided credentials from `$params` to check the login status. Returns the login status, one of
     * -  {@see rex_ycom_auth::STATUS_NOT_LOGGED_IN STATUS_NOT_LOGGED_IN}
     * -  {@see rex_ycom_auth::STATUS_IS_LOGGED_IN STATUS_IS_LOGGED_IN}
     * -  {@see rex_ycom_auth::STATUS_HAS_LOGGED_IN STATUS_HAS_LOGGED_IN}
     * -  {@see rex_ycom_auth::STATUS_HAS_LOGGED_OUT STATUS_HAS_LOGGED_OUT}
     * -  {@see rex_ycom_auth::STATUS_LOGIN_FAILED STATUS_LOGIN_FAILED}.
     *
     * @param array{
     *     filter: array|string,
     *     loginName: string,
     *     loginPassword: string,
     *     ignorePassword: bool,
     *     loginStay: bool,
     *     } $params
     *
     * @throws rex_exception
     */
    public static function login(array $params): int
    {
        /**
         * @param rex_yform_manager_query<static> $query
         * @return void
         */
        $filter = static function (rex_yform_manager_query $query) use ($params): void {
            if (is_array($params['filter'])) {
                foreach ($params['filter'] as $filter) {
                    $query->whereRaw($filter);
                }
            } elseif ('' != $params['filter']) {
                $query->whereRaw($params['filter']);
            }
        };

        rex_login::startSession();

        /*
         login_status
         0: not logged in
         1: logged in
         2: has just logged in
         3: has just logged out
         4: login failed
       */

        $loginStatus = self::STATUS_NOT_LOGGED_IN; // not logged in
        $loginFieldName = (string) rex_config::get('ycom/auth', 'login_field', 'email');
        $me = null;
        $sessionUserID = self::getSessionVar('UID', 'string', null);
        $cookieKey = rex_cookie(self::getStayLoggedInCookieName(), 'string', null);

        // ----- Login OVER CookieKey and SessionKey
        // Sobald ein Login durchgeführt wird, werden sessionkey und cookiekey
        // ignoriert und überschrieben
        if ('' != $params['loginName']) {
            try {
                $loginStatus = self::STATUS_HAS_LOGGED_IN; // has just logged in
                $userQuery =
                    rex_ycom_user::query()
                        ->where($loginFieldName, $params['loginName']);

                $filter($userQuery);

                $loginUsers = $userQuery->find();

                if (1 != count($loginUsers)) {
                    throw new rex_exception('Login failed');
                }

                /** @var rex_ycom_user $loginUser */
                $loginUser = $loginUsers[0];

                // Check Only AuthRules
                $auth_rules = new rex_ycom_auth_rules();
                $authRuleConfig = rex_config::get('ycom/auth', 'auth_rule', 'login_try_5_pause') ?? 'login_try_5_pause';
                if (!$auth_rules->check($loginUser, $authRuleConfig)) {
                    throw new rex_exception('Login failed - Auth Rules');
                }

                $loginUser->setValue('last_login_try_time', rex_sql::datetime(time()));

                if (
                    $params['ignorePassword']
                    || ('' != $params['loginPassword'] && self::checkPassword($params['loginPassword'], $loginUser->getId()))
                ) {
                    $me = $loginUser;
                    $me
                        ->setValue('last_login_time', rex_sql::datetime(time()))
                        ->setValue('login_tries', 0);
                    $me = rex_extension::registerPoint(new rex_extension_point('YCOM_AUTH_LOGIN_SUCCESS', $me, []));

                    // session fixation
                    rex_login::regenerateSessionId();

                    // stay logged in if selected
                    $cookieKey = null;
                    if ($params['loginStay']) {
                        $cookieKey = base64_encode(random_bytes(64));
                        self::setStayLoggedInCookie($cookieKey);
                    }
                    rex_ycom_user_session::getInstance()->storeCurrentSession($me, $cookieKey);

                    rex_ycom_log::log($me, rex_ycom_log::TYPE_LOGIN_SUCCESS, [
                        'stayloggedin' => ($params['loginStay']) ? 'yes' : '-',
                    ]);
                } else {
                    $loginUser->increaseLoginTries()->save();
                    throw new rex_exception('Login failed . Password wrong or not set or not ignored');
                }
            } catch (throwable $e) {
                $loginStatus = self::STATUS_LOGIN_FAILED; // login failed
                rex_ycom_user_session::clearExpiredSessions();
                rex_response::clearCookie(self::getStayLoggedInCookieName());

                rex_ycom_log::log(
                    $params['loginName'],
                    rex_ycom_log::TYPE_LOGIN_FAILED,
                    [
                        'EXCEPTION' => $e->getMessage(),
                        'SERVER' => $_SERVER,
                        'REQUEST' => $_REQUEST,
                    ],
                );
            }
            // login try -> no session or cookie login
            $cookieKey = null;
            $sessionUserID = null;
        }

        // ----- Login via SessionKey
        if (null !== $sessionUserID) {
            try {
                $userQuery =
                    rex_ycom_user::query()
                        ->where('id', $sessionUserID);

                $filter($userQuery);

                $loginUsers = $userQuery->find();

                if (1 != count($loginUsers)) {
                    throw new rex_exception('session `' . $sessionUserID . '` - user not found');
                }

                rex_ycom_user_session::clearExpiredSessions();
                if (0 === count(rex_sql::factory()->getArray('SELECT 1 FROM ' . rex::getTable('ycom_user_session') . ' where session_id = ?', [session_id()]))) {
                    throw new rex_exception('session expired or missing');
                }

                /** @var rex_ycom_user $me */
                $me = $loginUsers[0];
                self::setUser($me);
                $loginStatus = self::STATUS_IS_LOGGED_IN; // is logged in
            } catch (throwable $e) {
                $loginStatus = self::STATUS_LOGIN_FAILED; // login failed
                self::clearUserSession();
                rex_ycom_log::log(
                    $sessionUserID,
                    rex_ycom_log::TYPE_SESSION_FAILED,
                    [
                        'EXCEPTION' => $e->getMessage(),
                        'SERVER' => $_SERVER,
                        'REQUEST' => $_REQUEST,
                    ],
                );
            }
        }

        // ----- Login via CookieKey
        if (null !== $cookieKey && null === $me) {
            try {
                $cookieUser = rex_sql::factory()->setQuery('select user_id from ' . rex::getTable('ycom_user_session') . ' where cookie_key = ?', [$cookieKey]);
                if (1 !== $cookieUser->getRows()) {
                    throw new rex_exception('cookiekey `' . $cookieKey . '` not found');
                }
                $userQuery =
                    rex_ycom_user::query()
                        ->where('id', $cookieUser->getValue('user_id'));

                $filter($userQuery);

                $loginUsers = $userQuery->find();

                if (1 !== count($loginUsers)) {
                    throw new rex_exception('cookiekey `' . $cookieKey . '` - user width id=`' . $cookieUser->getValue('user_id') . '` not found');
                }

                /** @var rex_ycom_user $me */
                $me = $loginUsers[0];

                rex_ycom_user_session::getInstance()->storeCurrentSession($me, $cookieKey);

                rex_ycom_log::log(
                    $me,
                    rex_ycom_log::TYPE_LOGIN_SUCCESS,
                    [
                        'Login via CookieKey',
                    ],
                );
            } catch (throwable $e) {
                $loginStatus = self::STATUS_LOGIN_FAILED; // login failed
                self::clearUserSession();
                rex_response::clearCookie(self::getStayLoggedInCookieName());
                rex_ycom_log::log(
                    '-',
                    rex_ycom_log::TYPE_COOKIE_FAILED,
                    [
                        'EXCEPTION' => $e->getMessage(),
                        'SERVER' => $_SERVER,
                        'REQUEST' => $_REQUEST,
                    ],
                );
            }
        }

        if (null !== $me) {
            /** @var rex_ycom_user $me */
            $me = rex_extension::registerPoint(new rex_extension_point('YCOM_AUTH_LOGIN', $me, []));
            $me->setValue('last_action_time', rex_sql::datetime(time()));
            $me->setHistoryEnabled(false);
            $me->save();

            self::setUser($me);

            rex_ycom_user_session::getInstance()->updateLastActivity($me);
            rex_response::sendCacheControl('no-store');
            rex_response::setHeader('Pragma', 'no-cache');
        }

        return $loginStatus;
    }

    /**
     * @param array<string, string> $params
     * @return false|rex_ycom_user|null
     */
    public static function loginWithParams($params, ?callable $filter = null)
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
        $params['loginPassword'] = $user->getValue('password');
        $params['ignorePassword'] = true;
        $params['filter'] = [];
        $params['loginStay'] = false;

        self::login($params);

        return self::getUser();
    }

    /**
     * @param string|int $user_id
     */
    public static function checkPassword(string $password, $user_id): bool
    {
        if ('' == trim($password)) {
            return false;
        }

        /** @var rex_ycom_user $user */
        $user = rex_ycom_user::get((int) $user_id);
        if (null !== $user) {
            if (rex_login::passwordVerify($password, $user->getPassword())) {
                return true;
            }
        }

        return false;
    }

    public static function setUser(rex_ycom_user $me): void
    {
        rex_login::startSession();
        self::setSessionVar('UID', $me->getId());
        self::$me = $me;
    }

    /**
     * @return rex_ycom_user|null
     */
    public static function getUser()
    {
        return self::$me;
    }

    public static function deleteUser(int $id): bool
    {
        return rex_ycom_user::query()->where('id', $id)->find()->delete();
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
        self::$me = null;
        rex_ycom_user_session::getInstance()
            ->clearCurrentSession()
            ->clearExpiredSessions();
        self::unsetSessionVars();
    }

    /**
     * @throws rex_exception
     */
    public static function setSessionVar(string $key, $value): void
    {
        $sessionVars = rex_session(self::$sessionKey, 'array', []);
        $sessionVars[$key] = $value;
        rex_set_session(self::$sessionKey, $sessionVars);
    }

    /**
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

    public static function unsetSessionVars(): void
    {
        rex_set_session(self::$sessionKey, []);
    }

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
            $returnUrl .= '?' . $url['query'];
        }

        $article_id_logout = rex_config::get('ycom/auth', 'article_id_logout', '');
        if ($article_id_logout > 0) {
            $referer_to_logout = strpos($returnUrl, rex_getUrl(rex_config::get('ycom/auth', 'article_id_logout', '')));
        } else {
            $referer_to_logout = false;
        }

        if (false === $referer_to_logout) {
        } else {
            $returnUrl = '';
        }

        return $returnUrl;
    }

    /**
     * @param array<string> $returnTos
     * @param array<string> $allowedDomains
     */
    public static function getReturnTo(array $returnTos, array $allowedDomains): string
    {
        $return = '';
        $returnTosWithDomains = [];
        foreach ($returnTos as $returnTo) {
            if ('' != $returnTo) {
                if (!preg_match('/http(s?)\:\/\//i', $returnTo)) {
                    $frontendUrl = rex_url::frontend();
                    if (str_contains($returnTo, $frontendUrl)) {
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
                        break 2;
                    }
                }
            }
        }

        return $return;
    }

    /**
     * @return string
     */
    public static function getStayLoggedInCookieName()
    {
        $instname = rex::getProperty('instname');
        if (!$instname) {
            throw new rex_exception('Property "instname" is empty');
        }

        return sha1('rex_ycom_user_' . $instname);
    }

    private static function setStayLoggedInCookie(string $cookiekey): void
    {
        $sessionConfig = rex::getProperty('session', [])['frontend']['cookie'] ?? [];

        rex_response::sendCookie(self::getStayLoggedInCookieName(), $cookiekey, [
            'expires' => strtotime('+1 year'),
            'secure' => $sessionConfig['secure'] ?? false,
            'samesite' => $sessionConfig['samesite'] ?? 'lax',
        ]);
    }

    private static function deleteStayLoggedInCookie(): void
    {
        rex_response::clearCookie(self::getStayLoggedInCookieName());
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
}
