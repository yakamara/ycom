<?php

namespace Yakamara\YCom;

use Redaxo\Core\Addon\Addon;
use Redaxo\Core\ExtensionPoint\Extension;
use Redaxo\Core\ExtensionPoint\ExtensionPoint;

use function is_array;

/**
 * Read access to ycom's configuration, filtered through the `YCOM_CONFIG` extension point.
 *
 * In REDAXO 5 these values lived in the config namespace of the `auth` *plugin* (`ycom/auth`).
 * REDAXO 6 has no plugin concept, so everything moved into the addon's own namespace (`ycom`);
 * {@see YComAddon::migratePluginConfig()} carries an existing REDAXO 5 configuration over.
 */
final class Config
{
    /** @var array<string, mixed>|null */
    private static ?array $config = null;

    private function __construct() {}

    /** @return array<string, mixed> */
    public static function init(): array
    {
        if (null !== self::$config) {
            return self::$config;
        }

        $config = Addon::require('ycom')->getConfig();
        $config = is_array($config) ? $config : [];

        $config = Extension::dispatch(new ExtensionPoint('YCOM_CONFIG', $config));

        return self::$config = is_array($config) ? $config : [];
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::init()[$key] ?? $default;
    }

    /**
     * Drops the cached configuration.
     *
     * The settings page writes the configuration and then re-renders it in the same request, so the
     * cache filled while rendering the *previous* state has to go.
     */
    public static function reset(): void
    {
        self::$config = null;
    }
}
