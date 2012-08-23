<?php


/**
 * Plugin Auth
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

$mypage = "auth";
$REX['ADDON']['version'][$mypage] = '2.9.1';
$REX['ADDON']['author'][$mypage] = 'Jan Kristinus';
$REX['ADDON']['supportpage'][$mypage] = 'www.yakamara.de/tag/redaxo/';

include $REX["INCLUDE_PATH"]."/addons/community/plugins/auth/classes/class.rex_com_navigation.inc.php";
include $REX["INCLUDE_PATH"]."/addons/community/plugins/auth/classes/class.rex_com_auth.inc.php";

## Register extension points
rex_register_extension('REX_NAVI_CLASSNAME', create_function('','return "rex_com_navigation";'));
rex_register_extension('REXSEO_SITEMAP_ARRAY_CREATED', 'rex_com_auth::rexseo_removeSitemapArticles');

if(isset($I18N) && is_object($I18N))
  $I18N->appendFile($REX['INCLUDE_PATH'] . '/addons/community/plugins/auth/lang');

// --- DYN
$REX['ADDON']['community']['plugin_auth']['auth_active'] = 1;
$REX['ADDON']['community']['plugin_auth']['stay_active'] = 1;
$REX['ADDON']['community']['plugin_auth']['article_login_ok'] = 1;
$REX['ADDON']['community']['plugin_auth']['article_login_failed'] = 12;
$REX['ADDON']['community']['plugin_auth']['article_logout'] = 12;
$REX['ADDON']['community']['plugin_auth']['article_withoutperm'] = 12;
$REX['ADDON']['community']['plugin_auth']['login_field'] = "login";
$REX['ADDON']['community']['plugin_auth']['passwd_hashed'] = "1";
// --- /DYN

$REX['ADDON']['community']['plugin_auth']['cookie_ttl'] = 14; // Cookie time to life - in days
$REX['ADDON']['community']['plugin_auth']['passwd_algorithmus'] = "sha1"; // see: hash_algos();

$REX['ADDON']['community']['plugin_auth']['request'] = array();
$REX['ADDON']['community']['plugin_auth']['request']['name'] = "rex_com_auth_name";
$REX['ADDON']['community']['plugin_auth']['request']['psw'] = "rex_com_auth_psw";
$REX['ADDON']['community']['plugin_auth']['request']['stay'] = "rex_com_auth_stay";
$REX['ADDON']['community']['plugin_auth']['request']['activationkey'] = "rex_com_auth_activationkey";
$REX['ADDON']['community']['plugin_auth']['request']['id'] = "rex_com_auth_id";
$REX['ADDON']['community']['plugin_auth']['request']['logout'] = "rex_com_auth_logout";
$REX['ADDON']['community']['plugin_auth']['request']['ref'] = "rex_com_auth_ref";

$REX['ADDON']['community']['xform_path']['value'][] = $REX["INCLUDE_PATH"]."/addons/community/plugins/auth/xform/value/";
$REX['ADDON']['community']['xform_path']['validate'][] = $REX["INCLUDE_PATH"]."/addons/community/plugins/auth/xform/validate/";
$REX['ADDON']['community']['xform_path']['action'][] = $REX["INCLUDE_PATH"]."/addons/community/plugins/auth/xform/action/";

if($REX["REDAXO"])
  if($REX['USER'] && ($REX['USER']->isAdmin() || $REX['USER']->hasPerm("community[auth]")))
    $REX['ADDON']['community']['SUBPAGES'][] = array('plugin.auth','Authentifizierung');

if($REX['ADDON']['community']['plugin_auth']['auth_active'] == 1)
{
  if(!$REX["REDAXO"])
  {
    function rex_com_auth_config()
    {
	  global $REX, $I18N;
	  include $REX["INCLUDE_PATH"]."/addons/community/plugins/auth/inc/auth.php";
	}
	
	rex_register_extension('ADDONS_INCLUDED', 'rex_com_auth_config');
	
	/*
	 if(isset($ADDONSsic['status']['rexseo']) && $ADDONSsic['status']['rexseo'])
	  rex_register_extension('REXSEO_POST_INIT', 'rex_com_auth_config');
	 else
	  rex_register_extension('ADDONS_INCLUDED', 'rex_com_auth_config');
	 */
  }
}

?>