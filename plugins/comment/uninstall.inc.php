<?php

/**
 * Plugin comment
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

$REX['ADDON']['install']['comment'] = 0;

$i = rex_sql::factory();
$i->setQuery("DELETE FROM `rex_xform_table` where `table_name`='rex_com_comment';");
$i->setQuery("DELETE FROM `rex_xform_field` where `table_name`='rex_com_comment';");

$info = rex_generateAll(); // quasi kill cache ..

?>