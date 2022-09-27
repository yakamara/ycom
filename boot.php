<?php

/**
 * @var rex_addon $this
 * @psalm-scope-this rex_addon
 */

if (rex::isBackend()) {
    rex_extension::register('PACKAGES_INCLUDED', static function ($params) {
        $plugin = rex_plugin::get('yform', 'manager');

        if ($plugin->isAvailable()) {
            $pages = $plugin->getProperty('pages');
            $ycom_tables = rex_ycom::getTables();

            if (isset($pages) && is_array($pages)) {
                foreach ($pages as $page) {
                    if (in_array($page->getKey(), $ycom_tables)) {
                        $page->setBlock('ycom');
                        // $page->setRequiredPermissions('ycom[]');
                    }
                }
            }
        }
    });
}

rex_ycom::addTable('rex_ycom_user');
rex_yform_manager_dataset::setModelClass('rex_ycom_user', rex_ycom_user::class);

if (rex::isBackend() && ('index.php?page=content/edit' == rex_url::currentBackendPage() || 'mediapool' == rex_be_controller::getCurrentPagePart(1))) {
    rex_view::addJsFile($this->getAssetsUrl('ycom_backend.js'));
}
