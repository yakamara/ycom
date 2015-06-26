<?php

/**
 * Plugin board.
 *
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */
$REX['ADDON']['install']['board'] = 0;

$i = rex_sql::factory();
$i->setQuery("DELETE FROM `rex_xform_table` where `table_name`='rex_com_board_post';");
$i->setQuery("DELETE FROM `rex_xform_field` where `table_name`='rex_com_board_post';");
$info = rex_generateAll();
