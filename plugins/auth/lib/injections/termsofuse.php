<?php

class rex_ycom_injection_termsofuse extends rex_ycom_injection_abtract
{
    public function getRewrite(): bool|string
    {
        $user = rex_ycom_auth::getUser();
        if ($user) {
            $article_id_termsofuse = (int) rex_ycom_config::get('article_id_jump_termsofuse', 0);
            if (0 != $article_id_termsofuse && 1 != $user->getValue('termsofuse_accepted')) {
                if ($article_id_termsofuse != rex_article::getCurrentId()) {
                    return rex_getUrl($article_id_termsofuse, '', [], '&');
                }
            }
        }
        return false;
    }

    public function getSettingsContent(): string
    {
        $addon = rex_plugin::get('ycom', 'auth');

        return '
        <fieldset>
        <legend>' . $addon->i18n('ycom_auth_config_termsofuse') . '</legend>
		<div class="row abstand">
			<div class="col-xs-12 col-sm-6">
				<label for="rex-form-article_termsofuse">' . $addon->i18n('ycom_auth_config_id_jump_termsofuse') . '</label>
			</div>
			<div class="col-xs-12 col-sm-6">
				' . rex_var_link::getWidget(10, 'article_id_jump_termsofuse', (int) $addon->getConfig('article_id_jump_termsofuse')) . '
				<small>[article_id_jump_termsofuse]</small>
			</div>
		</div>
		</fieldset>
		';
    }

    public function triggerSaveSettings(): void
    {
        $addon = rex_plugin::get('ycom', 'auth');
        $addon->setConfig('article_id_jump_termsofuse', rex_request('article_id_jump_termsofuse', 'int', 0));
    }
}
