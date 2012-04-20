<?php

/**
 * COM - Plugin - Newsletter
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

/*
	UserFelder hinzufŸgen
	- newsletter, bool
	- newsletter_last_id, varchar
  + Felder abgleichen
*/

$error = '';

if ($error != '')
{
  $REX['ADDON']['installmsg']['newsletter'] = $error;
  
}else
{
  $REX['ADDON']['install']['newsletter'] = true;

	// xform refresh
	function rex_com_newsletter_install() {
		$r = new rex_xform_manager;
		$r->generateAll();
	}
	rex_register_extension('OUTPUT_FILTER', 'rex_com_newsletter_install');

}

?>