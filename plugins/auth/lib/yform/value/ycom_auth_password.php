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

        $this->params['form_output'][$this->getId()] = $this->parse(['value.password.tpl.php', 'value.text.tpl.php'], ['type' => 'password', 'value' => '']);
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
        return 'ycom_auth_password -> Beispiel: ycom_auth_password|name|label';
    }

    public function getDefinitions()
    {
        return [
                        'type' => 'value',
                        'name' => 'ycom_auth_password',
                        'values' => [
                                    'name' => ['type' => 'name',    'label' => 'Feld'],
                                    'label' => ['type' => 'text',    'label' => 'Bezeichnung'],
                                ],
                        'description' => 'Erzeugt den Hash-Wert des Passwortes',
                        'dbtype' => 'varchar(255)',
                        'famous' => false,
        ];
    }
}
