<?php
/**
* Plugin Media-Access - boot.php
* @author Tobias Krais
* @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
*/

/* Add fields for media pool */
// Legend
$sql = \rex_sql::factory();
$sql->setQuery("SELECT * FROM ". \rex::getTablePrefix() ."metainfo_field WHERE `name` = 'med_ycom_auth_media_legend'");
if($sql->getRows() == 0) {
	$sql->setQuery("INSERT INTO `". \rex::getTablePrefix() ."metainfo_field` (`title`, `name`, `priority`, `attributes`, `type_id`, `default`, `params`, `validate`, `callback`, `restrictions`, `createuser`, `createdate`, `updateuser`, `updatedate`) VALUES
			('translate:ycom_auth_perm', 'med_ycom_auth_media_legend', (SELECT MAX(priority)+1 FROM ". \rex::getTablePrefix() ."metainfo_field AS meta), '', 12, '', '', NULL, '', NULL, '". rex::getUser()->getValue('login') ."', '". time() ."', '". rex::getUser()->getValue('login') ."', '". time() ."')");
}
\rex_sql_table::get(\rex::getTable('media'))
    ->ensureColumn(new \rex_sql_column('med_ycom_auth_media_legend', "VARCHAR(255)", TRUE))
    ->alter();
// User type
$sql->setQuery("SELECT * FROM ". \rex::getTablePrefix() ."metainfo_field WHERE `name` = 'med_ycom_auth_media_users'");
if($sql->getRows() == 0) {
	$sql->setQuery("INSERT INTO `". \rex::getTablePrefix() ."metainfo_field` (`title`, `name`, `priority`, `attributes`, `type_id`, `default`, `params`, `validate`, `callback`, `restrictions`, `createuser`, `createdate`, `updateuser`, `updatedate`) VALUES
			('translate:yform_ycom_user', 'med_ycom_auth_media_users', (SELECT MAX(priority)+1 FROM ". \rex::getTablePrefix() ."metainfo_field AS meta), '', 3, '3', '3:Zugriff für alle|1:Zugriff für alle eingeloggte User|2:Zugriff für eingeloggte User nachfolgend ausgewählter Gruppen', NULL, '', NULL, '". rex::getUser()->getValue('login') ."', '". time() ."', '". rex::getUser()->getValue('login') ."', '". time() ."')");
}
\rex_sql_table::get(\rex::getTable('media'))
    ->ensureColumn(new \rex_sql_column('med_ycom_auth_media_users', "ENUM('1','2','3')", FALSE, '3'))
    ->alter();
// Group select
$sql->setQuery("SELECT * FROM ". \rex::getTablePrefix() ."metainfo_field WHERE `name` = 'med_ycom_auth_media_groups'");
if($sql->getRows() == 0) {
	$sql->setQuery("INSERT INTO `". \rex::getTablePrefix() ."metainfo_field` (`title`, `name`, `priority`, `attributes`, `type_id`, `default`, `params`, `validate`, `callback`, `restrictions`, `createuser`, `createdate`, `updateuser`, `updatedate`) VALUES
			('translate:ycom_groups', 'med_ycom_auth_media_groups', (SELECT MAX(priority)+1 FROM ". \rex::getTablePrefix() ."metainfo_field AS meta), 'multiple=multiple', 3, '', 'SELECT name AS label, id from ". \rex::getTablePrefix() ."ycom_group order by label', NULL, '', NULL, '". rex::getUser()->getValue('login') ."', '". time() ."', '". rex::getUser()->getValue('login') ."', '". time() ."')");
}
\rex_sql_table::get(\rex::getTable('media'))
    ->ensureColumn(new \rex_sql_column('med_ycom_auth_media_groups', "ENUM('0','1','2','3')", FALSE, '0'))
    ->alter();

rex_delete_cache();

if(!$this->hasConfig('unsecure_fileext')) {
	$this->setConfig('unsecure_fileext', 'png,gif,ico,css,js,swf');
}

// Create .htaccess file
rex_ycom_auth_media::manageHtaccess(TRUE, explode(',', $this->getConfig('unsecure_fileext')));