<?php

namespace Yakamara\YCom;

use Redaxo\Core\Addon\Addon;
use Redaxo\Core\Filesystem\Path;
use Redaxo\Core\Http\Request;
use Redaxo\Core\Log\LogFile;
use Yakamara\YForm\Manager\Dataset;

use function is_string;
use function json_encode;

/**
 * The frontend user activity log, written to `var/log/ycom_user.log`.
 *
 * Off by default; the backend log page toggles it via the `log` config key.
 */
final class Log
{
    public const string TYPE_ACCESS = 'access';
    public const string TYPE_LOGOUT = 'logout';
    public const string TYPE_CLICK = 'click';
    public const string TYPE_LOGIN_FAILED = 'login_failed';
    public const string TYPE_LOGIN_NOT_FOUND = 'login_not_found';
    public const string TYPE_LOGIN_SUCCESS = 'login_success';
    public const string TYPE_LOGIN_UPDATED = 'login_updated';
    public const string TYPE_LOGIN_DELETED = 'login_deleted';
    public const string TYPE_REGISTERD = 'registerd';
    public const string TYPE_SESSION_FAILED = 'session_failed';
    public const string TYPE_COOKIE_FAILED = 'cookie_failed';
    public const string TYPE_IMPERSONATE = 'session_impersonate';

    /** @var list<string> */
    public const array TYPES = [
        self::TYPE_COOKIE_FAILED,
        self::TYPE_SESSION_FAILED,
        self::TYPE_ACCESS,
        self::TYPE_LOGIN_SUCCESS,
        self::TYPE_LOGOUT,
        self::TYPE_LOGIN_UPDATED,
        self::TYPE_CLICK,
        self::TYPE_LOGIN_FAILED,
        self::TYPE_REGISTERD,
        self::TYPE_LOGIN_DELETED,
        self::TYPE_LOGIN_NOT_FOUND,
    ];

    private const int MAX_FILE_SIZE = 20_000_000; // 20 MB

    private static ?bool $active = null;

    private function __construct() {}

    public static function activate(): void
    {
        Addon::require('ycom')->setConfig('log', 1);
        self::$active = true;
    }

    public static function deactivate(): void
    {
        Addon::require('ycom')->setConfig('log', 0);
        self::$active = false;
    }

    public static function isActive(): bool
    {
        if (null === self::$active) {
            $addon = Addon::get('ycom');
            self::$active = null !== $addon && $addon->isActivated() && 1 === (int) $addon->getConfig('log');
        }

        return self::$active;
    }

    public static function logFile(): string
    {
        return Path::log('ycom_user.log');
    }

    public static function delete(): bool
    {
        return LogFile::delete(self::logFile());
    }

    /**
     * @param User|Dataset|string $user a user object, or a bare login/id when no user could be resolved
     * @param array<int|string, mixed> $params
     */
    public static function log(User|Dataset|string $user, string $type = '', array $params = []): void
    {
        if (!self::isActive()) {
            return;
        }

        $ip = Request::server('REMOTE_ADDR', 'string', '');
        if ('' !== Request::server('HTTP_CLIENT_IP', 'string', '')) {
            $ip = Request::server('HTTP_CLIENT_IP', 'string');
        } elseif ('' !== Request::server('HTTP_X_FORWARDED_FOR', 'string', '')) {
            $ip = Request::server('HTTP_X_FORWARDED_FOR', 'string');
        }

        $id = '';
        $login = '';
        if (is_string($user)) {
            $login = $user;
        } elseif ($user->exists()) {
            $id = (string) $user->getId();
            $login = (string) $user->getValue('login');
        }

        LogFile::factory(self::logFile(), self::MAX_FILE_SIZE)->add([
            $ip,
            $id,
            $login,
            $type,
            (string) json_encode($params),
        ]);
    }
}
