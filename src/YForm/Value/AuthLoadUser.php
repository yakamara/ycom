<?php

namespace Yakamara\YCom\YForm\Value;

use Override;
use Yakamara\YCom\Auth\Auth;
use Yakamara\YForm\Attribute\AsValue;
use Yakamara\YForm\Value\AbstractValue;

use function count;
use function explode;
use function in_array;

/**
 * Prefills the form's fields from the logged-in user — the basis of every profile form.
 *
 * Without a field list every field whose name matches a user column is filled.
 *
 * `ycom_auth_load_user|label|opt:field1,field2,field3`
 */
#[AsValue('ycom_auth_load_user')]
class AuthLoadUser extends AbstractValue
{
    #[Override]
    public function enterObject()
    {
        $user = Auth::getUser();

        // Only on first display — after a submit the user's own input has to survive.
        if (null === $user || $this->params['send']) {
            return;
        }

        $fieldList = (string) $this->getElement(2);
        $fields = '' === $fieldList ? [] : explode(',', $fieldList);

        foreach ($this->params['values'] as $value) {
            if ($value->getName() === $this->getName()) {
                continue;
            }

            if (1 > count($fields) || in_array($value->getName(), $fields, true)) {
                if ($user->hasValue($value->getName())) {
                    $value->setValue($user->getValue($value->getName()));
                }
            }
        }
    }

    #[Override]
    public function getDescription(): string
    {
        return 'ycom_auth_load_user -> Beispiel: ycom_auth_load_user|label|opt:field1,field2,field3';
    }
}
