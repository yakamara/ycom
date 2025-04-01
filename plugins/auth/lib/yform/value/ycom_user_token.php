<?php

class rex_yform_value_ycom_user_token extends rex_yform_value_abstract
{
    private static array $ycom_user_token_types = [
        'create' => 'create',
        'validate' => 'validate',
    ];
    private ?string $ycom_user_token_action = null;
    private ?string $ycom_user_token_type = null;
    private ?rex_ycom_user_token $ycom_user_token = null;
    private int $ycom_user_token_duration = 60 * 60 * 24;

    public function enterObject(): void
    {
        $this->ycom_user_token_action = (string) $this->getElement(2) ?? '';
        if (!in_array($this->ycom_user_token_action, self::$ycom_user_token_types)) {
            throw new rex_exception('ycom_user_token: action not defined. must be create or validate');
        }

        $this->ycom_user_token_type = (string) $this->getElement(3) ?? '';
        if ('' == $this->ycom_user_token_type) {
            throw new rex_exception('ycom_user_token: type not defined');
        }

        switch ($this->ycom_user_token_action) {
            case 'create':
                // Wenn actions durchgelaufen sind, dann Token erstellen
                break;
            case 'validate':
                if (!$this->params['send']) {
                    $value = rex_request($this->getName(), 'string', '');
                    $this->setValue($value);
                } else {
                    try {
                        $this->ycom_user_token = rex_ycom_user_token::getInstance()->validateToken($this->getValue(), $this->ycom_user_token_type);
                    } catch (Exception $e) {
                        $this->params['warning'][$this->getId()] = $this->params['error_class'];
                        $this->params['warning_messages'][$this->getId()] = $this->getElement(4);
                    }
                }

                if ($this->needsOutput()) {
                    $this->params['form_output'][$this->getId()] = $this->parse('value.hidden.tpl.php');
                }

                break;
        }
    }

    public function preAction(): void
    {
        switch ($this->ycom_user_token_action) {
            case 'create':
                // preActions, create Token
                $email = $this->params['value_pool']['sql']['email'] ?? '';
                if ('' == $email) {
                    throw new rex_exception('ycom_user_token: email not found');
                }
                $user = rex_ycom_user::getMe();
                $user_id = ($user instanceof rex_ycom_user) ? $user->getId() : null;

                $TokenData = rex_ycom_user_token::getInstance()
                    ->createToken($user_id, $email, $this->ycom_user_token_type, $this->ycom_user_token_duration);

                $this->params['value_pool']['email'][$this->getName()] = $TokenData['token'];
                break;

            case 'validate':
                $user = null;

                if ($this->ycom_user_token->getId()) {
                    $user = rex_ycom_user::query()
                        ->where('id', $this->ycom_user_token->getId())
                        ->findOne();
                }

                if (!$user && $this->ycom_user_token->getEmail()) {
                    $user = rex_ycom_user::query()
                        ->where('email', $this->ycom_user_token->getEmail())
                        ->findOne();
                }

                if ($user && count($this->params['value_pool']['sql']) > 0) {
                    foreach ($this->params['value_pool']['sql'] as $key => $value) {
                        $user->setValue($key, $value);
                    }
                    $user->save();
                }

                $user = rex_ycom_auth::loginWithParams([
                    'email' => $user->getValue('email'),
                ]);

                $this->ycom_user_token->delete();

                if ($user) {
                    rex_response::sendRedirect(rex_getUrl(rex_ycom_config::get('article_id_login')));
                }
        }
    }

    public function getDescription(): string
    {
        return 'ycom_user_token|create|[type]|[email_template]
                ycom_user_token|validate|[type]|[login/only_update]|[error_message]';
    }
}
