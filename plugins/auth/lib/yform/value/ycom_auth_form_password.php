<?php

class rex_yform_value_ycom_auth_form_password extends rex_yform_value_abstract
{
    public function enterObject()
    {
        $form_output = $this->parse(['value.ycom_auth_form_password.tpl.php', 'value.password.tpl.php', 'value.text.tpl.php'], ['type' => 'password', 'value' => '']);
        $form_output = str_replace('name="'.$this->getFieldName().'"', 'name="'.rex_config::get('ycom', 'auth_request_psw').'"', $form_output);
        $this->params['form_output'][$this->getId()] = $form_output;
    }

    public function getDescription()
    {
        return 'ycom_auth_form_password -> Beispiel: ycom_auth_form_password|label|Passwort:|Placeholder';
    }
}
