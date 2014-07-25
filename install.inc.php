<?php

/**
 * Community Install
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

$addonname = 'community';

$I18N->appendFile($REX['INCLUDE_PATH'] . '/addons/community/lang');

if ($REX['VERSION'] != '4' || $REX['SUBVERSION'] < '6') {
  $REX['ADDON']['install']['community'] = 0;
  $REX['ADDON']['installmsg']['community'] = $I18N->msg('community_install_redaxo_version_problem', '4.6', $REX['VERSION'].".".$REX['SUBVERSION']);

} elseif (OOAddon::isAvailable('phpmailer') != 1 || version_compare(OOAddon::getVersion('phpmailer'), '2.8', '<')) {
  $REX['ADDON']['install']['community'] = 0;
  $REX['ADDON']['installmsg']['community'] = $I18N->msg('community_install_phpmailer_version_problem', '2.8');

} elseif (OOAddon::isAvailable('xform') != 1 || version_compare(OOAddon::getVersion('xform'), '4.7', '<')) {
  $REX['ADDON']['install']['community'] = 0;
  $REX['ADDON']['installmsg']['community'] = $I18N->msg('community_install_xform_version_problem', '4.7');

} else {

  $msg = '';

  $table = array(
    'table_name' => 'rex_com_user',
    'name' => 'translate:com_user',
    'list_amount' => 100,
    'export' => 1,
    'import' => 1,

  );

  $fields = array();
  $fields[] = array(
    'table_name' => 'rex_com_user',
    'prio' => 100,
    'type_id' => 'value',
    'type_name' => 'text',
    'name' => 'login',
    'label' => 'translate:login',
    'list_hidden' => 0,
    'search' => 1
  );
  $fields[] = array(
    'table_name' => 'rex_com_user',
    'prio' => 200,
    'type_id' => 'value',
    'type_name' => 'text',
    'name' => 'password',
    'label' => 'translate:password',
    'list_hidden' => 1,
    'search' => 0
  );
  $fields[] = array(
    'table_name' => 'rex_com_user',
    'prio' => 300,
    'type_id' => 'value',
    'type_name' => 'text',
    'name' => 'email',
    'label' => 'translate:email',
    'list_hidden' => 0,
    'search' => 1
  );
  $fields[] = array(
    'table_name' => 'rex_com_user',
    'prio' => 400,
    'type_id' => 'value',
    'type_name' => 'select',
    'name' => 'status',
    'label' => 'translate:status',
    'options' => 'translate:com_account_requested=0,translate:com_account_active=1,translate:com_account_inactive=-1',
    'multiple' => 0,
    'default' => -1,
    'size' => 1,
    'list_hidden' => 0,
    'search' => 1
  );
  $fields[] = array(
    'table_name' => 'rex_com_user',
    'prio' => 500,
    'type_id' => 'value',
    'type_name' => 'text',
    'name' => 'firstname',
    'label' => 'translate:firstname',
    'list_hidden' => 0,
    'search' => 1
  );
  $fields[] = array(
    'table_name' => 'rex_com_user',
    'prio' => 600,
    'type_id' => 'value',
    'type_name' => 'text',
    'name' => 'name',
    'label' => 'translate:name',
    'list_hidden' => 0,
    'search' => 1
  );
  $fields[] = array(
    'table_name' => 'rex_com_user',
    'prio' => 700,
    'type_id' => 'value',
    'type_name' => 'text',
    'name' => 'activation_key',
    'label' => 'translate:activation_key',
    'list_hidden' => 1,
    'search' => 1
  );

  $fields[] = array(
    'table_name' => 'rex_com_user',
    'prio' => 800,
    'type_id' => 'value',
    'type_name' => 'text',
    'name' => 'session_key',
    'label' => 'translate:session_key',
    'list_hidden' => 1,
    'search' => 1
  );

  $fields[] = array(
    'table_name' => 'rex_com_user',
    'prio' => 900,
    'type_id' => 'value',
    'type_name' => 'datestamp',
    'name' => 'last_action_time',
    'label' => 'U',
    'only_empty' => '0',
    'list_hidden' => 1,
    'search' => 1
  );

  $fields[] = array(
    'table_name' => 'rex_com_user',
    'prio' => 110,
    'type_id' => 'validate',
    'type_name' => 'empty',
    'name' => 'login',
    'message' => 'translate:com_please_enter_login',
  );

  $fields[] = array(
    'table_name' => 'rex_com_user',
    'prio' => 120,
    'type_id' => 'validate',
    'type_name' => 'unique',
    'name' => 'login',
    'table' => 'rex_com_user',
    'message' => 'translate:com_this_login_exists_already',
  );

  $fields[] = array(
    'table_name' => 'rex_com_user',
    'prio' => 210,
    'type_id' => 'validate',
    'type_name' => 'empty',
    'name' => 'password',
    'message' => 'translate:com_please_enter_password',
  );

  $fields[] = array(
    'table_name' => 'rex_com_user',
    'prio' => 310,
    'type_id' => 'validate',
    'type_name' => 'empty',
    'name' => 'email',
    'message' => 'translate:com_please_enter_email',
  );

  $fields[] = array(
    'table_name' => 'rex_com_user',
    'prio' => 320,
    'type_id' => 'validate',
    'type_name' => 'email',
    'name' => 'email',
    'message' => 'translate:com_please_enter_email',
  );

  $fields[] = array(
    'table_name' => 'rex_com_user',
    'prio' => 330,
    'type_id' => 'validate',
    'type_name' => 'unique',
    'name' => 'email',
    'table' => 'rex_com_user',
    'message' => 'translate:com_this_email_exists_already',
  );

  rex_xform_manager_table_api::setTable($table, $fields);

  $autoinstall = array('auth', 'group', 'newsletter');

  $all_addons = rex_read_addons_folder();
  $all_plugins = array();
  foreach ($all_addons as $_addon) {
    $all_plugins[$_addon] = rex_read_plugins_folder($_addon);
  }

  $pluginManager = new rex_pluginManager($all_plugins, $addonname);
  foreach ($autoinstall as $pluginname) {
    if (($instErr = $pluginManager->install($pluginname)) !== true) {
      $msg = $instErr;
    }

    if ($msg == '' && ($actErr = $pluginManager->activate($pluginname)) !== true) {
      $msg = $actErr;
    }

    if ($msg != '') {
      break;
    }
  }

  if ($msg != '') {
    $REX['ADDON']['installmsg'][$addonname] = $msg;

  } else {

    $REX['ADDON']['install']['community'] = 1;

    function rex_com_install()
    {
      $r = new rex_xform_manager;
      $r->generateAll();
    }
    rex_register_extension('OUTPUT_FILTER', 'rex_com_install');

    rex_dir::copy(
      rex_path::addon('community', 'install'),
      rex_path::addonData('import_export', 'backups')
    );

  }

}
