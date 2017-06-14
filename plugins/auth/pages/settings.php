<?php

$info = '';
$warning = '';
$content = '';
$modules = [
//		"login"		=> array(		"key" => "login", 			"search" => "module:ycom_auth_login",			"name" => "ycom:auth - Login"),
// 		"pswchange"	=> array(		"key" => "pswchange", 		"search" => "module:ycom_auth_pswchange",		"name" => "ycom:auth - Change Password"),
// 		"profilechange"	=> array(	"key" => "profilechange", 	"search" => "module:ycom_auth_profilechange",	"name" => "ycom:auth - Profile"),
    ];

$table = rex_yform_manager_table::get('rex_ycom_user');
$xform_user_fields = $table->getValueFields();

if (rex_request('func', 'string') == 'update') {
    $this->setConfig('auth_active', rex_request('auth_active', 'int'));
    $this->setConfig('article_id_jump_ok', rex_request('article_id_jump_ok', 'int'));
    $this->setConfig('article_id_jump_not_ok', rex_request('article_id_jump_not_ok', 'int'));
    $this->setConfig('article_id_jump_logout', rex_request('article_id_jump_logout', 'int'));
    $this->setConfig('article_id_jump_denied', rex_request('article_id_jump_denied', 'int'));
    $this->setConfig('login_tries', rex_request('login_tries', 'int'));
    $this->setConfig('login_field', stripslashes(str_replace('"', '', rex_request('login_field', 'string'))));

    echo rex_view::success($this->i18n('ycom_auth_settings_updated'));
}

$sel_userfields = new rex_select();
$sel_userfields->setName('login_field');
$sel_userfields->setSize(1);
foreach ($xform_user_fields as $k => $xf) {
    $sel_userfields->addOption($k, $k);
}
$sel_userfields->setSelected($this->getConfig('login_field'));

$sel_logintries = new rex_select();
$sel_logintries->setId('login_tries_id');
$sel_logintries->setName('login_tries');
$sel_logintries->addOption($this->i18n('ycom_auth_config_login_tries_unlimited'), 0);
$sel_logintries->addOption($this->i18n('ycom_auth_config_login_tries_deactivateafter', 5), 5);
$sel_logintries->addOption($this->i18n('ycom_auth_config_login_tries_deactivateafter', 10), 10);
$sel_logintries->addOption($this->i18n('ycom_auth_config_login_tries_deactivateafter', 20), 20);
$sel_logintries->setAttribute('class', 'form-control selectpicker');
$sel_logintries->setSelected($this->getConfig('login_tries'));

$content .= '
<form action="index.php" method="post" id="ycom_auth_settings">
    <input type="hidden" name="page" value="ycom/auth/settings" />
    <input type="hidden" name="func" value="update" />


	<fieldset>
		<legend>'.$this->i18n('ycom_auth_config_status').'</legend>

		<div class="row">
			<div class="col-xs-12 col-sm-6 col-sm-push-6 abstand">
				<input class="rex-form-text" type="checkbox" id="rex-form-auth" name="auth_active" value="1" ';
                if ($this->getConfig('auth_active') == '1') {
                    $content .= 'checked="checked"';
                }
$content .= ' />
				<label for="rex-form-auth">'.$this->i18n('ycom_auth_config_status_authactive').'</label>
			</div>
		</div>


	</fieldset>

	<fieldset>
		<legend>'.$this->i18n('ycom_auth_config_forwarder').'</legend>

		<div class="row abstand">
			<div class="col-xs-12 col-sm-6">
				<label for="rex-form-article_login_ok">'.$this->i18n('ycom_auth_config_id_jump_ok').'</label>
			</div>
			<div class="col-xs-12 col-sm-6">
				'. rex_var_link::getWidget(5, 'article_id_jump_ok', stripslashes($this->getConfig('article_id_jump_ok'))) .'
			</div>
		</div>

		<div class="row abstand">
			<div class="col-xs-12 col-sm-6">
				<label for="rex-form-article_login_failed">'.$this->i18n('ycom_auth_config_id_jump_not_ok').'</label>
			</div>
			<div class="col-xs-12 col-sm-6">
				'. rex_var_link::getWidget(6, 'article_id_jump_not_ok', stripslashes($this->getConfig('article_id_jump_not_ok'))) .'
			</div>
		</div>

		<div class="row abstand">
			<div class="col-xs-12 col-sm-6">
				<label for="rex-form-article_logout">'.$this->i18n('ycom_auth_config_id_jump_logout').'</label>
			</div>
			<div class="col-xs-12 col-sm-6">
				'. rex_var_link::getWidget(7, 'article_id_jump_logout', stripslashes($this->getConfig('article_id_jump_logout'))) .'
			</div>
		</div>

		<div class="row abstand">
			<div class="col-xs-12 col-sm-6">
				<label for="rex-form-article_logout">'.$this->i18n('ycom_auth_config_id_jump_denied').'</label>
			</div>
			<div class="col-xs-12 col-sm-6">
				'. rex_var_link::getWidget(8, 'article_id_jump_denied', stripslashes($this->getConfig('article_id_jump_denied'))) .'
			</div>
		</div>

	</fieldset>

	<fieldset>
		<legend>'.$this->i18n('ycom_auth_config_login_field').'</legend>
		<div class="row">
			<div class="col-xs-12 col-sm-6">
				'.$this->i18n('ycom_auth_config_login_field').'
			</div>
			<div class="col-xs-12 col-sm-6">
			 	<div class="select-style">
	              	'.$sel_userfields->get().'
			  	</div>
			</div>
		</div>
	</fieldset>

    <fieldset>
            <legend>'.$this->i18n('ycom_auth_config_security').'</legend>
    
            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <label for="auth_login_tries_select">' . $this->i18n('ycom_auth_config_login_tries') . '</label>
                </div>
                <div class="col-xs-12 col-sm-6">
                '.$sel_logintries->get().'
            </div>
        </div>
    </fieldset>

	<div class="row">
		<div class="col-xs-12 col-sm-6 col-sm-push-6">
			<button class="btn btn-save right" type="submit" name="config-submit" value="1" title="'.$this->i18n('ycom_auth_config_save').'">'.$this->i18n('ycom_auth_config_save').'</button>
		</div>
	</div>

	</form>

  ';

$fragment = new rex_fragment();
$fragment->setVar('class', 'edit');
$fragment->setVar('title', $this->i18n('ycom_auth_settings'));
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');
