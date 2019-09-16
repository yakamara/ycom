<?php

class rex_yform_validate_ycom_auth extends rex_yform_validate_abstract
{
    public function postValueAction()
    {
        $login = '';
        $psw = '';
        $stay = true;

        foreach ($this->params['value_pool']['sql'] as $k => $v) {
            if ($k == $this->getElement(2)) {
                $login = $v;
            } elseif ($k == $this->getElement(3)) {
                $psw = $v;
            } elseif ($k == $this->getElement(4)) {
                if (1 == $v) {
                    $stay = true;
                }
            }
        }

        if ('' == $login || '' == $psw) {
            $this->params['warning'][] = 1;
            $this->params['warning_messages'][] = $this->getElement(5);
            rex_ycom_auth::clearUserSession();
            return;
        }

        /*
          login_status
          0: not logged in
          1: logged in
          2: has logged in
          3: has logged out
          4: login failed
        */

        $params = [];
        $params['loginName'] = $login;
        $params['loginPassword'] = $psw;
        $params['loginStay'] = $stay;

        $status = rex_ycom_auth::login($params);

        if (2 != $status) {
            $this->params['warning'][] = 1;
            $this->params['warning_messages'][] = $this->getElement(6);
            rex_ycom_auth::clearUserSession();
        }
    }

    public function getDescription()
    {
        return 'ycom_auth -> prüft ob login und registriert user, beispiel: validate|ycom_auth|loginfield|passwordfield|stayfield|warning_message_enterloginpsw|warning_message_login_failed';
    }
}
