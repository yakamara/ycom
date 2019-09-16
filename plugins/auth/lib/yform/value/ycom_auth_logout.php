<?php

class rex_yform_value_ycom_auth_logout extends rex_yform_value_abstract
{
    public function enterObject()
    {
        if (!rex::isBackend()) {
            rex_response::cleanOutputBuffers();
            rex_ycom_auth::clearUserSession();

            $url = ('' != $this->getElement(2)) ? $this->getElement(2) : rex_getUrl(rex_config::get('ycom/auth', 'article_id_jump_logout'), '', [], '&');
            rex_response::sendRedirect($url);
        }
    }

    public function getDescription()
    {
        return 'ycom_auth_logout -> Beispiel: ycom_auth_logout|label|[returnTo]';
    }
}
