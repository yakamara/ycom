<?php

rex_yform_manager_table::deleteCache();

/** @var rex_addon $this */

$content = rex_file::get(rex_path::addon('ycom', 'install/tablesets/yform_user.json'));
rex_yform_manager_table_api::importTablesets($content);

foreach ($this->getInstalledPlugins() as $plugin) {
    // use path relative to __DIR__ to get correct path in update temp dir
    $file = __DIR__ . '/plugins/' . $plugin->getName() . '/install.php';

    if (file_exists($file)) {
        $plugin->includeFile($file);
    }
}

rex_delete_cache();
rex_yform_manager_table::deleteCache();
