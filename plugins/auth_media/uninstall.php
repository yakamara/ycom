<?php
// REMOVE Table fields
\rex_sql_table::get(\rex::getTable('media'))
    ->removeColumn('med_ycom_auth_media_legend')
    ->removeColumn('med_ycom_auth_media_users')
    ->removeColumn('med_ycom_auth_media_groups')
    ->alter();

$sql = \rex_sql::factory();
$sql->setQuery("DELETE FROM ". \rex::getTablePrefix() ."metainfo_field WHERE `name` = 'med_ycom_auth_media_legend'");
$sql->setQuery("DELETE FROM ". \rex::getTablePrefix() ."metainfo_field WHERE `name` = 'med_ycom_auth_media_users'");
$sql->setQuery("DELETE FROM ". \rex::getTablePrefix() ."metainfo_field WHERE `name` = 'med_ycom_auth_media_groups'");

// Remove .htaccess
rex_ycom_auth_media::manageHtaccess(FALSE);