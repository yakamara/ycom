<?php

$sql = rex_sql::factory();
$sql->setQuery('DELETE FROM `'.rex::getTable('yform_table').'` WHERE table_name = "'.rex::getTable('ycom_user').'"');
$sql->setQuery('DELETE FROM `'.rex::getTable('yform_field').'` WHERE table_name = "'.rex::getTable('ycom_user').'"');
$sql->setQuery('DELETE FROM `'.rex::getTable('yform_history').'` WHERE table_name = "'.rex::getTable('ycom_user').'"');

rex_sql_table::get(rex::getTable('article'))
    ->removeColumn('ycom_auth_type')
    ->removeColumn('ycom_group_type')
    ->removeColumn('ycom_groups')
    ->alter();

rex_sql_table::get(rex::getTable('ycom_user'))
    ->drop();

if ($this->getPlugin('group')->isInstalled()) {
    $this->getPlugin('group')->includeFile(__DIR__.'/plugins/group/uninstall.php');
}

if ($this->getPlugin('media_auth')->isInstalled()) {
    $this->getPlugin('media_auth')->includeFile(__DIR__.'/plugins/media_auth/uninstall.php');
}
