<?php

namespace Yakamara\YCom\YForm\Value;

use Override;
use Redaxo\Core\Core;
use Yakamara\YCom\Auth\Auth;
use Yakamara\YForm\Attribute\AsValue;
use Yakamara\YForm\Value\AbstractValue;

/**
 * Points a form at the logged-in user's own row, so the rest of the fields edit their profile.
 *
 * `ycom_user_init|[label]|[name]|error_msg`
 */
#[AsValue('ycom_user_init')]
class YComUserInit extends AbstractValue
{
    #[Override]
    public function enterObject()
    {
        $user = Auth::getUser();

        if (null === $user) {
            $this->params['warning'][$this->getId()] = $this->getElement(3);

            return;
        }

        $this->params['main_table'] = Core::getTable('ycom_user');
        $this->params['main_id'] = $user->getId();
    }

    #[Override]
    public function getDescription(): string
    {
        return 'ycom_user_init -> Beispiel: ycom_user_init|[label]|[name]|error_msg';
    }
}
