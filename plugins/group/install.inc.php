<?php

/**
 * Community - Group
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

// ----- Art der Gruppenrechte
$a = new rex_sql;
$a->setTable("rex_62_params");
$a->setValue("title","translate:com_group_perm");
$a->setValue("name","art_com_grouptype");
$a->setValue("prior","10");
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
if ($g->getRows()==0) 
{
	$a->setQuery("ALTER TABLE `rex_article` ADD `art_com_grouptype` VARCHAR( 255 ) NOT NULL"); 
}

// ----- Gruppen
$a = new rex_sql;
$a->setTable("rex_62_params");
$a->setValue("title","translate:com_group_name");
$a->setValue("name","art_com_groups");
$a->setValue("prior","12");
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
}else {
	$a->insert();
}
$g = new rex_sql;
$g->setQuery('show columns from rex_article Like "art_com_groups"');
if ($g->getRows()==0) {
	$a->setQuery("ALTER TABLE `rex_article` ADD `art_com_groups` VARCHAR( 255 ) NOT NULL"); 
}

$REX['ADDON']['install']['group'] = 1;
$REX['ADDON']['installmsg']['group'] = ""; // $I18N->msg('community_group_install','2.8');

$info = rex_generateAll(); // quasi kill cache ..
	

?>