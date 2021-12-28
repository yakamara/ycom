<?php

declare(strict_types=1);

class rex_yform_action_ycom_auth_db extends rex_yform_action_db
{
    public function executeAction(): void
    {
        /** @var rex_ycom_user $user */
        $user = rex_ycom_auth::getUser();

        if (rex::isBackend() || !$user) {
            echo 'error - access denied - user not logged in';
            return;
        }

        $action = $this->getElement(2);

        switch ($action) {
            case 'delete':
                rex_ycom_auth::deleteUser($user->getValue('id'));
                rex_ycom_auth::clearUserSession();

                break;
            case 'update':
            default:
                $this->params['main_table'] = rex_ycom_user::table()->getTableName();
                $this->params['main_where'] = 'id='.(int) rex_ycom_user::getMe()->getId();

                $this->setElement(2, '');
                $this->setElement(3, 'main_where');

                parent::executeAction();
        }
    }

    public function getDescription(): string
    {
        return 'action|ycom_auth_db|update(default)/delete';
    }
}
