<?php

/**
 * @var rex_addon $this
 * @psalm-scope-this rex_addon
 */

echo rex_view::title($this->i18n('ycom_title'));

// this file integrates the already existing log-viewer as a syslog page.
// the required registration wiring can be found in the package.yml
require __DIR__. '/system.log.ycom_user.php';
