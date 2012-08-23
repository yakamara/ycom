<?php

/**
 * Plugin Group
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

$mypage = "group";
$REX['ADDON']['version'][$mypage] = '2.9.1';
$REX['ADDON']['author'][$mypage] = 'Jan Kristinus';
$REX['ADDON']['supportpage'][$mypage] = 'www.yakamara.de/tag/redaxo/';

if (isset($I18N) && is_object($I18N))
  $I18N->appendFile($REX['INCLUDE_PATH'] . '/addons/community/plugins/group/lang');

?>