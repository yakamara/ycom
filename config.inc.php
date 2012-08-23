<?php

$mypage = "community"; // only for this file

// ---------- Allgemeine AddOn Config

// TODO:
// Unterscheidung der Sprachen anhand REX_CUR_CLANG.

if($REX["CUR_CLANG"] == 1)
	$REX['LANG'] = "en_en_utf8";

if (!isset($I18N) && !is_object($I18N))
	$I18N = rex_create_lang($REX['LANG']);

$I18N->appendFile($REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/lang');

include $REX["INCLUDE_PATH"]."/addons/community/classes/class.rex_com.inc.php";
include $REX["INCLUDE_PATH"]."/addons/community/classes/class.rex_com_user.inc.php";

$REX['ADDON']['name'][$mypage] = "Community";   // name
$REX['ADDON']['perm'][$mypage] = "community[]"; // benoetigte mindest permission
$REX['ADDON']['navigation'][$mypage] = array('block'=>'community');

$REX['ADDON']['version'][$mypage] = '2.9.1';
$REX['ADDON']['author'][$mypage] = 'Jan Kristinus';
$REX['ADDON']['supportpage'][$mypage] = 'www.redaxo.org/de/forum';
$REX['PERM'][] = "community[]";


// ---------- Backend, Perms, Subpages etc.
if ($REX["REDAXO"] && $REX['USER'])
{
	$REX['EXTRAPERM'][] = "community[]";
	
	$REX['ADDON'][$mypage]['SUBPAGES'] = array();
	$REX['ADDON'][$mypage]['SUBPAGES'][] = array( '' , $I18N->msg("com_overview"));
	
	// if ($REX['USER']->isAdmin() || $REX['USER']->hasPerm("community[users]")) 
	// $REX['ADDON'][$mypage]['SUBPAGES'][] = array ('user' , $I18N->msg('com_user_management'));
	
}


// ---------- XForm values/action/validations einbinden

$REX['ADDON']['community']['xform_path']['value'] = array();
$REX['ADDON']['community']['xform_path']['validate'] = array();
$REX['ADDON']['community']['xform_path']['action'] = array();

$REX['ADDON']['community']['xform_path']['value'][] = $REX["INCLUDE_PATH"]."/addons/community/xform/value/";

function rex_com_xform_add($params){
	global $REX;
	foreach($REX['ADDON']['community']['xform_path']['value'] as $value) { 
		$REX['ADDON']['xform']['classpaths']['value'][] = $value;
	}
	foreach($REX['ADDON']['community']['xform_path']['validate'] as $validate) {
		$REX['ADDON']['xform']['classpaths']['validate'][] = $validate;
	}
	foreach($REX['ADDON']['community']['xform_path']['action'] as $action) {
		$REX['ADDON']['xform']['classpaths']['action'][] = $action;
	}

}

rex_register_extension('ADDONS_INCLUDED', 'rex_com_xform_add');
