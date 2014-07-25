<?php
/**
 * Plugin Facebook-Auth
 * @author m.lorch[at]it-kult[dot]de Markus Lorch
 * @author <a href="http://www.it-kult.de">www.it-kult.de</a>
 */

$REX['ADDON']['install']['auth_facebook'] = 1;
$REX['ADDON']['installmsg']['auth_facebook'] = '';

rex_register_extension('OUTPUT_FILTER', function () {

    $field = array(
      'table_name' => 'rex_com_user',
      'prio' => 270,
      'type_id' => 'value',
      'type_name' => 'text',
      'name' => 'authsource',
      'label' => 'translate:com_auth_authsource',
      'list_hidden' => 1,
      'search' => 1
    );

    rex_xform_manager_table_api::setTableField('rex_com_user', $field);

    $field = array(
      'table_name' => 'rex_com_user',
      'prio' => 280,
      'type_id' => 'value',
      'type_name' => 'text',
      'name' => 'facebookid',
      'label' => 'translate:com_auth_facebook_facebookid',
      'list_hidden' => 1,
      'search' => 1
    );

    rex_xform_manager_table_api::setTableField('rex_com_user', $field);

    rex_xform_manager_table_api::generateTablesAndFields();

  }, array(), REX_EXTENSION_LATE);