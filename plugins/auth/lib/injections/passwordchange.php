<?php

class rex_ycom_injection_passwordchange extends rex_ycom_injection_abtract
{
    public function getRewrite(): bool|string
    {
        $user = rex_ycom_auth::getUser();
        if ($user) {
            $article_id_password = (int) rex_ycom_config::get('article_id_jump_password', 0);
            if (0 != $article_id_password && 1 == $user->getValue('new_password_required')) {
                if ($article_id_password != rex_article::getCurrentId()) {
                    return rex_getUrl($article_id_password, '', [], '&');
                }
                return true;
            }
        }
        return false;
    }

    public function getSettingsContent(): string
    {
        $addon = rex_plugin::get('ycom', 'auth');
        return '
		<fieldset>
		<legend>' . $addon->i18n('ycom_auth_config_passwordchange') . '</legend>
        <div class="row abstand">
			<div class="col-xs-12 col-sm-6">
				<label for="rex-form-article_password">' . $addon->i18n('ycom_auth_config_id_jump_password') . '</label>
			</div>
			<div class="col-xs-12 col-sm-6">
				' . rex_var_link::getWidget(9, 'article_id_jump_password', (int) $addon->getConfig('article_id_jump_password')) . '
				<small>[article_id_jump_password]</small>
			</div>
		</div>
        </fieldset>
        ';
    }

    public function triggerSaveSettings(): void
    {
        $addon = rex_plugin::get('ycom', 'auth');
        $addon->setConfig('article_id_jump_password', rex_request('article_id_jump_password', 'int'));
    }
}
