<?php

namespace Yakamara\YCom\MediaAuth;

use Redaxo\Core\Exception\RuntimeException;
use Redaxo\Core\Filesystem\Url;
use Redaxo\Core\Http\Request;
use Redaxo\Core\Http\Response;
use Redaxo\Core\Translation\I18n;
use Yakamara\YCom\Config;
use Yakamara\YCom\User;

use function array_key_exists;
use function sprintf;

/**
 * What happens when a visitor requests a media file they are not allowed to see.
 */
final class MediaAuthRules
{
    public const string DEFAULT_RULE = 'header_perm_denied';

    /** @var array<string, array{info: string, action: array<string, mixed>}> */
    private array $rules;

    public function __construct()
    {
        $this->rules = [
            'redirect' => [
                'info' => I18n::msg('ycom_media_auth_failed_redirect_login'),
                'action' => [
                    'type' => 'redirect',
                    'article_id' => (int) Config::get('article_id_login'),
                ],
            ],
            'redirect_with_errorpage' => [
                'info' => I18n::msg('ycom_media_auth_failed_redirect_login_with_error_page'),
                'action' => [
                    'type' => 'redirect',
                    'article_id' => (int) Config::get('article_id_login'),
                    'error_article_id' => (int) Config::get('article_id_jump_denied'),
                ],
            ],
            'redirect_wo_returnto' => [
                'info' => I18n::msg('ycom_media_auth_failed_redirect_login_wo_returnto'),
                'action' => [
                    'type' => 'redirect_wo_returnto',
                    'article_id' => (int) Config::get('article_id_login'),
                ],
            ],
            'header_notfound' => [
                'info' => I18n::msg('ycom_media_auth_failed_header_notfound'),
                'action' => ['type' => 'header', 'header' => Response::HTTP_NOT_FOUND],
            ],
            'header_perm_denied' => [
                'info' => I18n::msg('ycom_media_auth_failed_header_perm_denied'),
                'action' => ['type' => 'header', 'header' => Response::HTTP_UNAUTHORIZED],
            ],
        ];
    }

    /**
     * Applies the configured rule and ends the request.
     */
    public function check(?string $ruleName): never
    {
        if (!array_key_exists($ruleName ?? '', $this->rules)) {
            $ruleName = self::DEFAULT_RULE;
        }

        $action = $this->rules[$ruleName]['action'];

        switch ($action['type']) {
            case 'redirect':
                if (null !== User::getMe()) {
                    // Logged in but not allowed — sending them to the login page would be pointless.
                    Response::setStatus(Response::HTTP_UNAUTHORIZED);

                    if (isset($action['error_article_id']) && 0 < (int) $action['error_article_id']) {
                        Response::sendRedirect(Url::article((int) $action['error_article_id']));
                    }

                    Response::sendCacheControl();
                    Response::sendContent('');
                    exit;
                }

                Response::sendCacheControl();
                Response::sendRedirect(Url::article((int) $action['article_id'], null, [
                    'returnTo' => Request::server('REQUEST_URI', 'string', ''),
                ]));

                // no break — sendRedirect() exits
            case 'redirect_wo_returnto':
                Response::sendCacheControl();
                Response::sendRedirect(Url::article((int) $action['article_id']));

                // no break — sendRedirect() exits
            case 'header':
                Response::setStatus((string) $action['header']);
                Response::sendCacheControl();
                Response::sendContent('');
                exit;

            default:
                throw new RuntimeException(sprintf('Unknown media_auth_rule action key "%s".', $action['type']));
        }
    }

    /** @return array<string, string> */
    public function getOptions(): array
    {
        $options = [];

        foreach ($this->rules as $key => $rule) {
            $options[$key] = $rule['info'];
        }

        return $options;
    }
}
