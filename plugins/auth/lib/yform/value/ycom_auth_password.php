<?php

class rex_yform_value_ycom_auth_password extends rex_yform_value_abstract
{
    public function enterObject()
    {
        if ($this->params['send']) {
            $placeholder = rex_i18n::translate('translate:ycom_auth_password_not_updated');
            if ($this->getValue() != '') {
                $placeholder = rex_i18n::translate('translate:ycom_auth_password_updated');
            }
        } else {
            $placeholder = rex_i18n::translate('translate:ycom_auth_password_exists');
            if ($this->getValue() == '') {
                $placeholder = rex_i18n::translate('translate:ycom_auth_password_isempty');
            }
        }

        if ($this->getElement('placeholder') == '') {
            $this->setElement('placeholder', $placeholder);
        }

        $rules = json_decode($this->getElement('rules'), true);
        if (count($rules) == 0) {
            $rules = json_decode(rex_yform_validate_password_policy::PASSWORD_POLICY_DEFAULT_RULES, true);
        }

        if ($this->params['send'] == '1') {
            $PasswordPolicy = new rex_password_policy($rules);

            if ($this->getValue() != '' && $PasswordPolicy->check($this->getValue()) !== true) {
                $this->params['warning'][$this->getId()] = $this->params['error_class'];
                $this->params['warning_messages'][$this->getId()] = $this->getElement('message');
            }

            $value = $this->getValue();
        } else {
            $value = '';
        }

        $this->params['form_output'][$this->getId()] = $this->parse(['value.ycom_password.tpl.php', 'value.password.tpl.php', 'value.text.tpl.php'], ['type' => 'password', 'value' => $value, 'script' => $this->getElement('script'), 'rules' => $rules]);
    }

    public function preAction()
    {
        $password = '';
        $hashed_value = '';

        if (isset($this->params['sql_object'])) {
            $hashed_value = $this->params['sql_object']->getValue($this->getName());

            if ($this->getValue() == '') {
                // kein neuer wert
                $password = '';
            } elseif ($hashed_value == $this->getValue()) {
            } else {
                $password = $this->getValue();
            }
        } elseif ($this->getValue() != '') {
            $password = $this->getValue();
        }
        if ($password != '') {
            $hash_info = password_get_info($password);
            if (!isset($hash_info['algoName']) || $hash_info['algoName'] != 'bcrypt') {
                $hashed_value = rex_login::passwordHash($password);
            } else {
                $hashed_value = $password;
                $password = '';
            }
        }

        self::enterObject();
        $this->params['value_pool']['sql'][$this->getName()] = $hashed_value;
        $this->params['value_pool']['email'][$this->getName()] = $password;
    }

    public function getDescription()
    {
        return 'ycom_auth_password -> Beispiel: ycom_auth_password|name|label|[password-rules-as-json]|message|[script 0/1]';
    }

    public function getDefinitions()
    {
        return [
                        'type' => 'value',
                        'name' => 'ycom_auth_password',
                        'values' => [
                                    'name' => ['type' => 'name',    'label' => 'Feld'],
                                    'label' => ['type' => 'text',   'label' => 'Bezeichnung'],
                                    'rules' => ['type' => 'text', 'label' => rex_i18n::msg('ycom_validate_password_policy_rules'), 'notice' => rex_i18n::msg('yform_validate_password_policy_rules_notice', rex_yform_validate_password_policy::PASSWORD_POLICY_DEFAULT_RULES)],
                                    'message' => ['type' => 'text', 'label' => rex_i18n::msg('ycom_validate_password_policy_rules_error_message')],
                                    'script' => ['type' => 'checkbox', 'label' => rex_i18n::msg('ycom_validate_password_policy_rules_script')],
                                ],
                        'description' => 'Erzeugt den Hash-Wert des Passwortes',
                        'dbtype' => 'varchar(255)',
                        'famous' => false,
        ];
    }
}
