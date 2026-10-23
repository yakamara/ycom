<?php

use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class rex_ycom_user_token_duration_test extends TestCase
{
    private string $email = '';

    protected function setUp(): void
    {
        $this->email = 'token-duration-' . bin2hex(random_bytes(4)) . '@example.org';
        rex_ycom_auth::$me = null;
    }

    protected function tearDown(): void
    {
        rex_sql::factory()->setQuery('DELETE FROM ' . rex::getTable('ycom_user_token') . ' WHERE email = ?', [$this->email]);
        unset($_REQUEST['send'], $_POST['send'], $_REQUEST['email'], $_POST['email']);
    }

    public function testDefaultDurationIsOneDay(): void
    {
        self::assertSame(24 * 60 * 60, $this->createTokenAndGetDuration('ycom_user_token|token|create|login|email'));
    }

    public function testDurationCanBeSetInFieldDefinition(): void
    {
        self::assertSame(3600, $this->createTokenAndGetDuration('ycom_user_token|token|create|login|email|3600'));
    }

    private function createTokenAndGetDuration(string $tokenField): int
    {
        $_REQUEST['send'] = $_POST['send'] = '1';
        $_REQUEST['email'] = $_POST['email'] = $this->email;

        $yform = new rex_yform();
        $yform->setObjectparams('form_action', '/');
        $yform->setObjectparams('real_field_names', true);
        $yform->setObjectparams('csrf_protection', false);
        $yform->setFormData($tokenField . "\ntext|email|E-Mail");
        $yform->getForm();

        $token = rex_sql::factory()->getArray(
            'SELECT createdate, expiredate FROM ' . rex::getTable('ycom_user_token') . ' WHERE email = ?',
            [$this->email],
        );
        self::assertCount(1, $token);

        return strtotime($token[0]['expiredate']) - strtotime($token[0]['createdate']);
    }
}
