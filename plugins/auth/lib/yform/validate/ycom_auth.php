<?php

class rex_yform_validate_ycom_auth extends rex_yform_validate_abstract
{
    public function enterObject()
    {
        $loginObject = null;
        $passwordObject = null;
        $stayObject = null;

        $warningObjects = [];
        foreach ($this->getObjects() as $Object) {
            if ($this->isObject($Object)) {
                if ($Object->getName() == $this->getElement(2)) {
                    $loginObject = $Object;
                    $warningObjects[] = $Object;
                }
                if ($Object->getName() == $this->getElement(3)) {
                    $passwordObject = $Object;
                    $warningObjects[] = $Object;
                }
                if ($Object->getName() == $this->getElement(4)) {
                    $stayObject = $Object;
                }
            }
        }

        if ('' == $loginObject->getValue() || '' == $passwordObject->getValue()) {
            foreach ($warningObjects as $warningObject) {
                $this->params['warning'][$warningObject->getId()] = $this->params['error_class'];
                $this->params['warning_messages'][$warningObject->getId()] = $this->getElement(5);
            }
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
        $params['loginName'] = ($loginObject) ? $loginObject->getValue() : '';
        $params['loginPassword'] = ($passwordObject) ? $passwordObject->getValue() : '';
        $params['loginStay'] = ($stayObject) ? $stayObject->getValue() : false;

        $status = rex_ycom_auth::login($params);

        if (2 != $status) {
            foreach ($warningObjects as $warningObject) {
                $this->params['warning'][$warningObject->getId()] = $this->params['error_class'];
                $this->params['warning_messages'][$warningObject->getId()] = $this->getElement(6);
            }
            rex_ycom_auth::clearUserSession();
        }
    }

    public function getDescription()
    {
        return 'ycom_auth -> prüft ob login und registriert user, beispiel: validate|ycom_auth|loginfield|passwordfield|stayfield|warning_message_enterloginpsw|warning_message_login_failed';
    }
}
