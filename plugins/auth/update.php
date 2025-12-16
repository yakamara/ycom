<?php

/**
 * @var rex_addon $this
 * @psalm-scope-this rex_addon
 */

// Ohne Overwrite schema
// dann werden nur fehlende Felder angelegt
// rex_yform_manager_table_api::importTablesets($content);
// existiert noch nicht

// einzelne Änderungen hier erzwingen und einzeln angeben

$this->includeFile(__DIR__ . '/install.php');
