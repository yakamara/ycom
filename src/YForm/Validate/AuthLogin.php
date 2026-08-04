<?php

namespace Yakamara\YCom\YForm\Validate;

use Override;
use Redaxo\Core\Core;
use Redaxo\Core\Http\Request;
use Redaxo\Core\Translation\I18n;
use Yakamara\YCom\Auth\Auth as YComAuth;
use Yakamara\YForm\Attribute\AsValidate;
use Yakamara\YForm\Manager\Query;
use Yakamara\YForm\Validate\AbstractValidate;

use function explode;
use function trim;

/**
 * Logs a user in by matching arbitrary user columns against request parameters — the basis of
 * link-based login (e.g. `?id=…&key=…`).
 *
 * `validate|ycom_auth_login|label1=request1,label2=request2|status>0|warning_message|opt:load_field1,load_field2|[no_db]`
 */
#[AsValidate('ycom_auth_login')]
class AuthLogin extends AbstractValidate
{
    #[Override]
    public function enterObject()
    {
        if (Core::isBackend()) {
            $this->params['warning'][] = 1;
            $this->params['warning_messages'][] = I18n::translate($this->getElement(4));

            return;
        }

        $vars = [];
        foreach (explode(',', (string) $this->getElement(2)) as $pair) {
            $parts = explode('=', $pair);
            if (!isset($parts[1])) {
                continue;
            }
            $vars[$parts[0]] = trim(Request::request($parts[1], 'string', ''));
        }

        $filter = null;
        if ('' !== (string) $this->getElement(3)) {
            $filterQuery = (string) $this->getElement(3);
            $filter = static function (Query $query) use ($filterQuery): void {
                $query->whereRaw($filterQuery);
            };
        }

        YComAuth::loginWithParams($vars, $filter);

        $user = YComAuth::getUser();

        if (null === $user) {
            $this->params['warning'][] = 1;
            $this->params['warning_messages'][] = I18n::translate($this->getElement(4));

            return;
        }

        // Hand the requested user columns on to the email and SQL value pools.
        $fieldList = (string) $this->getElement(5);
        if ('' === $fieldList) {
            return;
        }

        foreach (explode(',', $fieldList) as $field) {
            $this->params['value_pool']['email'][$field] = $user->getValue($field);

            if ('no_db' !== $this->getElement(6)) {
                $this->params['value_pool']['sql'][$field] = $user->getValue($field);
            }
        }
    }

    #[Override]
    public function getDescription(): string
    {
        return 'ycom_auth_login -> prüft ob leer, beispiel: validate|ycom_auth_login|label1=request1,label2=request2|status>0|warning_message|opt:load_field1,load_field2,load_field3|[no_db]';
    }
}
