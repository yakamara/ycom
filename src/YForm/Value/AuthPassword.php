<?php

namespace Yakamara\YCom\YForm\Value;

use Override;
use Redaxo\Core\Security\Login;
use Redaxo\Core\Security\PasswordPolicy;
use Redaxo\Core\Translation\I18n;
use Yakamara\YForm\Attribute\AsValue;
use Yakamara\YForm\Validate\PasswordPolicy as PasswordPolicyValidate;
use Yakamara\YForm\Value\AbstractValue;

use function count;
use function is_array;
use function json_decode;
use function password_get_info;
use function trim;

/**
 * A password field that checks the password policy and stores only the bcrypt hash.
 *
 * The plain password is put into the email value pool (so a registration mail can quote it) but
 * never into the SQL pool.
 *
 * `ycom_auth_password|name|label|[password-rules-as-json]|message|[script 0/1]`
 */
#[AsValue('ycom_auth_password')]
class AuthPassword extends AbstractValue
{
    #[Override]
    public function enterObject()
    {
        $rules = $this->getRules();

        if ('1' == $this->params['send']) {
            $policy = new PasswordPolicy($rules);

            if ('' !== (string) $this->getValue() && true !== $msg = $policy->check((string) $this->getValue())) {
                $this->params['warning'][$this->getId()] = $this->params['error_class'];
                $this->params['warning_messages'][$this->getId()] = '' === trim((string) $this->getElement('message'))
                    ? $msg
                    : $this->getElement('message');
            }
        }

        if (!$this->needsOutput() || !$this->isViewable()) {
            return;
        }

        if (!$this->isEditable()) {
            $this->params['form_output'][$this->getId()] = $this->parse(
                ['ycom_password.view', 'view'],
                ['value' => '***********'],
            );

            return;
        }

        $this->params['form_output'][$this->getId()] = $this->parse(
            ['ycom_password', 'text'],
            [
                'type' => 'password',
                'value' => '',
                'script' => $this->getElement('script'),
                'rules' => $rules,
            ],
        );
    }

    /**
     * Turns the submitted password into a hash for the database.
     *
     * An already-hashed value (an unchanged field, or an import) is passed through untouched, so
     * saving a profile form without entering a new password does not re-hash the hash.
     */
    #[Override]
    public function preAction(): void
    {
        $password = null;
        $hashedValue = null;

        if (isset($this->params['sql_object'])) {
            $hashedValue = $this->params['sql_object']->getValue($this->getName());

            if ('' !== (string) $this->getValue() && $hashedValue !== $this->getValue()) {
                $password = (string) $this->getValue();
            }
        } elseif ('' !== (string) $this->getValue()) {
            $password = (string) $this->getValue();
        }

        if (null !== $password && '' !== $password) {
            $hashInfo = password_get_info($password);

            if ('bcrypt' !== ($hashInfo['algoName'] ?? '')) {
                $hashedValue = Login::passwordHash($password);
            } else {
                // Already a hash — do not put it into the email pool as if it were a password.
                $hashedValue = $password;
                $password = null;
            }
        }

        if (null !== $password && '' !== $password) {
            $this->params['value_pool']['email'][$this->getName()] = $password;
        }

        if (null !== $hashedValue && '' !== $hashedValue) {
            $this->params['value_pool']['sql'][$this->getName()] = $hashedValue;
        }
    }

    /** @return array<string, mixed> */
    private function getRules(): array
    {
        $rules = $this->getElement('rules');
        $rules = is_array($rules) ? $rules : json_decode((string) $rules, true);

        if (!is_array($rules) || 1 > count($rules)) {
            $rules = json_decode(PasswordPolicyValidate::PASSWORD_POLICY_DEFAULT_RULES, true);
        }

        return is_array($rules) ? $rules : [];
    }

    #[Override]
    public function getDescription(): string
    {
        return 'ycom_auth_password -> Beispiel: ycom_auth_password|name|label|[password-rules-as-json]|message|[script 0/1]';
    }

    /** @return array<string, mixed> */
    #[Override]
    public function getDefinitions(): array
    {
        return [
            'type' => 'value',
            'name' => 'ycom_auth_password',
            'values' => [
                'name' => ['type' => 'name', 'label' => 'Feld'],
                'label' => ['type' => 'text', 'label' => 'Bezeichnung'],
                'rules' => [
                    'type' => 'text',
                    'label' => I18n::msg('ycom_validate_password_policy_rules'),
                    'notice' => I18n::msg('yform_validate_password_policy_rules_notice', PasswordPolicyValidate::PASSWORD_POLICY_DEFAULT_RULES),
                ],
                'message' => ['type' => 'text', 'label' => I18n::msg('ycom_validate_password_policy_rules_error_message')],
                'script' => ['type' => 'checkbox', 'label' => I18n::msg('ycom_validate_password_policy_rules_script')],
                'attributes' => [
                    'type' => 'text',
                    'label' => I18n::msg('yform_values_defaults_attributes'),
                    'notice' => I18n::msg('yform_values_defaults_attributes_notice'),
                ],
            ],
            'description' => 'Erzeugt den Hash-Wert des Passwortes',
            'dbtype' => 'varchar(255)',
            'famous' => false,
        ];
    }
}
