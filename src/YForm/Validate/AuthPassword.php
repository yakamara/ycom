<?php

namespace Yakamara\YCom\YForm\Validate;

use Override;
use Yakamara\YCom\Auth\Auth as YComAuth;
use Yakamara\YForm\Attribute\AsValidate;
use Yakamara\YForm\Validate\AbstractValidate;

/**
 * Checks a field against the logged-in user's current password — the "old password" field of a
 * password-change form.
 *
 * `validate|ycom_auth_password|pswfield|warning_message`
 */
#[AsValidate('ycom_auth_password')]
class AuthPassword extends AbstractValidate
{
    #[Override]
    public function enterObject()
    {
        $object = $this->getValueObject();

        if (!$this->isObject($object)) {
            return;
        }

        $user = YComAuth::getUser();

        if (null === $user || !YComAuth::checkPassword((string) $object->getValue(), $user->getId())) {
            $this->params['warning'][$object->getId()] = $this->params['error_class'];
            $this->params['warning_messages'][$object->getId()] = $this->getElement(3);
        }
    }

    #[Override]
    public function getDescription(): string
    {
        return 'ycom_auth_password -> validate|ycom_auth_password|pswfield|warning_message';
    }
}
