<?php

namespace Yakamara\YCom\YForm\Value;

use Override;
use Yakamara\YCom\User;
use Yakamara\YForm\Attribute\AsValue;
use Yakamara\YForm\Value\AbstractValue;

use function stripslashes;
use function trim;

use function Redaxo\Core\View\escape;

/**
 * Writes a column of the logged-in user into the form — used to stamp a submission with the user.
 *
 * `ycom_user|name|dbfield|Fieldlabel|hidden|[no_db]|showlabel`
 */
#[AsValue('ycom_user')]
class YComUser extends AbstractValue
{
    #[Override]
    public function enterObject()
    {
        $showValue = '';
        $showLabel = (string) $this->getElement(6);

        $this->setValue(-1);

        $me = User::getMe();
        if (null !== $me) {
            $this->setValue($me->getValue((string) $this->getElement(2)));
            $showValue = $me->getValue($showLabel);
        }

        $warningClass = $this->params['warning'][$this->getId()] ?? '';

        if ('hidden' !== trim((string) $this->getElement(4))) {
            $this->params['form_output'][$this->getId()] = '
                <p class="formtext">
                    <label class="text ' . escape($warningClass) . '" for="' . $this->getFieldId() . '">' . escape($this->getElement(3)) . '</label>
                    <input type="text" class="text inp_disabled" disabled="disabled" id="' . $this->getFieldId() . '" value="' . escape((string) $showValue) . '" />
                </p>';
        }

        $this->params['value_pool']['email'][$this->getElement(1)] = stripslashes((string) $this->getValue());

        if ('no_db' !== $this->getElement(5)) {
            $this->params['value_pool']['sql'][$this->getElement(1)] = $this->getValue();
        }
    }

    #[Override]
    public function getDescription(): string
    {
        return 'ycom_user -> Beispiel: ycom_user|label|dbfield|Fieldlabel|hidden|[no_db]|showlabel';
    }
}
