<?php

use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class rex_ycom_otp_config_test extends TestCase
{
    private const KEYS = ['otp_auth_option', 'otp_auth_email_period', 'otp_auth_enforce'];

    /** @var array<string, array<string, mixed>> */
    private array $backup = [];

    protected function setUp(): void
    {
        // the settings page stores into ycom/auth - values in the addon namespace must not win
        foreach (['ycom/auth', 'ycom'] as $namespace) {
            foreach (self::KEYS as $key) {
                $this->backup[$namespace][$key] = rex_config::get($namespace, $key);
            }
        }
        rex_config::set('ycom', 'otp_auth_option', rex_ycom_otp_password::OPTION_EMAIL);
        rex_config::set('ycom', 'otp_auth_enforce', rex_ycom_otp_password::ENFORCED_DISABLED);
        $this->resetConfigCache();
    }

    protected function tearDown(): void
    {
        foreach ($this->backup as $namespace => $values) {
            foreach ($values as $key => $value) {
                null === $value ? rex_config::remove($namespace, $key) : rex_config::set($namespace, $key, $value);
            }
        }
        $this->resetConfigCache();
    }

    public function testAuthOptionIsReadFromSettings(): void
    {
        rex_plugin::get('ycom', 'auth')->setConfig('otp_auth_option', rex_ycom_otp_password::OPTION_TOTP);
        $this->resetConfigCache();

        self::assertSame(rex_ycom_otp_password::OPTION_TOTP, rex_ycom_otp_password::getInstance()->getAuthOption());
    }

    public function testEnforceIsReadFromSettings(): void
    {
        rex_plugin::get('ycom', 'auth')->setConfig('otp_auth_enforce', rex_ycom_otp_password::ENFORCED_ALL);
        $this->resetConfigCache();

        self::assertSame(rex_ycom_otp_password::ENFORCED_ALL, rex_ycom_otp_password::getInstance()->isEnforced());
    }

    public function testEmailPeriodIsReadFromSettings(): void
    {
        rex_plugin::get('ycom', 'auth')->setConfig('otp_auth_email_period', 900);
        $this->resetConfigCache();

        self::assertSame(900, rex_ycom_otp_method_email::getPeriod());
    }

    private function resetConfigCache(): void
    {
        $property = new ReflectionProperty(rex_ycom_config::class, 'config');
        $property->setValue(null, null);
    }
}
