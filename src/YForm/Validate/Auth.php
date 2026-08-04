<?php

namespace Yakamara\YCom\YForm\Validate;

use Override;
use Yakamara\YCom\Auth\Auth as YComAuth;
use Yakamara\YForm\Attribute\AsValidate;
use Yakamara\YForm\Validate\AbstractValidate;

use function count;

/**
 * The login check: takes a login field, a password field and an optional "stay logged in" checkbox
 * and logs the user in.
 *
 * `validate|ycom_auth|loginfield|passwordfield|stayfield|warning_message_enterloginpsw|warning_message_login_failed`
 */
#[AsValidate('ycom_auth')]
class Auth extends AbstractValidate
{
    #[Override]
    public function enterObject()
    {
        $loginObject = null;
        $passwordObject = null;
        $stayObject = null;
        $warningObjects = [];

        foreach ($this->getObjects() as $object) {
            if (!$this->isObject($object)) {
                continue;
            }

            if ($object->getName() === $this->getElement(2)) {
                $loginObject = $object;
                $warningObjects[] = $object;
            }
            if ($object->getName() === $this->getElement(3)) {
                $passwordObject = $object;
                $warningObjects[] = $object;
            }
            if ($object->getName() === $this->getElement(4)) {
                $stayObject = $object;
            }
        }

        if (
            null === $loginObject
            || null === $passwordObject
            || '' === (string) $loginObject->getValue()
            || '' === (string) $passwordObject->getValue()
        ) {
            foreach ($warningObjects as $warningObject) {
                $this->params['warning'][$warningObject->getId()] = $this->params['error_class'];
                $this->params['warning_messages'][$warningObject->getId()] = $this->getElement(5);
            }

            YComAuth::clearUserSession();

            return;
        }

        // Another field already failed — do not count this as a login attempt.
        if (0 < count($this->params['warning'])) {
            return;
        }

        $status = YComAuth::login([
            'loginName' => (string) $loginObject->getValue(),
            'loginPassword' => (string) $passwordObject->getValue(),
            'loginStay' => null !== $stayObject && (bool) $stayObject->getValue(),
            'ignorePassword' => false,
            'filter' => ['status > 0'],
        ]);

        if (YComAuth::STATUS_HAS_LOGGED_IN !== $status) {
            foreach ($warningObjects as $warningObject) {
                $this->params['warning'][$warningObject->getId()] = $this->params['error_class'];
                $this->params['warning_messages'][$warningObject->getId()] = $this->getElement(6);
            }
        }
    }

    #[Override]
    public function getDescription(): string
    {
        return 'ycom_auth -> prüft ob login und registriert user, beispiel: validate|ycom_auth|loginfield|passwordfield|stayfield|warning_message_enterloginpsw|warning_message_login_failed';
    }
}
