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
  $REX['ADDON']['installmsg']['newsletter'] = $error;
else
  $REX['ADDON']['install']['newsletter'] = true;

?>