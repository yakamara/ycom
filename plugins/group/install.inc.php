<?php

/**
 * Community - Group
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

$REX['ADDON']['install']['group'] = 1;

// ----- Art der Gruppenrechte
$a = new rex_sql;
$a->setTable("rex_62_params");
$a->setValue("title","translate:com_group_perm");
$a->setValue("name","art_com_grouptype");
$a->setValue("prior","110");
$a->setValue("type","3");
$a->setValue("params","0:translate:com_group_forallgroups|1:translate:com_group_inallgroups|2:translate:com_group_inonegroup|3:translate:com_group_nogroups");
$a->setValue("validate",NULL);
$a->addGlobalCreateFields();
$g = new rex_sql;
$g->setQuery('select * from rex_62_params where name="art_com_grouptype"');

if ($g->getRows()==1) {
	$a->setWhere('name="art_com_grouptype"');
	$a->update();

}else {
	$a->insert();

}

$g = new rex_sql;
$g->setQuery('show columns from rex_article Like "art_com_grouptype"');
if ($g->getRows()==0) {
	  $a->setQuery("ALTER TABLE `rex_article` ADD `art_com_grouptype` VARCHAR( 255 ) NOT NULL");
}

// ----- Gruppen
$a = new rex_sql;
$a->setTable("rex_62_params");
$a->setValue("title","translate:com_group_name");
$a->setValue("name","art_com_groups");
$a->setValue("prior","111");
$a->setValue("type","3");
$a->setValue("attributes","multiple=multiple");
$a->setValue("params","select name as label,id from rex_com_group order by label");
$a->setValue("validate",NULL);
$a->addGlobalCreateFields();
$g = new rex_sql;
$g->setQuery('select * from rex_62_params where name="art_com_groups"');
if ($g->getRows()==1) {
    $a->setWhere('name="art_com_groups"');
    $a->update();
} else {
	  $a->insert();
}

$g = new rex_sql;
$g->setQuery('show columns from rex_article Like "art_com_groups"');
if ($g->getRows()==0) {
	  $a->setQuery("ALTER TABLE `rex_article` ADD `art_com_groups` VARCHAR( 255 ) NOT NULL");
}

## Prio neu sortieren // Metainfo
rex_organize_priorities($REX['TABLE_PREFIX']. '62_params', 'prior', 'name LIKE "art_%"', 'prior, updatedate', 'field_id');



rex_register_extension('OUTPUT_FILTER', function () {

    $table = array(
      'table_name' => 'rex_com_group',
      'name' => 'translate:com_group_name',
      'list_amount' => 50,
      'status' => 1,
      'export' => 1,
      'import' => 1,

    );

    $fields = array();

    // ('rex_com_group', 10, 'value', 'text', 'name', 'translate:name', '', '0', '', '', '', '', '', 0, 0);");
    $fields[] = array(
      'table_name' => 'rex_com_group',
      'prio' => 100,
      'type_id' => 'value',
      'type_name' => 'text',
      'name' => 'name',
      'label' => 'translate:name',
      'list_hidden' => 0,
      'search' => 0
    );

    // ('rex_com_comment', 20, 'validate', 'empty', 'comment', 'translate:com_comment_enter_comment', '', '', '', '', '', '', '', 1, 0);");
    $fields[] = array(
      'table_name' => 'rex_com_group',
      'prio' => 110,
      'type_id' => 'validate',
      'type_name' => 'empty',
      'name' => 'name',
      'message' => 'translate:com_group_xform_enter_name',
    );

    rex_xform_manager_table_api::setTable($table, $fields);

    // ('rex_com_user', 150, 'value', 'be_manager_relation', 'rex_com_group', 'translate:rex_com_group', 'rex_com_group', 'name', '1', '1', '', '', '', 1, 0);");
    $field = array(
      'table_name' => 'rex_com_user',
      'prio' => 50,
      'type_id' => 'value',
      'type_name' => 'be_manager_relation',
      'name' => 'rex_com_group',
      'label' => 'translate:rex_com_group',
      'table' => 'rex_com_group',
      'field' => 'name',
      'type' => 1,
      'empty_option' => 1,
      'size' => 5,
      'list_hidden' => 1,
      'search' => 0
    );

    rex_xform_manager_table_api::setTableField('rex_com_user', $field);

    rex_xform_manager_table_api::generateTablesAndFields();

    $info = rex_generateAll(); // kill cache ..

  }, array(), REX_EXTENSION_LATE);









?>