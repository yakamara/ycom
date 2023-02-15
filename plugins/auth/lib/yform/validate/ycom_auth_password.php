<?php

class rex_yform_validate_ycom_auth_password extends rex_yform_validate_abstract
{
    public function enterObject(): void
    {
        $Object = $this->getValueObject();

        $user = rex_ycom_auth::getUser();
        if (null === $user) {
            // no user available -> error
            $this->params['warning'][$Object->getId()] = $this->params['error_class'];
            $this->params['warning_messages'][$Object->getId()] = $this->getElement(3);
            return;
        }

        $status = rex_ycom_auth::checkPassword($Object->getValue(), $user->getId());
        if (!$status) {
            // password wrong
            $this->params['warning'][$Object->getId()] = $this->params['error_class'];
            $this->params['warning_messages'][$Object->getId()] = $this->getElement(3);
        }
    }

    public function getDescription(): string
    {
        return 'ycom_auth_password -> validate|ycom_auth_password|pswfield|warning_message';
    }
}
