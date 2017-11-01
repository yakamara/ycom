<?php

class rex_yform_action_ycom_auth_db extends rex_yform_action_abstract
{
    public function execute()
    {
        $user = rex_ycom_auth::getUser();
        $action = '';
        if (!rex::isBackend() && !$user) {
            echo 'error - access denied - user not logged in';
        } else {
            switch ($this->getElement(2)) {
                case 'logout':
                    rex_ycom_auth::clearUserSession();
                    $action = 'logout';
                    break;

                case 'delete':
                    rex_ycom_auth::deleteUser($user->getValue('id'));
                    rex_ycom_auth::clearUserSession();
                    $action = 'delete';
                    break;

                case 'update':
                default:
                    $sql = rex_sql::factory();
                    if ($this->params['debug']) {
                        $sql->setDebug();
                    }

                    $sql->setTable(rex_ycom_user::table());
                    foreach ($this->params['value_pool']['sql'] as $key => $value) {
                        $sql->setValue($key, $value);
                    }
                    $sql->setWhere('id='.$user->getValue('id').'');
                    $sql->update();
                    $action = 'update';
                    break;
            }
        }

        rex_extension::registerPoint(new rex_extension_point('REX_YCOM_YFORM_SAVED', $sql,
            [
                'form' => $this,
                'sql' => $sql,
                'table' => rex_ycom_user::table(),
                'action' => $action,
                'id' => $this->params['main_id'],
                'yform' => true,
            ]
        ));
    }

    public function getDescription()
    {
        return 'action|ycom_auth_db|update(default)/delete/logout';
    }
}
