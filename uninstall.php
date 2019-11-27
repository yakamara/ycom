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

foreach ($this->getInstalledPlugins() as $plugin) {
    // use path relative to __DIR__ to get correct path in update temp dir
    $file = __DIR__ . '/plugins/' . $plugin->getName() . '/uninstall.php';

    if (file_exists($file)) {
        $plugin->includeFile($file);
    }
}

rex_yform_manager_table::deleteCache();
