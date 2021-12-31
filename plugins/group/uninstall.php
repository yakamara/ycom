<?php

/**
 * @var rex_addon $this
 * @psalm-scope-this rex_addon
 */

$sql = rex_sql::factory();
$sql->setQuery('DELETE FROM `'.rex::getTable('yform_table').'` WHERE table_name = "'.rex::getTable('ycom_group').'"');
$sql->setQuery('DELETE FROM `'.rex::getTable('yform_field').'` WHERE table_name = "'.rex::getTable('ycom_group').'"');
$sql->setQuery('DELETE FROM `'.rex::getTable('yform_field').'` WHERE table_name = "'.rex::getTable('ycom_user').'" and name="ycom_groups"');
$sql->setQuery('DELETE FROM `'.rex::getTable('yform_history').'` WHERE table_name = "'.rex::getTable('ycom_group').'"');

rex_sql_table::get(rex::getTable('ycom_group'))
    ->drop();

rex_yform_manager_table::deleteCache();
