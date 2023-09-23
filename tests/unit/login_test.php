<?php

use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class rex_ycom_login_test extends TestCase
{
    public function testLogin()
    {
        $params = [
            'loginName' => 'admin',
            'loginPassword' => 'admin',
            'filter' => [],
            'loginStay' => false,
            'ignorePassword' => false,
        ];

        $status = rex_ycom_auth::login($params);
        static::assertEquals(rex_ycom_auth::STATUS_LOGIN_FAILED, $status);

    }
}
