<?php

/**
 * Plugin Board.
 *
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */
$mypage = 'board';
$REX['ADDON']['version'][$mypage] = '4.7.1';
$REX['ADDON']['author'][$mypage] = 'Jan Kristinus, Gregor Harlan';
$REX['ADDON']['supportpage'][$mypage] = 'www.yakamara.de/tag/redaxo/';

include rex_path::plugin('community', 'board', 'classes/class.rex_com_board.inc.php');
include rex_path::plugin('community', 'board', 'classes/class.rex_com_board_post.inc.php');
include rex_path::plugin('community', 'board', 'classes/class.rex_com_board_thread.inc.php');

if (isset($I18N) && is_object($I18N)) {
    $I18N->appendFile($REX['INCLUDE_PATH'] . '/addons/community/plugins/board/lang');
}

if ($REX['REDAXO'] && !$REX['SETUP'] && $REX['USER']) {
    $REX['ADDON']['community']['SUBPAGES'][] = array('plugin.board' , $I18N->msg('com_board'));
}
