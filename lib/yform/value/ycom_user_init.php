<?php

class rex_yform_value_ycom_user_init extends rex_yform_value_abstract
{
    public function enterObject(): void
    {
        if (null !== rex_ycom_auth::getUser()) {
            $this->params['main_table'] = rex::getTablePrefix() . 'ycom_user';
            $this->params['main_id'] = rex_ycom_auth::getUser()->getId();
        } else {
            $this->params['warning'][$this->getId()] = $this->getElement(3);
        }
    }

    public function getDescription(): string
    {
        return 'ycom_user_init -> Beispiel: ycom_user_init|[label]|[name]|error_msg';
    }
}
