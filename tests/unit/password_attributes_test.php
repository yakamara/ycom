<?php

use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class rex_ycom_password_attributes_test extends TestCase
{
    public function testPasswordFieldContainsPolicyAttributes(): void
    {
        $yform = new rex_yform();
        $yform->setObjectparams('form_action', '/');
        $yform->setObjectparams('real_field_names', true);
        $yform->setObjectparams('csrf_protection', false);
        $yform->setValueField('ycom_auth_password', ['password', 'Passwort', '{"length":{"min":10},"digit":{"min":1}}']);

        $html = $yform->getForm();

        self::assertStringContainsString('name="password"', $html);
        self::assertStringContainsString('minlength="10"', $html);
        self::assertStringContainsString('passwordrules="required: digit', $html);
    }
}
