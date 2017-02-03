<?php

class rex_yform_action_ycom_auth_db extends rex_yform_action_abstract
{
    public function execute()
    {
        $user = rex_ycom_auth::getUser();
        if (!rex::isBackend() && !$user) {
            echo 'error - access denied - user not logged in';
        } else {
            switch ($this->getElement(2)) {
                case 'logout':
                    rex_ycom_auth::clearUserSession();
                    break;

                case 'delete':
                    rex_ycom_auth::deleteUser($user->getValue('id'));
                    rex_ycom_auth::clearUserSession();
                    break;

                case 'update':
                default:
                    $sql = rex_sql::factory();
                    if ($this->params['debug']) {
                        $sql->debugsql = true;
                    }

                    $sql->setTable(rex_ycom_user::getTable());
                    foreach ($this->params['value_pool']['sql'] as $key => $value) {
                        $sql->setValue($key, $value);
                    }
                    $sql->setWhere('id='.$user->getValue('id').'');
                    $sql->update();
                    break;
            }
        }
    }

    public function getDescription()
    {
        return 'action|ycom_auth_db|update(default)/delete/logout';
    }
}
