<?php

/**
 * @var rex_addon $this
 * @psalm-scope-this rex_addon
 */

rex_yform_manager_table::deleteCache();

$content = rex_file::get(rex_path::addon('ycom', 'install/tablesets/yform_user.json'));
if (is_string($content) && '' != $content) {
    rex_yform_manager_table_api::importTablesets($content);
}

// old plugin docs still exists ? -> delete
$pluginDocs = __DIR__.'/plugins/docs';
if (file_exists($pluginDocs)) {
    rex_dir::delete($pluginDocs);
}

foreach ($this->getInstalledPlugins() as $plugin) {
    // use path relative to __DIR__ to get correct path in update temp dir
    $file = __DIR__ . '/plugins/' . $plugin->getName() . '/install.php';

    if (file_exists($file)) {
        $plugin->includeFile($file);
    }
}

rex_delete_cache();
rex_yform_manager_table::deleteCache();
