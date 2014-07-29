<?php

/**
 * auth
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

// --- metainfo
$a = rex_sql::factory();
$a->setTable("rex_62_params");
$a->setValue("title","translate:com_permtype");
$a->setValue("name","art_com_permtype");
$a->setValue("prior","100");
$a->setValue("type","3");
$a->setValue("params","0:translate:com_perm_extends|1:translate:com_perm_only_logged_in|2:translate:com_perm_only_not_logged_in|3:translate:com_perm_all");
$a->setValue("validate",NULL);
$a->addGlobalCreateFields();
$g = rex_sql::factory();
$g->setQuery('select * from rex_62_params where name="art_com_permtype"');
if ($g->getRows()==1) {
	$a->setWhere('name="art_com_permtype"');
	$a->update();

} else {
	$a->insert();
}
$g = new rex_sql;
$g->setQuery('show columns from rex_article like "art_com_permtype"');
if ($g->getRows()==0) {
	$a->setQuery("ALTER TABLE `rex_article` ADD `art_com_permtype` VARCHAR( 255 ) NOT NULL"); 
}

## Prio neu sortieren // Metainfo
rex_organize_priorities($REX['TABLE_PREFIX']. '62_params', 'prior', 'name LIKE "art_%"', 'prior, updatedate', 'field_id');

rex_register_extension('OUTPUT_FILTER', function () {

    global $REX;

    $REX['ADDON']['xform']['classpaths']['value']['community.auth'] = $REX['INCLUDE_PATH'] . '/addons/community/plugins/auth/xform/value/';
    $REX['ADDON']['xform']['classpaths']['validate']['community.auth'] = $REX['INCLUDE_PATH'] . '/addons/community/plugins/auth/xform/validate/';
    $REX['ADDON']['xform']['classpaths']['action']['community.auth'] = $REX['INCLUDE_PATH'] . '/addons/community/plugins/auth/xform/action/';

    $field = array(
      'table_name' => 'rex_com_user',
      'prio' => 250,
      'type_id' => 'value',
      'type_name' => 'com_auth_password_hash',
      'name' => 'password_hash',
      'hashname' => 'password',
      'list_hidden' => 0,
      'search' => 1
    );

    rex_xform_manager_table_api::setTableField('rex_com_user', $field);

    rex_xform_manager_table_api::generateTablesAndFields();

    $info = rex_generateAll(); // kill cache

  }, array(), REX_EXTENSION_LATE);


$REX['ADDON']['install']['auth'] = 1;
