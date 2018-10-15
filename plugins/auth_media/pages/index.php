<?php
if (filter_input(INPUT_POST, "btn_save") == 'save') {
	$form = (array) rex_post('form', 'array', []);
	$this->setConfig('auth_media_active', array_key_exists('auth_media_active', $form));

	// Set .htaccess file
	rex_ycom_auth_media::manageHtaccess(array_key_exists('auth_media_active', $form) ? TRUE : FALSE, explode(',', $form['secured_fileext']));
    
	$this->setConfig('secured_fileext', $form['secured_fileext']);

	echo rex_view::success($this->i18n('ycom_auth_settings_updated'));
}

// X-SendFile reminder
if (!extension_loaded('mod_xsendfile')) {
	echo rex_view::warning($this->i18n('ycom_auth_media_settings_xsendfile'));
}
?>
<form action="<?php print rex_url::currentBackendPage(); ?>" method="post">
	<div class="panel panel-edit">
		<header class="panel-heading"><div class="panel-title"><?php print rex_i18n::msg('ycom_auth_media'); ?></div></header>
		<div class="panel-body">
			<dl class="rex-form-group form-group">
				<dt><label><?php print $this->i18n('description'); ?></label></dt>
				<dd><?php print $this->i18n('ycom_auth_media_settings_description'); ?></dd>
			</dl>
			<dl class="rex-form-group form-group">
				<dt><input class="rex-form-text" type="checkbox" name="form[auth_media_active]" value="true" style="float: right; height: auto; width: auto;" <?php print ($this->getConfig('auth_media_active') == 'true' ? ' checked="checked"' : ''); ?>></dt>
				<dd><label><?php print $this->i18n('ycom_auth_media_settings_auth_active'); ?></label></dd>
			</dl>

			<dl class="rex-form-group form-group">
				<dt><label></label></dt>
				<dd><?php print $this->i18n('ycom_auth_media_settings_secure_fileext_desc'); ?></dd>
			</dl>
			<dl class="rex-form-group form-group">
				<dt><label><?php print $this->i18n('ycom_auth_media_settings_secure_fileext'); ?></label></dt>
				<dd><input class="form-control" type="text" name="form[secured_fileext]" value="<?php echo $this->getConfig('secured_fileext'); ?>"></dd>
			</dl>
		</div>

		<footer class="panel-footer">
			<div class="rex-form-panel-footer">
				<div class="btn-toolbar">
					<button class="btn btn-save rex-form-aligned" type="submit" name="btn_save" value="save"><?php echo $this->i18n('ycom_auth_config_save'); ?></button>
				</div>
			</div>
		</footer>
	</div>
</form>