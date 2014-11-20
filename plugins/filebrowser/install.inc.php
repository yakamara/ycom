<?php

/**
 * Community - filebrowser
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

$moduleName = 'Community – Filebrowser';
$sql = rex_sql::factory();
$sql->setQuery('SELECT id FROM ' . $REX['TABLE_PREFIX'] . 'module WHERE name = "' . $sql->escape($moduleName) . '"');
if (!$sql->getRows()) {
    $sql->setTable($REX['TABLE_PREFIX'] . 'module');
    $sql->setValue('name', $moduleName);
    $sql->setValue('eingabe', $sql->escape(rex_get_file_contents(__DIR__ . '/install/module.input.php')));
    $sql->setValue('ausgabe', $sql->escape(rex_get_file_contents(__DIR__ . '/install/module.output.php')));
    $sql->addGlobalCreateFields();
    $sql->addGlobalUpdateFields();
    $sql->insert();
}

$REX['ADDON']['install']['filebrowser'] = 1;
$REX['ADDON']['installmsg']['filebrowser'] = ''; // $I18N->msg('com_comment_install','2.8');

// $info = rex_generateAll(); // quasi kill cache ..
