<?php

class rex_yform_value_ycom_auth_form_logout extends rex_yform_value_abstract
{
    public function enterObject()
    {
        if (!rex::isBackend()) {
            rex_response::cleanOutputBuffers();
            rex_ycom_auth::clearUserSession();
            header('Location:'.rex_getUrl(rex_config::get('ycom/auth', 'article_id_jump_logout'), '', [], '&'));
            exit;
        }
    }

    public function getDescription()
    {
        return 'ycom_auth_form_logout -> Beispiel: ycom_auth_form_logout|label|';
    }
}
