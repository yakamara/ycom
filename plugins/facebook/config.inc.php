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
$REX['ADDON']['community']['plugin_facebook']['appId'] = "179478962146992";
$REX['ADDON']['community']['plugin_facebook']['appSecret'] = "299f0a88cec92d7d7097f7f6b835ed8d";
$REX['ADDON']['community']['plugin_facebook']['appAccess'] = "email";
$REX['ADDON']['community']['plugin_facebook']['defaultgroups']['0'] = 1;
$REX['ADDON']['community']['plugin_facebook']['defaultgroups']['1'] = 2;
// --- /DYN

//
// Synctranslation
//
## login, password status, authsource, facebookid are default fields and already set - don't add!
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

## Register extension to create facebook object
rex_register_extension('ADDONS_INCLUDED', 'rex_com_facebookobj');
function rex_com_facebookobj()
{
	global $REX;
	## Loading Facebook API
	if(!class_exists('Facebook')) {
		echo "true";
		include $REX["INCLUDE_PATH"]."/addons/community/plugins/facebook/api/facebook.php";}
	
	$REX['ADDON']['community']['plugin_facebook']['facebook'] = new Facebook($REX['ADDON']['community']['plugin_facebook']['facebook_conf']);	
}

$REX['ADDON']['community']['plugin_facebook']['facebook_conf'] = array(
	'appId'=>$REX['ADDON']['community']['plugin_facebook']['appId'],
	'secret'=>$REX['ADDON']['community']['plugin_facebook']['appSecret']
	);

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