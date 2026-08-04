<?php

namespace Yakamara\YCom\YForm\Value;

use Override;
use Redaxo\Core\Exception\RuntimeException;
use Redaxo\Core\Filesystem\Url;
use Redaxo\Core\Http\Request;
use Redaxo\Core\Http\Response;
use Throwable;
use Yakamara\YCom\Auth\Auth;
use Yakamara\YCom\Auth\UserToken as Token;
use Yakamara\YCom\Config;
use Yakamara\YCom\User;
use Yakamara\YForm\Action\Redirect;
use Yakamara\YForm\Attribute\AsValue;
use Yakamara\YForm\Value\AbstractValue;

use function count;
use function in_array;

/**
 * Creates or validates a single-use token — the field behind registration confirmation,
 * password-reset and direct-login links.
 *
 * `ycom_user_token|create|[type]`
 * `ycom_user_token|validate|[type]|[error_message]`
 */
#[AsValue('ycom_user_token')]
class UserToken extends AbstractValue
{
    private const array ACTIONS = ['create', 'validate'];
    private const int DEFAULT_DURATION = 60 * 60 * 24;

    private ?string $tokenAction = null;
    private ?string $tokenType = null;
    private ?Token $token = null;
    private int $tokenDuration = self::DEFAULT_DURATION;

    #[Override]
    public function enterObject()
    {
        $this->tokenAction = (string) $this->getElement(2);
        if (!in_array($this->tokenAction, self::ACTIONS, true)) {
            throw new RuntimeException('ycom_user_token: action not defined. Must be "create" or "validate".');
        }

        $this->tokenType = (string) $this->getElement(3);
        if ('' === $this->tokenType) {
            throw new RuntimeException('ycom_user_token: type not defined.');
        }

        // "create" happens in preAction(), once the other fields have produced their values.
        if ('validate' !== $this->tokenAction) {
            return;
        }

        if (!$this->params['send']) {
            // The token arrives as a query parameter from the link in the email.
            $this->setValue(Request::request($this->getName(), 'string', ''));
        } else {
            try {
                $this->token = Token::getInstance()->validateToken((string) $this->getValue(), $this->tokenType);
            } catch (Throwable) {
                $this->params['warning'][$this->getId()] = $this->params['error_class'];
                $this->params['warning_messages'][$this->getId()] = $this->getElement(4);
            }
        }

        if ($this->needsOutput()) {
            $this->params['form_output'][$this->getId()] = $this->parse('hidden');
        }
    }

    #[Override]
    public function preAction(): void
    {
        match ($this->tokenAction) {
            'create' => $this->createToken(),
            'validate' => $this->consumeToken(),
            default => null,
        };
    }

    private function createToken(): void
    {
        $email = (string) ($this->params['value_pool']['sql']['email'] ?? '');

        if ('' === $email) {
            throw new RuntimeException('ycom_user_token: email not found');
        }

        $user = User::getMe();

        $tokenData = Token::getInstance()->createToken(
            $user?->getId(),
            $email,
            (string) $this->tokenType,
            $this->tokenDuration,
        );

        // Only into the email pool — the plain token must never reach the database.
        $this->params['value_pool']['email'][$this->getName()] = $tokenData['token'];
    }

    /**
     * Applies the form's values to the token's user, logs them in and burns the token.
     */
    private function consumeToken(): void
    {
        // enterObject() already flagged an invalid token as a warning; yform still runs the actions
        // when a field only set a warning, so bail out here rather than dereferencing null.
        if (null === $this->token) {
            return;
        }

        $user = null;

        if (null !== $this->token->getId()) {
            $user = User::query()->where('id', $this->token->getId())->findOne();
        }

        if (null === $user && '' !== $this->token->getEmail()) {
            $user = User::query()->where('email', $this->token->getEmail())->findOne();
        }

        if (null === $user) {
            return;
        }

        /** @var User $user */
        if (0 < count($this->params['value_pool']['sql'])) {
            foreach ($this->params['value_pool']['sql'] as $key => $value) {
                $user->setValue($key, $value);
            }
            $user->save();
        }

        $loggedIn = Auth::loginWithParams(['email' => $user->getValue('email')]);

        $this->token->delete();

        if (!$loggedIn) {
            return;
        }

        // Only redirect when the form has no redirect action of its own.
        foreach ($this->params['fields']['actions'] as $action) {
            if ($action instanceof Redirect) {
                return;
            }
        }

        Response::sendRedirect(Url::article((int) Config::get('article_id_login')));
    }

    #[Override]
    public function getDescription(): string
    {
        return 'ycom_user_token|create|[type]
                ycom_user_token|validate|[type]|[error_message]';
    }
}
