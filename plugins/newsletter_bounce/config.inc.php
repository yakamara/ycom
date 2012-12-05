<?php
$REX['ADDON']['version']['newsletter_bounce'] = '1.0.0';
$REX['ADDON']['author']['newsletter_bounce'] = 'Yakamara/WebDevOne';
$REX['ADDON']['supportpage']['newsletter_bounce'] = 'www.redaxo.org';

// add sub page
if ($REX["REDAXO"] && $REX['USER'] && $REX['USER']->isAdmin("rights","admin[]")) {
	$REX['ADDON']['community']['SUBPAGES'][] = array('plugin.newsletter_bounce', 'Newsletter Bounce');
}

// --- DYN
$REX['ADDON']['newsletter_bounce']['mailhost'] = "";
$REX['ADDON']['newsletter_bounce']['mailbox_username'] = "";
$REX['ADDON']['newsletter_bounce']['mailbox_password'] = "";
$REX['ADDON']['newsletter_bounce']['port'] = 143;
$REX['ADDON']['newsletter_bounce']['service'] = "imap";
$REX['ADDON']['newsletter_bounce']['service_option'] = "notls";
$REX['ADDON']['newsletter_bounce']['boxname'] = "INBOX";
$REX['ADDON']['newsletter_bounce']['user_table'] = "rex_com_user";
$REX['ADDON']['newsletter_bounce']['user_table_email_field'] = "email";
$REX['ADDON']['newsletter_bounce']['user_table_bounce_counter_field'] = "bounce_counter";
$REX['ADDON']['newsletter_bounce']['test_mode'] = "";
// --- /DYN

if ($REX['REDAXO']) {
	// add lang file
	$I18N->appendFile($REX['INCLUDE_PATH'] . '/addons/community/plugins/newsletter_bounce/lang/');

	// includes
	include($REX["INCLUDE_PATH"]."/addons/community/plugins/newsletter_bounce/lib/rules.inc.php");
	include($REX["INCLUDE_PATH"]."/addons/community/plugins/newsletter_bounce/lib/class.rex_com_newsletter_bounce.inc.php");
}

