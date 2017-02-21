<?php

class rex_yform_value_ycom_auth_form_login extends rex_yform_value_abstract
{
    public function init()
    {
        // adding referer to action for redirects
        $params = [rex_config::get('ycom', 'auth_request_ref') => rex_request(rex_config::get('ycom', 'auth_request_ref'), 'string')];
        $this->params['form_action'] = rex_getUrl('', '', $params);

        // Show form after sending
        $this->params['form_showformafterupdate'] = 1;
    }

    public function enterObject()
    {
        $login = rex_request(rex_config::get('ycom', 'auth_request_name'), 'string');
        $this->setValue($login);

        $this->params['form_output'][$this->getId()] = $this->parse(['value.ycom_auth_form_login.tpl.php', 'value.text.tpl.php']);
        $this->params['form_output'][$this->getId()] = str_replace($this->getFieldName(), rex_config::get('ycom', 'auth_request_name'), $this->params['form_output'][$this->getId()]);
    }

    public function getDescription()
    {
        return 'ycom_auth_form_login -> Beispiel: ycom_auth_form_login|label|Benutzername:|placeholder';
    }
}
