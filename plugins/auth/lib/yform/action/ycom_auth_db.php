<?php

declare(strict_types=1);

class rex_yform_action_ycom_auth_db extends rex_yform_action_db
{
    public function executeAction(): void
    {
        /** @var rex_ycom_user|null $user */
        $user = rex_ycom_auth::getUser();

        if (rex::isBackend() || !$user) {
            echo 'error - access denied - user not logged in';
            return;
        }

        $action = $this->getElement(2);

        switch ($action) {
            case 'delete':
                rex_ycom_log::log($user, rex_ycom_log::TYPE_LOGIN_DELETED, [
                    'self delete',
                ]);
                rex_ycom_auth::deleteUser($user->getValue('id'));
                rex_ycom_auth::clearUserSession();

                break;
            case 'update':
            default:
                rex_ycom_log::log($user, rex_ycom_log::TYPE_LOGIN_UPDATED, [
                    'self update',
                ]);

                $this->params['main_table'] = rex_ycom_user::table()->getTableName();
                $this->params['main_where'] = 'id='.(int) (rex_ycom_user::getMe() ? rex_ycom_user::getMe()->getId() : 0);

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
