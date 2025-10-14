<?php

use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class rex_ycom_user_status_test extends TestCase
{
    public function testStatusConstantsExist()
    {
        // Test that all status constants are defined
        self::assertTrue(defined('rex_ycom_user::STATUS_INACTIVE_TERMINATION'));
        self::assertTrue(defined('rex_ycom_user::STATUS_INACTIVE_LOGINS'));
        self::assertTrue(defined('rex_ycom_user::STATUS_INACTIVE'));
        self::assertTrue(defined('rex_ycom_user::STATUS_REQUESTED'));
        self::assertTrue(defined('rex_ycom_user::STATUS_CONFIRMED'));
        self::assertTrue(defined('rex_ycom_user::STATUS_ACTIVE'));
    }

    public function testStatusConstantValues()
    {
        // Test that constants have the correct values
        self::assertEquals(-3, rex_ycom_user::STATUS_INACTIVE_TERMINATION);
        self::assertEquals(-2, rex_ycom_user::STATUS_INACTIVE_LOGINS);
        self::assertEquals(-1, rex_ycom_user::STATUS_INACTIVE);
        self::assertEquals(0, rex_ycom_user::STATUS_REQUESTED);
        self::assertEquals(1, rex_ycom_user::STATUS_CONFIRMED);
        self::assertEquals(2, rex_ycom_user::STATUS_ACTIVE);
    }

    public function testDefaultStatusOptions()
    {
        // Test that default status options contain all expected entries
        $defaultOptions = rex_ycom_user::DEFAULT_STATUS_OPTIONS;
        
        self::assertIsArray($defaultOptions);
        self::assertArrayHasKey(-3, $defaultOptions);
        self::assertArrayHasKey(-2, $defaultOptions);
        self::assertArrayHasKey(-1, $defaultOptions);
        self::assertArrayHasKey(0, $defaultOptions);
        self::assertArrayHasKey(1, $defaultOptions);
        self::assertArrayHasKey(2, $defaultOptions);
        
        // Test that values are translation keys
        self::assertEquals('translate:ycom_account_inactive_termination', $defaultOptions[-3]);
        self::assertEquals('translate:ycom_account_inactive_logins', $defaultOptions[-2]);
        self::assertEquals('translate:ycom_account_inactive', $defaultOptions[-1]);
        self::assertEquals('translate:ycom_account_requested', $defaultOptions[0]);
        self::assertEquals('translate:ycom_account_confirm', $defaultOptions[1]);
        self::assertEquals('translate:ycom_account_active', $defaultOptions[2]);
    }

    public function testGetStatusOptionsReturnsArray()
    {
        // Test that getStatusOptions returns an array
        $options = rex_ycom_user::getStatusOptions();
        
        self::assertIsArray($options);
        self::assertNotEmpty($options);
        
        // Test that it returns the default options
        self::assertEquals(rex_ycom_user::DEFAULT_STATUS_OPTIONS, $options);
    }
}
