<?php

/**
 * Community - comment
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

$REX['ADDON']['install']['comment'] = 1;
$REX['ADDON']['installmsg']['comment'] = ""; // $I18N->msg('com_comment_install','2.8');

// $info = rex_generateAll(); // quasi kill cache ..

function rex_com_comment_install() {
	$r = new rex_xform_manager;
	$r->generateAll();
}
rex_register_extension('OUTPUT_FILTER', 'rex_com_comment_install');

?>