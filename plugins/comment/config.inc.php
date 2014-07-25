<?php

/**
 * Plugin Comment
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

/*

TODOS:

- Standard Styles einbauen
- "Nur für eingeloggte" / "für alle" Option einbauen
	- Login mit Facebook und Co. einbauen
	- Jump URL beim Login
	- Captcha wenn nicht eingeloggt

- Allgemeine Bilddarstellungslogik einbauen . Gravatarlogik rein
- Benachrichtigung einbauen, Xform Emailtemplate installierbar machen.
- getChildren /reply_id noch rein .. siehe wordpress logik
- Sprachenunterscheidung I18N einbauen .. CLANG -> I18N verbindung

*/

$mypage = "comment";
$REX['ADDON']['version'][$mypage] = '4.7';
$REX['ADDON']['author'][$mypage] = 'Jan Kristinus';
$REX['ADDON']['supportpage'][$mypage] = 'www.yakamara.de/tag/redaxo/';

if (isset($I18N) && is_object($I18N)) {
  $I18N->appendFile($REX['INCLUDE_PATH'] . '/addons/community/plugins/comment/lang');
}

include $REX["INCLUDE_PATH"]."/addons/community/plugins/comment/classes/class.rex_com_comment.inc.php";
include $REX["INCLUDE_PATH"]."/addons/community/plugins/comment/classes/class.rex_com_comments.inc.php";

if($REX["REDAXO"] && !$REX['SETUP'])
{
	if ($REX['USER'])
	{
		$REX['ADDON']['community']['SUBPAGES'][] = array('plugin.comment' , $I18N->msg("com_comments"));
	}
}

?>