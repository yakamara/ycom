<?php

class rex_yform_value_ycom_auth_form_info extends rex_yform_value_abstract
{
    public function enterObject()
    {
        $login_name = rex_request(rex_config::get('ycom', 'auth_request_name'), 'string');
        $referer = rex_request(rex_config::get('ycom', 'auth_request_ref'), 'string');
        $logout = rex_request(rex_config::get('ycom', 'auth_request_logout'), 'int');

        $referer = rex_ycom_auth::cleanReferer($referer);

        $state = 'info';
        if ($logout) {
            $msg = $this->getElement(3);
        }

        if ($login_name) {
            $msg = $this->getElement(4);
            $state = 'error';
        } elseif ($referer && rex_ycom_auth::getUser()) {
            $msg = $this->getElement(5);
            $state = 'error';
        }

        if (!isset($msg)) {
            $msg = $this->getElement(2);
        }

        if ($msg) {
            $parse = $this->parse(['value.ycom_auth_form_info.tpl.php'], ['message' => $msg, 'state' => $state]);
            $msg = $parse == '' ? $msg : $parse;
            $this->params['form_output'][$this->getId()] = $msg;
        }
    }

    public function getDescription()
    {
        return 'ycom_auth_form_info -> Beispiel: ycom_auth_form_info|label|login_msg|logout_msg|wronglogin_msg|accessdenied_msg';
    }
}
