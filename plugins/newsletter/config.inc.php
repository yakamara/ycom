<?php

/** 
 * Config . Zuständig für den Newsletter 
 * @author jan@kristinus
 * @version 1.0
 */ 

$mypage = "newsletter";
$REX['ADDON']['version'][$mypage] = '2.9.6';
$REX['ADDON']['author'][$mypage] = 'Jan Kristinus';
$REX['ADDON']['supportpage'][$mypage] = 'www.yakamara.de/tag/redaxo/';
$REX['PERM'][] = "community[newsletter]";


if ($REX["REDAXO"] && $REX['USER'] && ( $REX['USER']->isAdmin() || $REX['USER']->hasPerm("community[newsletter]") ) ) {
  $REX['ADDON']['community']['SUBPAGES'][] = array('plugin.newsletter','Newsletter');
}

if (isset($I18N) && is_object($I18N)) {
  $I18N->appendFile($REX['INCLUDE_PATH'] . '/addons/community/plugins/newsletter/lang');
}

include ($REX["INCLUDE_PATH"].'/addons/community/plugins/newsletter/lib/class.rex_com_newsletter.inc.php');

$REX['ADDON']['community']['xform_path']['validate'][] = $REX["INCLUDE_PATH"]."/addons/community/plugins/newsletter/xform/validate/";
