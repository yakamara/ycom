<?php

/** @var rex_addon $this */

echo rex_view::title($this->i18n('ycom_title'));

$info = '';
$warning = '';
$content = '';
$modules = [
    //		"login"		=> array(		"key" => "login", 			"search" => "module:ycom_auth_login",			"name" => "ycom:auth - Login"),
    // 		"pswchange"	=> array(		"key" => "pswchange", 		"search" => "module:ycom_auth_pswchange",		"name" => "ycom:auth - Change Password"),
    // 		"profilechange"	=> array(	"key" => "profilechange", 	"search" => "module:ycom_auth_profilechange",	"name" => "ycom:auth - Profile"),
];

$table = rex_yform_manager_table::get(rex::getTablePrefix() . 'ycom_user');

if ('update' == rex_request('func', 'string')) {
    $this->setConfig('article_id_jump_ok', rex_request('article_id_jump_ok', 'int'));
    $this->setConfig('article_id_jump_not_ok', rex_request('article_id_jump_not_ok', 'int'));
    $this->setConfig('article_id_jump_logout', rex_request('article_id_jump_logout', 'int'));
    $this->setConfig('article_id_jump_denied', rex_request('article_id_jump_denied', 'int'));
    $this->setConfig('article_id_login', rex_request('article_id_login', 'int'));
    $this->setConfig('article_id_logout', rex_request('article_id_logout', 'int'));
    $this->setConfig('article_id_register', rex_request('article_id_register', 'int'));
    $this->setConfig('article_id_password', rex_request('article_id_password', 'int'));
    $this->setConfig('auth_rule', rex_request('auth_rule', 'string'));
    $this->setConfig('auth_cookie_ttl', rex_request('auth_cookie_ttl', 'int'));
    $this->setConfig('login_field', stripslashes(str_replace('"', '', rex_request('login_field', 'string'))));
    $this->setConfig('session_max_overall_duration', rex_request('session_max_overall_duration', 'int'));
    $this->setConfig('session_duration', rex_request('session_duration', 'int'));

    foreach (rex_ycom_auth::getInjections() as $injection) {
        $injection->triggerSaveSettings();
    }

    echo rex_view::success($this->i18n('ycom_auth_settings_updated'));
}

$sel_userfields = new rex_select();
$sel_userfields->setName('login_field');
$sel_userfields->setSize(1);

$sel_userfields->addOption('id', 'id');
$sel_userfields->addOption('email', 'email');
$sel_userfields->addOption('login', 'login');

/*foreach ($table->getValueFields() as $k => $xf) {
    $sel_userfields->addOption($k, $k);
}*/

$sel_userfields->setSelected($this->getConfig('login_field'));

$sel_authrules = new rex_select();
$sel_authrules->setId('auth-rule');
$sel_authrules->setName('auth_rule');

$rules = new rex_ycom_auth_rules();

$sel_authrules->addOptions($rules->getOptions());

$sel_authrules->setAttribute('class', 'form-control selectpicker');
$sel_authrules->setSelected($this->getConfig('auth_rule'));

$sel_authcookiettl = new rex_select();
$sel_authcookiettl->setId('auth-cookie-ttl');
$sel_authcookiettl->setName('auth_cookie_ttl');
$sel_authcookiettl->setAttribute('class', 'form-control selectpicker');
$sel_authcookiettl->setSelected($this->getConfig('auth_cookie_ttl'));

$sel_authcookiettl->addOption($this->i18n('ycom_days', 7), '7');
$sel_authcookiettl->addOption($this->i18n('ycom_days', 14), '14');
$sel_authcookiettl->addOption($this->i18n('ycom_days', 30), '30');
$sel_authcookiettl->addOption($this->i18n('ycom_days', 90), '90');

$content .= '
<form action="index.php" method="post" id="ycom_auth_settings">
    <input type="hidden" name="page" value="ycom/auth/settings" />
    <input type="hidden" name="func" value="update" />';

$content .= '
	<fieldset>
		<legend>' . $this->i18n('ycom_auth_config_forwarder') . '</legend>

        <div class="row abstand">
			<div class="col-xs-12 col-sm-12">
				<label for="rex-form-article_login_ok"><strong>' . $this->i18n('ycom_auth_howtoread_configvalues') . '</strong></label>
			</div>
		</div>

		<div class="row abstand">
			<div class="col-xs-12 col-sm-6">
				<label for="rex-form-article_login_ok">' . $this->i18n('ycom_auth_config_id_jump_ok') . '</label>
			</div>
			<div class="col-xs-12 col-sm-6">
				' . rex_var_link::getWidget(5, 'article_id_jump_ok', (int) $this->getConfig('article_id_jump_ok')) . '
				<small>[article_id_jump_ok]</small>
			</div>
		</div>

		<div class="row abstand">
			<div class="col-xs-12 col-sm-6">
				<label for="rex-form-article_logout">' . $this->i18n('ycom_auth_config_id_jump_logout') . '</label>
			</div>
			<div class="col-xs-12 col-sm-6">
				' . rex_var_link::getWidget(7, 'article_id_jump_logout', (int) $this->getConfig('article_id_jump_logout')) . '
				<small>[article_id_jump_logout]</small>
			</div>
		</div>

		<div class="row abstand">
			<div class="col-xs-12 col-sm-6">
				<label for="rex-form-article_denied">' . $this->i18n('ycom_auth_config_id_jump_denied') . '
				</label>

			</div>
			<div class="col-xs-12 col-sm-6">
				' . rex_var_link::getWidget(8, 'article_id_jump_denied', (int) $this->getConfig('article_id_jump_denied')) . '
				<small>' . $this->i18n('ycom_auth_config_id_jump_denied_notice') . '<br />[article_id_jump_denied]</small>
			</div>
		</div>
    </fieldset>

	<fieldset>
		<legend>' . $this->i18n('ycom_auth_config_pages') . '</legend>

		<div class="row abstand">
			<div class="col-xs-12 col-sm-6">
				<label for="rex-form-article_login">' . $this->i18n('ycom_auth_config_id_login') . '</label>
			</div>
			<div class="col-xs-12 col-sm-6">
				' . rex_var_link::getWidget(11, 'article_id_login', (int) $this->getConfig('article_id_login')) . '
				<small>[article_id_login]</small>
			</div>
		</div>

		<div class="row abstand">
			<div class="col-xs-12 col-sm-6">
				<label for="rex-form-article_login">' . $this->i18n('ycom_auth_config_id_logout') . '</label>
			</div>
			<div class="col-xs-12 col-sm-6">
				' . rex_var_link::getWidget(12, 'article_id_logout', (int) $this->getConfig('article_id_logout')) . '
				<small>[article_id_logout]</small>
			</div>
		</div>


        <div class="row abstand">
			<div class="col-xs-12 col-sm-6">
				<label for="rex-form-article_register">' . $this->i18n('ycom_auth_config_id_register') . '</label>
			</div>
			<div class="col-xs-12 col-sm-6">
				' . rex_var_link::getWidget(13, 'article_id_register', (int) $this->getConfig('article_id_register')) . '
				<small>[article_id_register]</small>
			</div>
		</div>

        <div class="row abstand">
			<div class="col-xs-12 col-sm-6">
				<label for="rex-form-article_password">' . $this->i18n('ycom_auth_config_id_password') . '</label>
			</div>
			<div class="col-xs-12 col-sm-6">
				' . rex_var_link::getWidget(14, 'article_id_password', $this->getConfig('article_id_password')) . '
				<small>[article_id_password]</small>
			</div>
		</div>

	</fieldset>

	<fieldset>
		<legend>' . $this->i18n('ycom_auth_config_login_field') . '</legend>
		<div class="row">
			<div class="col-xs-12 col-sm-6">
				' . $this->i18n('ycom_auth_config_login_field') . '
			</div>
			<div class="col-xs-12 col-sm-6">
			 	<div class="select-style">
	              	' . $sel_userfields->get() . '
			  	</div>
			  	<small>[login_field]</small>
			</div>
		</div>
	</fieldset>

    <fieldset>
            <legend>' . $this->i18n('ycom_auth_config_security') . '</legend>

            <div class="row abstand">
                <div class="col-xs-12 col-sm-6">
                    <label for="auth_rules_select">' . $this->i18n('ycom_auth_config_auth_rules') . '</label>
                </div>
                <div class="col-xs-12 col-sm-6">
                ' . $sel_authrules->get() . '
                <small>[auth-rule]</small>
                </div>
            </div>

            <div class="row abstand">
                <div class="col-xs-12 col-sm-6">
                    <label for="auth_cookie_ttl_select">' . $this->i18n('ycom_auth_config_auth_cookie_ttl') . '</label>
                </div>
                <div class="col-xs-12 col-sm-6">
                ' . $sel_authcookiettl->get() . '
                <small>[auth-cookie-ttl]</small>
                </div>
            </div>

            <div class="row abstand">
                <div class="col-xs-12 col-sm-6">
                    <label for="auth_session_max_overall_duration">' . $this->i18n('ycom_auth_config_session_max_overall_duration') . '</label>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <input class="form-control" type="text" name="session_max_overall_duration" value="' . $this->getConfig('session_max_overall_duration', 21600) . '" />
                    <small>[session_max_overall_duration]</small>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <label for="auth_session_duration">' . $this->i18n('ycom_auth_config_session_duration') . '</label>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <input class="form-control" type="text" name="session_duration" value="' . $this->getConfig('session_duration', 3600) . '" />
                    <small>[session_duration]</small>
                </div>
            </div>

    </fieldset>

    <fieldset>
        <legend>' . $this->i18n('ycom_auth_config_extern') . '</legend>

        <div class="row abstand">
			<div class="col-xs-12 col-sm-6">
				<label for="rex-form-article_login_failed">' . $this->i18n('ycom_auth_config_id_jump_not_ok') . '</label>
			</div>
			<div class="col-xs-12 col-sm-6">
				' . rex_var_link::getWidget(15, 'article_id_jump_not_ok', $this->getConfig('article_id_jump_not_ok', '')) . '
				<small>[article_id_jump_not_ok]</small>
			</div>
		</div>

    </fieldset>';

foreach (rex_ycom_auth::getInjections() as $injection) {
    $content .= $injection->getSettingsContent();
}

$content .= '
	<div class="row">
		<div class="col-xs-12 col-sm-6 col-sm-push-6">
			<button class="btn btn-save right" type="submit" name="config-submit" value="1" title="' . $this->i18n('ycom_auth_config_save') . '">' . $this->i18n('ycom_auth_config_save') . '</button>
		</div>
	</div>

	</form>
  ';

$fragment = new rex_fragment();
$fragment->setVar('class', 'edit');
$fragment->setVar('title', $this->i18n('ycom_auth_settings'));
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');
