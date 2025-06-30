<?php

/**
 * @var rex_addon $this
 * @psalm-scope-this rex_addon
 */

if (rex_plugin::get('ycom', 'auth')->isAvailable()) {
    rex_ycom_auth::addInjection(new rex_ycom_injection_otp(), 1);
    rex_ycom_auth::addInjection(new rex_ycom_injection_passwordchange(), 4);
    rex_ycom_auth::addInjection(new rex_ycom_injection_termsofuse(), 8);
}

if (rex::isBackend()) {
    rex_extension::register('PACKAGES_INCLUDED', static function ($params) {
        $addon = rex_addon::get('yform');
        $plugin = rex_plugin::get('yform', 'manager');
        if ($plugin->isAvailable()) {
            // YForm <= 5
            $pages = $plugin->getProperty('pages');
            $ycom_tables = rex_ycom::getTables();
            if (isset($pages) && is_array($pages)) {
                foreach ($pages as $page) {
                    if (in_array($page->getKey(), $ycom_tables, true)) {
                        $page->setBlock('ycom');
                        // $page->setRequiredPermissions('ycom[]');
                    }
                }
            }
        } else {
            // YForm >= 5
            $pages = $addon->getProperty('pages');
            $ycom_tables = rex_ycom::getTables();
            if (isset($pages) && is_array($pages)) {
                foreach ($pages as $page) {
                    if (in_array($page->getKey(), $ycom_tables, true)) {
                        $page->setBlock('ycom');
                        // $page->setRequiredPermissions('ycom[]');
                    }
                }
            }
        }
    });
}

rex_ycom::addTable(rex::getTablePrefix() . 'ycom_user');
rex_yform_manager_dataset::setModelClass(rex::getTablePrefix() . 'ycom_user', rex_ycom_user::class);

if (rex::isBackend() && ('index.php?page=content/edit' === rex_url::currentBackendPage() || 'mediapool' === rex_be_controller::getCurrentPagePart(1))) {
    rex_view::addJsFile($this->getAssetsUrl('ycom_backend.js'));
}
