<?php

$mypage = "community"; // only for this file

if(!isset($_SESSION)) {
    session_start();
}

// ---------- Allgemeine AddOn Config

// TODO:
// Unterscheidung der Sprachen anhand REX_CUR_CLANG.

if($REX["CUR_CLANG"] == 1) {
    $REX['LANG'] = "en_gb_utf8";
}

if (!isset($I18N) && !is_object($I18N)) {
	  $I18N = rex_create_lang($REX['LANG']);
}

$I18N->appendFile($REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/lang');

include $REX["INCLUDE_PATH"]."/addons/community/classes/class.rex_com.inc.php";
include $REX["INCLUDE_PATH"]."/addons/community/classes/class.rex_com_user.inc.php";

$REX['ADDON']['name'][$mypage] = "Community";   // name
$REX['ADDON']['perm'][$mypage] = "community[]"; // benoetigte mindest permission
$REX['ADDON']['navigation'][$mypage] = array('block'=>'community');

$REX['ADDON']['version'][$mypage] = '4.7';
$REX['ADDON']['author'][$mypage] = 'Jan Kristinus';
$REX['ADDON']['supportpage'][$mypage] = 'www.redaxo.org/de/forum';
$REX['PERM'][] = "community[]";


// ---------- Backend, Perms, Subpages etc.
if ($REX["REDAXO"] && $REX['USER']) {
    $REX['EXTRAPERM'][] = "community[]";
    $REX['ADDON'][$mypage]['SUBPAGES'] = array();
    $REX['ADDON'][$mypage]['SUBPAGES'][] = array( '' , $I18N->msg("com_overview"));
}

$REX['ADDON']['xform']['classpaths']['value']['community'] = $REX["INCLUDE_PATH"]."/addons/community/xform/value/";
