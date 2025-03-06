<?php

class rex_ycom_config
{
    private static ?array $config = null;

    private function __construct() {}

    public static function init()
    {
        if (null !== self::$config) {
            return self::$config;
        }

        self::$config = rex_plugin::get('ycom', 'auth')->getConfig() ?? [];
        self::$config = rex_extension::registerPoint(new rex_extension_point('YCOM_CONFIG', self::$config));

        return self::$config;
    }

    public static function get(string $key, $default = null): mixed
    {
        self::init();
        return self::$config[$key] ?? $default;
    }
}
