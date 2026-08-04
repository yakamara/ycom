<?php

namespace Yakamara\YCom\YForm\Value;

use Override;
use Redaxo\Core\Core;
use Redaxo\Core\Filesystem\Url;
use Redaxo\Core\Http\Request;
use Redaxo\Core\Http\Response;
use Yakamara\YCom\Auth\Auth;
use Yakamara\YCom\Config;
use Yakamara\YForm\Attribute\AsValue;
use Yakamara\YForm\Value\AbstractValue;

use function explode;

/**
 * Logs the current user out as soon as the form containing this field is rendered.
 *
 * `ycom_auth_logout|label|[allowed domains: DomainA,DomainB]|[returnTo]`
 */
#[AsValue('ycom_auth_logout')]
class AuthLogout extends AbstractValue
{
    #[Override]
    public function enterObject()
    {
        if (Core::isBackend()) {
            return;
        }

        $me = Auth::getUser();

        if (null === $me) {
            return;
        }

        $returnTos = [
            Request::request('returnTo', 'string', ''),
            (string) $this->getElement(3),
            Url::article((int) Config::get('article_id_jump_logout')),
        ];

        $allowedDomains = '' !== (string) $this->getElement(2) ? explode(',', (string) $this->getElement(2)) : [];
        $returnTo = Auth::getReturnTo($returnTos, $allowedDomains);

        Auth::logout($me);

        if ('' !== $returnTo) {
            Response::sendCacheControl();
            Response::sendRedirect($returnTo);
        }
    }

    #[Override]
    public function getDescription(): string
    {
        return 'ycom_auth_logout -> Beispiel: ycom_auth_logout|label|[allowed domains: DomainA,DomainB]|[returnTo]';
    }
}
