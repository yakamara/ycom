<?php
/**
 * Plugin Facebook-Auth
 * @author m.lorch[at]it-kult[dot]de Markus Lorch
 * @author <a href="http://www.it-kult.de">www.it-kult.de</a>
 */

//
// Plugin Settings
//

// --- DYN
$REX['ADDON']['community']['plugin_facebook']['appId'] = "";
$REX['ADDON']['community']['plugin_facebook']['appSecret'] = "";
$REX['ADDON']['community']['plugin_facebook']['appAccess'] = "email";
// --- /DYN

//
// Synctranslation
//
## login, password status, authsource, facebookid are default fields and already set - dont add!
## For Available facebook fields see: http://developers.facebook.com/docs/reference/api/
$REX['ADDON']['community']['plugin_facebook']['synctranslation'] = array(
##	'rex_com_user field' => 'facebook field'
	'firstname' => 'first_name',
	'name' => 'last_name',
	'email' => 'email'
	);

//
// Initialisierung
//
include $REX["INCLUDE_PATH"]."/addons/community/plugins/facebook/classes/class.rex_com_facebook.inc.php";

## Include Lang
if (isset($I18N) && is_object($I18N))
{
	$I18N->appendFile($REX['INCLUDE_PATH'].'/addons/community/plugins/facebook/lang');
	
	## Adding language key for compat reasons	
	if(!$I18N->hasMsg('com_auth_authsource'))
		$I18N->addMsg('com_auth_authsource','Auth-Plugin');
}

## Include xform classes
$REX['ADDON']['community']['xform_path']['value'][] = $REX["INCLUDE_PATH"]."/addons/community/plugins/facebook/xform/value/";

## Facebook-API if Available
## http://www.redaxo.org/de/forum/post96341.html#p96341
## Not working with redaxo5 -> use in r5: OOAddon::isAvailable('facebook_sdk')
if($ADDONSsic['status']['facebook_sdk'])
{
	## Register extension to create facebook object
	rex_register_extension('ADDONS_INCLUDED', 'rex_com_facebookobj');
	function rex_com_facebookobj()
	{
		global $REX;
		$REX['ADDON']['community']['plugin_facebook']['facebook'] = new Facebook($REX['ADDON']['community']['plugin_facebook']['facebook_conf']);	
	}

	$REX['ADDON']['community']['plugin_facebook']['facebook_conf'] = array(
		'appId'=>$REX['ADDON']['community']['plugin_facebook']['appId'],
		'secret'=>$REX['ADDON']['community']['plugin_facebook']['appSecret']
		);
}

if($REX["REDAXO"])
{
	## Adding to Backend Menu
	if($REX['USER'] && ($REX['USER']->isAdmin() || $REX['USER']->hasPerm("community[facebook]")))
		$REX['ADDON']['community']['SUBPAGES'][] = array('plugin.facebook','Facebook');
}
else
{
	## Include Auth
	rex_register_extension('ADDONS_INCLUDED', create_function('','
		global $REX,$I18N;
		include $REX["INCLUDE_PATH"]."/addons/community/plugins/facebook/inc/auth.php";
	'));
}
?>