<?php

class rex_yform_value_ycom_auth_form_logout extends rex_yform_value_abstract
{
    public function enterObject()
    {
        if (!rex::isBackend()) {
            ob_end_clean();
            ob_end_clean();
            header('Location:'.rex_getUrl(rex_config::get('ycom', 'article_logout'), '', [rex_config::get('ycom', 'auth_request_logout') => 1], '&'));
            exit;
        }
    }

    public function getDescription()
    {
        return 'ycom_auth_form_logout -> Beispiel: ycom_auth_form_logout|label|';
    }
}
