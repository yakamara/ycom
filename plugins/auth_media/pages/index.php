<?php
if (filter_input(INPUT_POST, "btn_save") == 1) {
	$form = (array) rex_post('form', 'array', []);
	$this->setConfig('auth_media_active', array_key_exists('auth_media_active', $form));
    
	// Set .htaccess file for unsecure file extensions and its settings
	if($this->getConfig('unsecure_fileext') != $form['unsecure_fileext']) {
		$this->setConfig('unsecure_fileext', $form['unsecure_fileext']);
		rex_ycom_auth_media::manageHtaccess(TRUE, explode(',', $form['unsecure_fileext']));
	}
	
    echo rex_view::success($this->i18n('ycom_auth_settings_updated'));
}

$content = '
<form action="'. rex_url::currentBackendPage() .'" method="post" id="ycom_auth_settings">
	<fieldset>
		<legend>'. $this->i18n('description') .'</legend>
		<div class="row">
			<div class="col-xs-12 abstand">
				<p>'. $this->i18n('ycom_auth_media_settings_description') .'</p>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend>'. $this->i18n('ycom_auth_media_settings_config') .'</legend>

		<div class="row">
			<div class="col-xs-12 col-sm-6 col-sm-push-6 abstand">
				<input class="rex-form-text" type="checkbox" id="rex-form-auth" name="form[auth_media_active]" value="true" ';
                if ($this->getConfig('auth_media_active') == 'true') {
                    $content .= ' checked="checked"';
                }
$content .= ' />
				<label for="rex-form-auth">'. $this->i18n('ycom_auth_media_settings_auth_active') .'</label>
			</div>
		</div>

		<div class="row abstand">
			<div class="col-xs-12 col-sm-6">
				<label for="rex-form-article_login_failed">'.$this->i18n('ycom_auth_media_help_unsecure_fileext').'</label>
			</div>
			<div class="col-xs-12 col-sm-6">
				<input class="form-control" type="text" name="form[unsecure_fileext]" value="' . $this->getConfig('unsecure_fileext') . '" />
			</div>
		</div>
	</fieldset>

	<div class="row">
		<div class="col-xs-12 col-sm-6 col-sm-push-6">
			<button class="btn btn-save right" type="submit" name="btn_save" value="1" title="'. $this->i18n('ycom_auth_config_save') .'">'. $this->i18n('ycom_auth_config_save') .'</button>
		</div>
	</div>
</form>
';

$fragment = new rex_fragment();
$fragment->setVar('class', 'edit');
$fragment->setVar('title', $this->i18n('ycom_auth_settings'));
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');
