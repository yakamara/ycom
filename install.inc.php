<?php

/**
 * Community Install 
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

$I18N->appendFile($REX['INCLUDE_PATH'] . '/addons/community/lang');

if($I18N->msg("htmlcharset") != "utf-8") {

	$REX['ADDON']['install']['community'] = 0;
	$REX['ADDON']['installmsg']['community'] = $I18N->msg('community_install_only_utf8');

}elseif (OOAddon::isAvailable('phpmailer') != 1 || OOAddon::getVersion('phpmailer') < "2.8") {
	$REX['ADDON']['install']['community'] = 0;
	$REX['ADDON']['installmsg']['community'] = $I18N->msg('community_install_phpmailer_version_problem','2.8');

}elseif(OOAddon::isAvailable('xform') != 1 || OOAddon::getVersion('xform') < "2.8") {
	$REX['ADDON']['install']['community'] = 0;
	$REX['ADDON']['installmsg']['community'] = $I18N->msg('community_install_xform_version_problem','2.8');

}elseif(OOPlugin::isAvailable('xform','manager') != 1 || OOPlugin::getVersion("xform", "manager") < "2.8") {
	$REX['ADDON']['install']['community'] = 0;
	$REX['ADDON']['installmsg']['community'] = $I18N->msg('community_install_xform_manager_version_problem','2.8');

}elseif(OOPlugin::isAvailable('xform','email') != 1 || OOPlugin::getVersion("xform", "email") < "2.8") {
	$REX['ADDON']['install']['community'] = 0;
	$REX['ADDON']['installmsg']['community'] = $I18N->msg('community_install_xform_email_version_problem','2.8');

}else
{
	$REX['ADDON']['install']['community'] = 1;

}

?>