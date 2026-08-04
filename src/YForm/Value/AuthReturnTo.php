<?php

namespace Yakamara\YCom\YForm\Value;

use Override;
use Redaxo\Core\Filesystem\Url;
use Redaxo\Core\Http\Request;
use Redaxo\Core\Http\Response;
use Yakamara\YCom\Auth\Auth;
use Yakamara\YCom\Config;
use Yakamara\YForm\Attribute\AsValue;
use Yakamara\YForm\Value\AbstractValue;

use function explode;

/**
 * Carries the "where did the visitor want to go" target through a login form and redirects there
 * once the form's actions have run.
 *
 * `ycom_auth_returnto|label|[allowed domains: DomainA,DomainB]|[URL]`
 */
#[AsValue('ycom_auth_returnto')]
class AuthReturnTo extends AbstractValue
{
    #[Override]
    public function enterObject()
    {
        $returnTos = [
            (string) $this->getValue(),
            Request::request('returnTo', 'string', ''),
            (string) $this->getElement(3),
            Url::article((int) Config::get('article_id_jump_ok')),
        ];

        $allowedDomains = '' !== (string) $this->getElement(2) ? explode(',', (string) $this->getElement(2)) : [];

        $this->setValue(Auth::getReturnTo($returnTos, $allowedDomains));

        if ($this->needsOutput()) {
            $this->params['form_output'][$this->getId()] = $this->parse('hidden');
        }

        $this->params['value_pool']['email'][$this->getName()] = $this->getValue();
    }

    #[Override]
    public function executeAction(): void
    {
        if ('' === (string) $this->getValue()) {
            return;
        }

        // Response::sendRedirect() rather than setHeader() + `form_exit`: setHeader() only *queues*
        // a header, and yform's `form_exit` path calls cleanOutputBuffers() and exit() without
        // flushing that queue — the redirect would be silently dropped and the visitor would get an
        // empty 200. REDAXO 5 called header() directly, which does not have that problem.
        Response::sendRedirect((string) $this->getValue());
    }

    #[Override]
    public function getDescription(): string
    {
        return 'ycom_auth_returnto|label|[allowed domains: DomainA,DomainB]|[URL]';
    }
}
