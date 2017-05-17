<?php

class rex_ycom_media_request
{
	public static function init(rex_extension_point $ep)
	{
		if (!rex::isBackend())
		{
			self::getMedia();
		}
	}

	public static function getMedia()
    {
    	$requested_file = trim(rex_get('f', 'string', ''));
    	
		// Prüfe ob eine Datei übergeben wurde
		if ($requested_file !== '')
		{
			$redirect_article = rex_article::getNotfoundArticleId();
			$status = '404 Not Found';

			if (file_exists(rex_path::media($requested_file)))
			{
				if(rex_ycom_media::checkPerm($requested_file))
				{
					// Nutzer darf auf die Datei zugreifen...
					$file = rex_path::media() . $requested_file;
					$contenttype = 'application/octet-stream';

					// soll kein Download erzwungen werden, ändere attachment in inline		
					rex_response::sendFile($file, $contenttype, $contentDisposition = 'attachment');
					exit();
				}

				if(rex_ycom_auth::getUser())
				{
					$redirect_article = rex_addon::get('ycom')->getPlugin('auth')->getConfig('article_id_jump_denied');
					$status = '401 Unauthorized';
				}
				else
				{
					$redirect_article = rex_addon::get('ycom')->getPlugin('auth')->getConfig('article_id_jump_not_ok');
					$status = '403 Forbidden';
				}
			}

			// um Endlosweiterleitungen zu verhindern prüfen, ob die Weiterleitungs-ID unterschiedlich vom aktuellen Artikel ist
			if($redirect_article != rex_article::getCurrentId())
			{
				$url = rex_getUrl($redirect_article);
				$url = preg_replace('/^\.\//','../',$url); // sonst würde man auf /media/index.php weitergeleitet werden...

				rex_response::setStatus($status);
				rex_response::sendRedirect($url);
			}

			// bei ungültigen Weiterleitungszielen einfach einen Fehler auswerfen.
			throw new rex_exception('File ' . $requested_file . ' not found.');

			exit();
		}
    }
}
