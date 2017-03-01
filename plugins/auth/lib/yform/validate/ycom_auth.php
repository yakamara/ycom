<?PHP

class rex_yform_validate_ycom_auths extends rex_yform_validate_abstract
{
    public function postValueAction()
    {
        $login = '';
        $psw = '';
        $stay = '';

        foreach ($this->params['value_pool']['sql'] as $k => $v) {
            if ($k == $this->getElement(2)) {
                $login = $v;
            } elseif ($k == $this->getElement(3)) {
                $psw = $v;
            } elseif ($k == $this->getElement(4)) {
                $stay = $v;
            }
        }

        if ($login == '' || $psw == '') {
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

        $status = rex_ycom_auth::login($login, $psw, $stay, false); // no logout

        if ($status != 2) {
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
