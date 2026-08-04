<?php

namespace Yakamara\YCom\Auth\Injection;

use Override;
use Redaxo\Core\Addon\Addon;
use Redaxo\Core\Content\Article;
use Redaxo\Core\Filesystem\Url;
use Redaxo\Core\Http\Request;
use Redaxo\Core\RexVar\LinkVar;
use Yakamara\YCom\Auth\Auth;
use Yakamara\YCom\Config;


/**
 * Forces a user flagged with `new_password_required` onto the password-change article.
 */
final class PasswordChange extends AbstractInjection
{
    #[Override]
    public function getRewrite(): bool|string
    {
        $user = Auth::getUser();

        if (null === $user) {
            return false;
        }

        $articleId = (int) Config::get('article_id_jump_password');

        if (0 === $articleId || 1 !== (int) $user->getValue('new_password_required')) {
            return false;
        }

        if ($articleId === Article::getCurrentId()) {
            return true;
        }

        return Url::article($articleId);
    }

    #[Override]
    public function getSettingsContent(): string
    {
        $addon = Addon::require('ycom');

        return '
        <fieldset>
            <legend>' . $addon->i18n('ycom_auth_config_passwordchange') . '</legend>
            <div class="row abstand">
                <div class="col-xs-12 col-sm-6">
                    <label for="rex-form-article_password">' . $addon->i18n('ycom_auth_config_id_jump_password') . '</label>
                </div>
                <div class="col-xs-12 col-sm-6">
                    ' . LinkVar::getWidget(9, 'article_id_jump_password', (int) $addon->getConfig('article_id_jump_password')) . '
                    <small>[article_id_jump_password]</small>
                </div>
            </div>
        </fieldset>';
    }

    #[Override]
    public function triggerSaveSettings(): void
    {
        Addon::require('ycom')->setConfig('article_id_jump_password', Request::request('article_id_jump_password', 'int', 0));
    }
}
