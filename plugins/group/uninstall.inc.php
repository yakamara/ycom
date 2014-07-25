<?php

/**
 * Plugin Group
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

$REX['ADDON']['install']['group'] = 0;

$i = rex_sql::factory();
$i->setQuery("DELETE FROM `rex_xform_table` where `table_name`='rex_com_group';");
$i->setQuery("DELETE FROM `rex_xform_field` where `table_name`='rex_com_group';");
$i->setQuery("DELETE FROM `rex_xform_field` where `table_name`='rex_com_user' and `f1`='rex_com_group';");

$i->setQuery("DELETE FROM `rex_62_params` where `name`='art_com_grouptype';");
$i->setQuery("DELETE FROM `rex_62_params` where `name`='art_com_groups';");


$info = rex_generateAll(); // quasi kill cache ..

?>