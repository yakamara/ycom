<?php

class rex_yform_action_ycom_auth_db extends rex_yform_action_abstract
{
    public function execute()
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
                    $action = 'update';
                    foreach ($this->params['value_pool']['sql'] as $key => $value) {
                        $user->setValue($key, $value);
                    }
                    $user->save();
                    break;
            }

        rex_extension::registerPoint(new rex_extension_point('YCOM_YFORM_SAVED', '',
            [
                'form' => $this,
                'user' => $user,
                'action' => $action,
            ]
        ));
    }

    public function getDescription()
    {
        return 'action|ycom_auth_db|update(default)/delete';
    }
}
