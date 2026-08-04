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
 * Forces a user who has not accepted the terms of use onto the terms-of-use article.
 */
final class TermsOfUse extends AbstractInjection
{
    #[Override]
    public function getRewrite(): bool|string
    {
        $user = Auth::getUser();

        if (null === $user) {
            return false;
        }

        $articleId = (int) Config::get('article_id_jump_termsofuse');

        if (0 === $articleId || 1 === (int) $user->getValue('termsofuse_accepted')) {
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
            <legend>' . $addon->i18n('ycom_auth_config_termsofuse') . '</legend>
            <div class="row abstand">
                <div class="col-xs-12 col-sm-6">
                    <label for="rex-form-article_termsofuse">' . $addon->i18n('ycom_auth_config_id_jump_termsofuse') . '</label>
                </div>
                <div class="col-xs-12 col-sm-6">
                    ' . LinkVar::getWidget(10, 'article_id_jump_termsofuse', (int) $addon->getConfig('article_id_jump_termsofuse')) . '
                    <small>[article_id_jump_termsofuse]</small>
                </div>
            </div>
        </fieldset>';
    }

    #[Override]
    public function triggerSaveSettings(): void
    {
        Addon::require('ycom')->setConfig('article_id_jump_termsofuse', Request::request('article_id_jump_termsofuse', 'int', 0));
    }
}
