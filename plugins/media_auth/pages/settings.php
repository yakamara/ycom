<?php

$table = rex_yform_manager_table::get('rex_ycom_user');
$xform_user_fields = $table->getValueFields();

if ('update' == rex_request('func', 'string')) {
    $this->setConfig('media_auth_rule', rex_request('media_auth_rule', 'string'));

    echo rex_view::success($this->i18n('ycom_media_auth_settings_updated'));
}

$sel_authrules = new rex_select();
$sel_authrules->setId('media-auth-rule');
$sel_authrules->setName('media_auth_rule');

$rules = new rex_ycom_media_auth_rules();

$sel_authrules->addOptions($rules->getOptions());

$sel_authrules->setAttribute('class', 'form-control selectpicker');
$sel_authrules->setSelected($this->getConfig('media_auth_rule'));

$content = '
<form action="index.php" method="post" id="ycom_auth_settings">
    <input type="hidden" name="page" value="ycom/media_auth/settings" />
    <input type="hidden" name="func" value="update" />

	<fieldset>

        <div class="row abstand">
            <div class="col-xs-12 col-sm-4">
                <label for="auth_rules_select">' . $this->i18n('ycom_auth_config_media_auth_rules') . '</label>
            </div>
            <div class="col-xs-12 col-sm-8">
            '.$sel_authrules->get().'
            </div>
        </div>
        
    </fieldset>

	<div class="row">
		<div class="col-xs-12 col-sm-8 col-sm-push-4">
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
