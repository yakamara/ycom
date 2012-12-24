<?php
/**
 * Plugin Facebook-Auth
 * @author m.lorch[at]it-kult[dot]de Markus Lorch
 * @author <a href="http://www.it-kult.de">www.it-kult.de</a>
 */

//
// Plugin Settings
//

$mypage = "auth_facebook";
$REX['ADDON']['version'][$mypage] = '2.9.6';

// --- DYN
$REX['ADDON']['community']['plugin_auth_facebook']['appId'] = "";
$REX['ADDON']['community']['plugin_auth_facebook']['appSecret'] = "";
$REX['ADDON']['community']['plugin_auth_facebook']['appAccess'] = "email";
// --- /DYN

$REX['ADDON']['community']['plugin_auth_facebook']['redirect'] = true;

//
// Synctranslation
//
## login, password status, authsource, facebookid are default fields and already set - don't add!
## For Available facebook fields see: http://developers.facebook.com/docs/reference/api/
$REX['ADDON']['community']['plugin_auth_facebook']['synctranslation'] = array(
##	'rex_com_user field' => 'facebook field'
	'firstname' => 'first_name',
	'name' => 'last_name',
	'email' => 'email'
	);

//
// Initialisierung
//
include $REX["INCLUDE_PATH"]."/addons/community/plugins/auth_facebook/classes/class.rex_com_auth_facebook.inc.php";

## Include Lang
if (isset($I18N) && is_object($I18N))
{
	$I18N->appendFile($REX['INCLUDE_PATH'].'/addons/community/plugins/auth_facebook/lang');
	
	## Adding language key for compat reasons	
	if(!$I18N->hasMsg('com_auth_authsource'))
		$I18N->addMsg('com_auth_authsource','Auth-Plugin');
}

## Include xform classes
$REX['ADDON']['community']['xform_path']['value'][] = $REX["INCLUDE_PATH"]."/addons/community/plugins/auth_facebook/xform/value/";

## Register extension to create facebook object
rex_register_extension('ADDONS_INCLUDED', 'rex_com_facebookobj');
function rex_com_facebookobj()
{
	global $REX;
	## Loading Facebook API
	if(!class_exists('Facebook')) 
	{
		include $REX["INCLUDE_PATH"]."/addons/community/plugins/auth_facebook/api/facebook.php";
	}
  $REX['ADDON']['community']['plugin_auth_facebook']['facebook'] = new Facebook($REX['ADDON']['community']['plugin_auth_facebook']['facebook_conf']);	
}

$REX['ADDON']['community']['plugin_auth_facebook']['facebook_conf'] = array(
	'appId'=>$REX['ADDON']['community']['plugin_auth_facebook']['appId'],
	'secret'=>$REX['ADDON']['community']['plugin_auth_facebook']['appSecret']
	);

if($REX["REDAXO"])
{
	## Adding to Backend Menu
	if($REX['USER'] && ($REX['USER']->isAdmin() || $REX['USER']->hasPerm("community[facebook]")))
		$REX['ADDON']['community']['SUBPAGES'][] = array('plugin.auth_facebook','Facebook');

}else
{
	## Include Auth
	rex_register_extension('ADDONS_INCLUDED', create_function('','
		global $REX,$I18N;
		include $REX["INCLUDE_PATH"]."/addons/community/plugins/auth_facebook/inc/auth.php";
	'));

}

?>