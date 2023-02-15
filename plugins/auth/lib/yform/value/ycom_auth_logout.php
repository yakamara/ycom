<?php

class rex_yform_value_ycom_auth_logout extends rex_yform_value_abstract
{
    public function enterObject(): void
    {
        if (rex::isFrontend()) {
            /** @var rex_ycom_user|null $me */
            $me = rex_ycom_auth::getUser();

            if (rex::isBackend() || null === $me) {
                echo 'error - access denied - user not logged in';
                return;
            }

            $returnTos = [];
            $returnTos[] = rex_request('returnTo', 'string');
            $returnTos[] = (string) $this->getElement(3);
            $returnTos[] = rex_getUrl(rex_config::get('ycom/auth', 'article_id_jump_logout'));

            $allowedDomains = ('' != $this->getElement(2)) ? explode(',', $this->getElement(2)) : [];
            $returnTo = rex_ycom_auth::getReturnTo($returnTos, $allowedDomains);

            rex_ycom_auth::logout($me);

            if ('' != $returnTo) {
                rex_response::sendCacheControl();
                rex_response::sendRedirect($returnTo);
            }
        }
    }

    public function getDescription(): string
    {
        return 'ycom_auth_logout -> Beispiel: ycom_auth_logout|label|[allowed domains: DomainA,DomainB]|[returnTo]';
    }
}
