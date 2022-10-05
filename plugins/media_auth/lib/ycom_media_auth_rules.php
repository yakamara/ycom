<?php

/**
 * Class rex_ycom_media_auth_rules.
 */

class rex_ycom_media_auth_rules
{
    /**
     * @var array<string, array{'info': string, 'action': string[]}>
     */
    private array $rules;

    public function __construct()
    {
        $this->rules = [];
        $this->rules['redirect'] = [
            'info' => rex_i18n::msg('ycom_media_auth_failed_redirect_login'),
            'action' => [
                'type' => 'redirect',
                'article_id' => rex_plugin::get('ycom', 'auth')->getConfig('article_id_login'),
            ],
        ];
        $this->rules['redirect_with_errorpage'] = [
            'info' => rex_i18n::msg('ycom_media_auth_failed_redirect_login_with_error_page'),
            'action' => [
                'type' => 'redirect',
                'article_id' => rex_plugin::get('ycom', 'auth')->getConfig('article_id_login'),
                'error_article_id' => rex_plugin::get('ycom', 'auth')->getConfig('article_id_jump_denied'),
            ],
        ];
        $this->rules['header_notfound'] = [
            'info' => rex_i18n::msg('ycom_media_auth_failed_header_notfound'),
            'action' => ['type' => 'header', 'header' => rex_response::HTTP_NOT_FOUND],
        ];
        $this->rules['header_perm_denied'] = [
            'info' => rex_i18n::msg('ycom_media_auth_failed_header_perm_denied'),
            'action' => ['type' => 'header', 'header' => rex_response::HTTP_UNAUTHORIZED],
        ];
    }

    public function check(string $rule_name): void
    {
        if (!array_key_exists($rule_name, $this->rules)) {
            $rule_name = 'redirect';
        }

        $rule = $this->rules[$rule_name];

        switch ($rule['action']['type']) {
            case 'redirect':
                $me = rex_ycom_user::getMe();
                if ($me) {
                    // logged in
                    rex_response::setStatus(rex_response::HTTP_UNAUTHORIZED);
                    if (isset($rule['action']['error_article_id'])) {
                        rex_redirect($rule['action']['error_article_id']);
                    } else {
                        rex_response::sendContent('');
                    }
                    exit;
                }
                rex_response::sendCacheControl();
                rex_redirect($rule['action']['article_id'], '', ['returnTo' => $_SERVER['REQUEST_URI']]);

                // no break
            case 'redirect_wo_returnto':
                rex_response::sendCacheControl();
                rex_redirect($rule['action']['article_id'], '', []);

                // no break
            case 'header':
                rex_response::setStatus($rule['action']['header']);
                rex_response::sendContent('');
                break;

            default:
                throw new rex_exception(sprintf('Unknown auth_rule action key "%s".', $rule['action']['type']));
        }
        exit;
    }

    /**
     * @return string[]
     */
    public function getOptions(): array
    {
        $options = [];

        foreach ($this->rules as $key => $rule) {
            $options[$key] = $rule['info'];
        }

        return $options;
    }
}
