<?php

class rex_ycom_log
{
    public const TYPE_ACCESS = 'access';
    public const TYPE_LOGOUT = 'logout';
    public const TYPE_CLICK = 'click';
    public const TYPE_LOGIN_FAILED = 'login_failed';
    public const TYPE_LOGIN_NOT_FOUND = 'login_not_found';
    public const TYPE_LOGIN_SUCCESS = 'login_success';
    public const TYPE_LOGIN_UPDATED = 'login_updated';
    public const TYPE_LOGIN_DELETED = 'login_deleted';
    public const TYPE_REGISTERD = 'registerd';
    public const TYPE_SESSION_FAILED = 'session_failed';
    public const TYPE_COOKIE_FAILED = 'cookie_failed';
    public const TYPE_IMPERSONATE = 'session_impersonate';

    public const TYPES = [self::TYPE_COOKIE_FAILED, self::TYPE_SESSION_FAILED, self::TYPE_ACCESS, self::TYPE_LOGIN_SUCCESS, self::TYPE_LOGOUT, self::TYPE_LOGIN_UPDATED, self::TYPE_CLICK, self::TYPE_LOGIN_FAILED, self::TYPE_REGISTERD, self::TYPE_LOGIN_DELETED, self::TYPE_LOGIN_NOT_FOUND];
    /** @var null|bool */
    private static $active;
    private static int $maxFileSize = 20000000; // 20 Mb Default

    public static function activate(): void
    {
        $addon = rex_addon::get('ycom');
        if ($addon->isAvailable()) {
            $addon->setConfig('log', 1);
            self::$active = true;
        }
    }

    public static function deactivate(): void
    {
        $addon = rex_addon::get('ycom');
        if ($addon->isAvailable()) {
            $addon->setConfig('log', 0);
            self::$active = false;
        }
    }

    public static function isActive(): bool
    {
        if (null === self::$active) {
            $addon = rex_addon::get('ycom');
            if ($addon->isAvailable()) {
                self::$active = (1 === $addon->getConfig('log')) ? true : false;
            } else {
                self::$active = false;
            }
        }
        return (self::$active) ? true : false;
    }

    public static function logFolder(): string
    {
        return rex_path::addonData('ycom', 'log');
    }

    public static function logFile(): string
    {
        return rex_path::log('ycom_user.log');
    }

    public static function delete(): bool
    {
        return rex_log_file::delete(self::logFile());
    }

    /**
     * @param rex_ycom_user|string|rex_yform_manager_dataset $user
     * @param array<string|int, string|array<string, mixed>> $params
     */
    public static function log($user, string $type = '', array $params = []): void
    {
        if (!self::isActive()) {
            return;
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = (string) $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = (string) $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        $id = '';
        $login = '';
        if (is_string($user)) {
            /** @var string $user */
            $id = '';
            $login = $user;
        } elseif ('rex_ycom_user' == $user::class) {
            /** @var rex_ycom_user $user */
            $id = $user->getId();
            $login = $user->getValue('login');
        }

        $log = new rex_log_file(self::logFile(), self::$maxFileSize);
        $data = [
            $ip,
            $id,
            $login,
            $type,
            (string) json_encode($params),
        ];
        $log->add($data);
    }
}
