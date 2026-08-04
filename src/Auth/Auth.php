<?php

namespace Yakamara\YCom\Auth;

use Redaxo\Core\Content\Article;
use Redaxo\Core\Content\Category;
use Redaxo\Core\Core;
use Redaxo\Core\Database\Sql;
use Redaxo\Core\Exception\RuntimeException;
use Redaxo\Core\ExtensionPoint\Extension;
use Redaxo\Core\ExtensionPoint\ExtensionPoint;
use Redaxo\Core\Filesystem\Url;
use Redaxo\Core\Http\Request;
use Redaxo\Core\Http\Response;
use Redaxo\Core\Security\Login;
use Redaxo\Core\Util\Type;
use Throwable;
use Yakamara\YCom\Auth\Injection\AbstractInjection;
use Yakamara\YCom\Config;
use Yakamara\YCom\Log;
use Yakamara\YCom\User;
use Yakamara\YForm\Manager\Query;
use Yakamara\YRewrite\YRewrite;

use function array_key_exists;
use function count;
use function in_array;
use function is_array;
use function is_string;
use function ksort;
use function random_bytes;
use function session_id;
use function str_contains;
use function strlen;
use function substr;
use function time;

/**
 * Frontend authentication: session/cookie login, page permissions and the injection chain.
 */
final class Auth
{
    public const int STATUS_NOT_LOGGED_IN = 0;
    public const int STATUS_IS_LOGGED_IN = 1;
    public const int STATUS_HAS_LOGGED_IN = 2;
    public const int STATUS_HAS_LOGGED_OUT = 3;
    public const int STATUS_LOGIN_FAILED = 4;

    /** The `ycom_auth_type` choices of an article. */
    public const array PERMS = [
        '0' => 'translate:ycom_perm_extends',
        '1' => 'translate:ycom_perm_only_logged_in',
        '2' => 'translate:ycom_perm_only_not_logged_in',
        '3' => 'translate:ycom_perm_all',
    ];

    public static string $sessionKey = 'ycom_login';

    private static ?User $me = null;

    /** @var array<int, list<AbstractInjection>> */
    private static array $injections = [];

    private function __construct() {}

    public static function addInjection(AbstractInjection $injection, int $level = 1): void
    {
        self::$injections[$level][] = $injection;
    }

    /** @return list<AbstractInjection> */
    public static function getInjections(): array
    {
        $injections = [];
        ksort(self::$injections);

        foreach (self::$injections as $levelInjections) {
            foreach ($levelInjections as $injection) {
                $injections[] = $injection;
            }
        }

        return $injections;
    }

    /**
     * Runs on every frontend request and returns the URL this request has to be redirected to,
     * or an empty string when it may be served as requested.
     */
    public static function init(): string
    {
        $params = [
            'redirect' => '',
            'loginStay' => false,
            'loginName' => '',
            'loginPassword' => '',
            'ignorePassword' => true,
            'filter' => ['status > 0'],
        ];

        $loginStatus = self::login($params);

        if (self::STATUS_HAS_LOGGED_IN === $loginStatus) {
            $params['redirect'] = Url::article((int) Config::get('article_id_jump_ok'));
        }

        // ----- page permissions
        $currentId = Article::getCurrentId();
        $article = Article::get($currentId);

        if (
            null !== $article
            && !$article->isPermitted()
            && '' === $params['redirect']
            && (int) Config::get('article_id_jump_denied') !== $currentId
        ) {
            $params = [];

            // Remember where the user wanted to go — but not when that is the logout or
            // post-login page, which would bounce them straight back out again.
            $ignoreRefArticles = [
                (int) Config::get('article_id_jump_logout'),
                (int) Config::get('article_id_jump_ok'),
            ];

            if (!in_array($currentId, $ignoreRefArticles, true)) {
                $refererUrl = self::cleanReferer(YRewrite::getFullUrlByArticleId());
                if ('' !== $refererUrl) {
                    $params = ['returnTo' => $refererUrl];
                }
            }

            $articleIdLogin = (int) Config::get('article_id_login');
            $articleIdDenied = (int) Config::get('article_id_jump_denied');

            $params['redirect'] = null === self::getUser() && 0 !== $articleIdLogin
                ? Url::article($articleIdLogin, null, $params)
                : Url::article($articleIdDenied, null, $params);
        }

        if (self::STATUS_HAS_LOGGED_OUT === $loginStatus && '' === $params['redirect']) {
            $params['redirect'] = Url::article((int) Config::get('article_id_jump_logout'));
        }

        $params['loginStatus'] = $loginStatus;
        $params = Extension::dispatch(new ExtensionPoint('YCOM_AUTH_INIT', $params, []));
        $params = is_array($params) ? $params : ['redirect' => ''];

        // The logout page is always reachable, otherwise an injection could lock a user in.
        if ((int) Config::get('article_id_logout') !== Article::getCurrentId()) {
            foreach (self::getInjections() as $injection) {
                $rewrite = $injection->getRewrite();

                if (is_string($rewrite) && '' !== $rewrite) {
                    return $rewrite;
                }

                if (true === $rewrite) {
                    // No redirect — this request already *is* the injection's own page.
                    break;
                }
            }
        }

        return (string) ($params['redirect'] ?? '');
    }

    public static function logout(User $me): void
    {
        Log::log($me, Log::TYPE_LOGOUT);
        Response::cleanOutputBuffers();
        self::clearUserSession();
        self::deleteStayLoggedInCookie();
        self::$me = null;
    }

    /**
     * Checks the credentials in `$params` and returns one of the `STATUS_*` constants.
     *
     * @param array{
     *     filter: array<string>|string,
     *     loginName: string,
     *     loginPassword: string,
     *     ignorePassword: bool,
     *     loginStay: bool,
     * } $params
     */
    public static function login(array $params): int
    {
        $filter = static function (Query $query) use ($params): void {
            if (is_array($params['filter'])) {
                foreach ($params['filter'] as $where) {
                    $query->whereRaw($where);
                }
            } elseif ('' !== $params['filter']) {
                $query->whereRaw($params['filter']);
            }
        };

        Login::startSession();

        $loginStatus = self::STATUS_NOT_LOGGED_IN;
        $loginFieldName = (string) Config::get('login_field', 'email');
        $me = null;
        $sessionUserId = self::getSessionVar('UID', 'string', null);
        $cookieKey = Request::cookie(self::getStayLoggedInCookieName(), 'string', null);

        // ----- login with credentials; overrides any session or cookie login
        if ('' !== $params['loginName']) {
            try {
                $loginStatus = self::STATUS_HAS_LOGGED_IN;

                $userQuery = User::query()->where($loginFieldName, $params['loginName']);
                $filter($userQuery);
                $loginUsers = $userQuery->find();

                if (1 !== count($loginUsers)) {
                    throw new RuntimeException('Login failed');
                }

                /** @var User $loginUser */
                $loginUser = $loginUsers[0];

                $authRuleConfig = (string) (Config::get('auth_rule') ?? 'login_try_5_pause');
                if (!new AuthRules()->check($loginUser, $authRuleConfig)) {
                    throw new RuntimeException('Login failed - Auth Rules');
                }

                $loginUser->setValue('last_login_try_time', Sql::datetime(time()));

                if (
                    $params['ignorePassword']
                    || ('' !== $params['loginPassword'] && self::checkPassword($params['loginPassword'], $loginUser->getId()))
                ) {
                    $me = $loginUser;
                    $me
                        ->setValue('last_login_time', Sql::datetime(time()))
                        ->setValue('login_tries', 0);

                    $me = Extension::dispatch(new ExtensionPoint('YCOM_AUTH_LOGIN_SUCCESS', $me, []));

                    // session fixation
                    Login::regenerateSessionId();

                    $cookieKey = null;
                    if ($params['loginStay']) {
                        $cookieKey = base64_encode(random_bytes(64));
                        self::setStayLoggedInCookie($cookieKey);
                    }

                    UserSession::getInstance()->storeCurrentSession($me, $cookieKey);

                    Log::log($me, Log::TYPE_LOGIN_SUCCESS, [
                        'stayloggedin' => $params['loginStay'] ? 'yes' : '-',
                    ]);
                } else {
                    $loginUser->increaseLoginTries()->save();
                    throw new RuntimeException('Login failed - password wrong, not set or not ignored');
                }
            } catch (Throwable $e) {
                $loginStatus = self::STATUS_LOGIN_FAILED;
                UserSession::clearExpiredSessions();
                Response::clearCookie(self::getStayLoggedInCookieName());

                Log::log($params['loginName'], Log::TYPE_LOGIN_FAILED, [
                    'EXCEPTION' => $e->getMessage(),
                ] + self::requestContext());
            }

            // A login attempt was made — do not additionally try session or cookie.
            $cookieKey = null;
            $sessionUserId = null;
        }

        // ----- login via session
        if (null !== $sessionUserId) {
            try {
                $userQuery = User::query()->where('id', $sessionUserId);
                $filter($userQuery);
                $loginUsers = $userQuery->find();

                if (1 !== count($loginUsers)) {
                    throw new RuntimeException('session `' . $sessionUserId . '` - user not found');
                }

                UserSession::clearExpiredSessions();

                $stillKnown = Sql::factory()->getArray(
                    'SELECT 1 FROM ' . Core::getTable('ycom_user_session') . ' WHERE session_id = ?',
                    [session_id()],
                );
                if (1 > count($stillKnown)) {
                    throw new RuntimeException('session expired or missing');
                }

                /** @var User $me */
                $me = $loginUsers[0];
                self::setUser($me);
                $loginStatus = self::STATUS_IS_LOGGED_IN;
            } catch (Throwable $e) {
                $loginStatus = self::STATUS_LOGIN_FAILED;
                self::clearUserSession();
                $me = null;

                Log::log((string) $sessionUserId, Log::TYPE_SESSION_FAILED, [
                    'EXCEPTION' => $e->getMessage(),
                ] + self::requestContext());
            }
        }

        // ----- login via "stay logged in" cookie
        if (null !== $cookieKey && null === $me) {
            try {
                $cookieUser = Sql::factory()->setQuery(
                    'SELECT user_id FROM ' . Core::getTable('ycom_user_session') . ' WHERE cookie_key = ?',
                    [$cookieKey],
                );

                if (1 !== $cookieUser->getRows()) {
                    throw new RuntimeException('cookie key not found');
                }

                $userQuery = User::query()->where('id', $cookieUser->getValue('user_id'));
                $filter($userQuery);
                $loginUsers = $userQuery->find();

                if (1 !== count($loginUsers)) {
                    throw new RuntimeException('cookie key - user not found');
                }

                /** @var User $me */
                $me = $loginUsers[0];

                UserSession::getInstance()->storeCurrentSession($me, $cookieKey);

                Log::log($me, Log::TYPE_LOGIN_SUCCESS, ['Login via CookieKey']);
            } catch (Throwable $e) {
                $loginStatus = self::STATUS_LOGIN_FAILED;
                self::clearUserSession();
                Response::clearCookie(self::getStayLoggedInCookieName());
                $me = null;

                Log::log('-', Log::TYPE_COOKIE_FAILED, [
                    'EXCEPTION' => $e->getMessage(),
                ] + self::requestContext());
            }
        }

        if (null !== $me) {
            $me = Extension::dispatch(new ExtensionPoint('YCOM_AUTH_LOGIN', $me, []));
            /** @var User $me */
            $me->setValue('last_action_time', Sql::datetime(time()));
            $me->setHistoryEnabled(false);
            $me->save();

            self::setUser($me);

            UserSession::getInstance()->updateLastActivity($me);
            Response::sendCacheControl('no-store');
            Response::setHeader('Pragma', 'no-cache');
        }

        return $loginStatus;
    }

    /**
     * Logs a user in by matching arbitrary columns — used by the token and external-auth flows.
     *
     * @param array<string, string> $params column => value
     */
    public static function loginWithParams(array $params, ?callable $filter = null): false|User|null
    {
        $userQuery = User::query();
        foreach ($params as $column => $value) {
            $userQuery->where($column, $value);
        }

        if (null !== $filter) {
            $filter($userQuery);
        }

        $users = $userQuery->find();

        if (1 !== count($users)) {
            return false;
        }

        /** @var User $user */
        $user = $users[0];

        $loginField = (string) Config::get('login_field', 'email');

        self::login([
            'loginName' => (string) $user->getValue($loginField),
            'loginPassword' => (string) $user->getValue('password'),
            'ignorePassword' => true,
            'filter' => [],
            'loginStay' => false,
        ]);

        return self::getUser();
    }

    public static function checkPassword(string $password, int|string $userId): bool
    {
        if ('' === trim($password) || 1 > (int) $userId) {
            return false;
        }

        $user = User::get((int) $userId);

        return null !== $user && Login::passwordVerify($password, $user->getPassword());
    }

    public static function setUser(User $me): void
    {
        Login::startSession();
        self::setSessionVar('UID', $me->getId());
        self::$me = $me;
    }

    public static function getUser(): ?User
    {
        return self::$me;
    }

    /**
     * Opens a frontend session for a user from the backend ("log in as this user").
     *
     * REDAXO 6 keeps backend and frontend session data in separate namespaces of the same http
     * session (`<instanceId>_backend` vs `<instanceId>`), so a backend request cannot use
     * {@see self::setUser()} — `Request::setSession()` would write into the backend namespace and
     * the frontend would never see it. REDAXO 5 had a single namespace and got away with it.
     */
    public static function createSessionForUser(User $user): void
    {
        Login::startSession();

        $namespace = Core::getInstanceId();

        $sessionVars = $_SESSION[$namespace][self::$sessionKey] ?? [];
        $sessionVars = is_array($sessionVars) ? $sessionVars : [];
        $sessionVars['UID'] = $user->getId();

        $_SESSION[$namespace][self::$sessionKey] = $sessionVars;

        UserSession::getInstance()->storeCurrentSession($user);
    }

    public static function deleteUser(int $id): bool
    {
        return User::query()->where('id', $id)->find()->delete();
    }

    /**
     * Evaluates the `ycom_auth_type` of an article or category.
     *
     * Called from the `ART_IS_PERMITTED` / `CAT_IS_PERMITTED` extension points, so `$permitted`
     * is whatever a previously registered listener decided.
     */
    public static function articleIsPermitted(Article|Category $article, bool $permitted = true): bool
    {
        if (!$permitted) {
            return false;
        }

        $me = self::getUser();
        $permType = (int) $article->getValue('ycom_auth_type');
        $granted = null;

        if (3 === $permType) {
            // 3 - everybody
            $granted = true;
        } elseif (1 > $permType) {
            // 0 - inherit from the parent; without a parent nothing restricts it
            $parent = self::getParentOf($article);
            if (null !== $parent) {
                return $parent->isPermitted();
            }
            $granted = true;
        } elseif (2 === $permType) {
            // 2 - only while *not* logged in
            $granted = null === $me;
        } elseif (1 === $permType && null === $me) {
            // 1 - only while logged in; group checks happen in the extension point below
            $granted = false;
        }

        $granted ??= true;

        return (bool) Extension::dispatch(new ExtensionPoint('YCOM_AUTH_USER_CHECK', $granted, [
            'article' => $article,
            'me' => $me,
        ]));
    }

    /**
     * The parent of an article or category.
     *
     * Mirrors core's `StructureElement::getParent()`, which is `protected` — a start article
     * inherits from the category *above* its own, everything else from its own category.
     */
    private static function getParentOf(Article|Category $element): ?Category
    {
        if ($element instanceof Category) {
            return $element->getParent();
        }

        $category = $element->getCategory();

        return $element->isStartArticle() ? $category?->getParent() : $category;
    }

    public static function clearUserSession(): void
    {
        self::$me = null;
        UserSession::getInstance()
            ->clearCurrentSession()
            ->clearExpiredSessions();
        self::unsetSessionVars();
    }

    public static function setSessionVar(string $key, mixed $value): void
    {
        $sessionVars = Request::session(self::$sessionKey, 'array', []);
        $sessionVars[$key] = $value;
        Request::setSession(self::$sessionKey, $sessionVars);
    }

    public static function getSessionVar(string $key, string $varType = 'string', mixed $default = ''): mixed
    {
        $sessionVars = Request::session(self::$sessionKey, 'array', []);

        if (array_key_exists($key, $sessionVars)) {
            return Type::cast($sessionVars[$key], $varType);
        }

        if ('' === $default) {
            return Type::cast($default, $varType);
        }

        return $default;
    }

    public static function unsetSessionVar(string $key): void
    {
        $sessionVars = Request::session(self::$sessionKey, 'array', []);
        unset($sessionVars[$key]);
        Request::setSession(self::$sessionKey, $sessionVars);
    }

    public static function unsetSessionVars(): void
    {
        Request::setSession(self::$sessionKey, []);
    }

    /**
     * Reduces a referer URL to a path (plus query) on a domain this installation actually serves,
     * so `returnTo` cannot be used to bounce a visitor to a foreign host.
     */
    public static function cleanReferer(string $refererUrl): string
    {
        if ('' === $refererUrl) {
            return '';
        }

        $url = parse_url($refererUrl) ?: [];
        $returnUrl = '';

        if (isset($url['host'])) {
            $domains = YRewrite::getDomains();

            if (array_key_exists($url['host'], $domains)) {
                $returnUrl .= YRewrite::isHttps() ? 'https://' : 'http://';
                $returnUrl .= $domains[$url['host']]->getHost();
            }
        }

        if (isset($url['path']) && '' !== $url['path']) {
            $returnUrl .= $url['path'];
        }

        if (isset($url['query']) && '' !== $url['query']) {
            $returnUrl .= '?' . $url['query'];
        }

        // Never return to the logout page — that would log the user straight out again.
        $articleIdLogout = (int) Config::get('article_id_logout');
        if (0 < $articleIdLogout && str_contains($returnUrl, Url::article($articleIdLogout))) {
            return '';
        }

        return $returnUrl;
    }

    /**
     * Picks the first of `$returnTos` that points at an allowed domain.
     *
     * @param array<string> $returnTos candidates, in order of precedence
     * @param array<string> $allowedDomains additional allowed domains; the yrewrite domains are always allowed
     */
    public static function getReturnTo(array $returnTos, array $allowedDomains): string
    {
        $absolute = [];
        foreach ($returnTos as $returnTo) {
            if ('' === $returnTo) {
                continue;
            }

            if (!preg_match('@^https?://@i', $returnTo)) {
                $frontendUrl = Url::frontend();
                if (str_contains($returnTo, $frontendUrl)) {
                    $returnTo = str_replace($frontendUrl, '/', $returnTo);
                }
                $returnTo = YRewrite::getFullPath(str_starts_with($returnTo, '/') ? substr($returnTo, 1) : $returnTo);
            }

            $absolute[] = $returnTo;
        }

        foreach (YRewrite::getDomains() as $domain) {
            $allowedDomains[] = $domain->getUrl();
        }

        foreach ($absolute as $candidate) {
            if (1 > count($allowedDomains)) {
                return $candidate;
            }

            foreach ($allowedDomains as $allowedDomain) {
                if (substr($candidate, 0, strlen($allowedDomain)) === $allowedDomain) {
                    return $candidate;
                }
            }
        }

        return '';
    }

    public static function getStayLoggedInCookieName(): string
    {
        $instname = Core::getProperty('instname');

        if (!$instname) {
            throw new RuntimeException('Property "instname" is empty');
        }

        return sha1('rex_ycom_user_' . $instname);
    }

    private static function setStayLoggedInCookie(string $cookieKey): void
    {
        $sessionConfig = Core::getProperty('session', [])['frontend']['cookie'] ?? [];

        Response::sendCookie(self::getStayLoggedInCookieName(), $cookieKey, [
            'expires' => strtotime('+1 year'),
            'secure' => $sessionConfig['secure'] ?? false,
            'samesite' => $sessionConfig['samesite'] ?? 'lax',
        ]);
    }

    private static function deleteStayLoggedInCookie(): void
    {
        Response::clearCookie(self::getStayLoggedInCookieName());
    }

    /**
     * The request details a failed login is logged with.
     *
     * REDAXO 5 dumped all of `$_SERVER` and `$_REQUEST` here, which put session cookies,
     * authorization headers and the submitted password into a plain-text log file. Only the
     * fields that actually help investigating a failed login are recorded.
     */
    private static function requestContext(): array
    {
        return [
            'REMOTE_ADDR' => Request::server('REMOTE_ADDR', 'string', ''),
            'HTTP_USER_AGENT' => Request::server('HTTP_USER_AGENT', 'string', ''),
            'REQUEST_URI' => Request::server('REQUEST_URI', 'string', ''),
            'HTTP_REFERER' => Request::server('HTTP_REFERER', 'string', ''),
        ];
    }
}
