<?php

namespace Yakamara\YCom\YForm\Action;

use Override;
use Redaxo\Core\Core;
use Yakamara\YCom\Auth\Auth;
use Yakamara\YCom\Log;
use Yakamara\YForm\Action\Db;
use Yakamara\YForm\Attribute\AsAction;

/**
 * Writes a form back to the logged-in user's own row, or deletes their account.
 *
 * Unlike the plain `db` action this never takes the target row from the form — it is always the
 * current user, so a submitted id cannot be used to edit somebody else's profile.
 *
 * `action|ycom_auth_db|update(default)/delete`
 */
#[AsAction('ycom_auth_db')]
class AuthDb extends Db
{
    #[Override]
    public function executeAction(): void
    {
        $user = Auth::getUser();

        if (Core::isBackend() || null === $user) {
            $this->params['warning_messages'][] = '{{ ycom_error_access_denied }}';
            $this->params['warning'][] = 1;

            return;
        }

        if ('delete' === $this->getElement(2)) {
            Log::log($user, Log::TYPE_LOGIN_DELETED, ['self delete']);
            Auth::deleteUser($user->getId());
            Auth::clearUserSession();

            return;
        }

        Log::log($user, Log::TYPE_LOGIN_UPDATED, ['self update']);

        $this->params['main_table'] = Core::getTable('ycom_user');
        $this->params['main_where'] = 'id=' . $user->getId();

        // Hand the parent action an empty table so it falls back on main_table/main_where above.
        $this->setElement(2, '');
        $this->setElement(3, 'main_where');

        parent::executeAction();
    }

    #[Override]
    public function getDescription(): string
    {
        return 'action|ycom_auth_db|update(default)/delete';
    }
}
