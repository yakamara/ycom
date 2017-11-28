<?php

/**
 * Plugin Media-Access - rex_ycom_auth_media class
 */

class rex_ycom_auth_media {
	var $filename;
	var $filepath;
	var $fullpath;
	
	/**
	 * @var boolean If XSendFile PHP Extension is installed, it can be used to transmit file.
	 */
	var $xsendfile = false;
	
	var $MEDIA;

	function rex_ycom_auth_media() {
	}

	/**
	 * Sends media file to client
	 * @param rex_media $media Redaxo media object
	 */
	private static function send($media) {
		if(function_exists('apache_get_modules') && in_array('mod_xsendfile', apache_get_modules()) && extension_loaded('mod_xsendfile')) {
			// Use X-SendFile TODO Testing
			if($media->getType() != '') {
				header('Content-type: ' . $media->getType());
			}
			header('Content-disposition: attachment; filename="' . $media->getFileName() . '"');
			header('X-SendFile: ' . rex_path::media($media->getFileName()));
		}
		else {
			header('Pragma: public');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			if($media->getType() != '') {
				header('Content-Type: ' . $media->getType());
			}
			
	        // Send content length so browser can show loading bar
	        if (!ini_get('zlib.output_compression')) {
		        header('Content-Length: ' . filesize(rex_path::media($media->getFileName())));
			}
			
			header('Content-Disposition: attachment; filename=' . $media->getFileName() . ';');
			
			ob_clean();
			flush();
			
			readfile(rex_path::media($media->getFileName()));
		}
		exit;
	}

	/**
	 * Checks if user has permission to access file. If user is logged in in 
	 * Redaxo, permission needs to be granted, due to be able to manage media pool.
	 * @param rex_media $media Redaxo media object
	 * @return boolean TRUE if user may access file, otherwise false.
	 */
	public static function checkPerm($media) {
		// Is backend user logged in? Return TRUE to be able to access media pool files
		if(rex_backend_login::hasSession()) {
			return TRUE;
		}

		// User access rules
		if($media->getValue('med_ycom_auth_media_users') == '' || $media->getValue('med_ycom_auth_media_users') == '3') {
	        return TRUE;
		}
		else if($media->getValue('med_ycom_auth_media_users') == '1' && rex_ycom_user::getMe()) {
	        return TRUE;
		}
		else if($media->getValue('med_ycom_auth_media_users') == '2' && $user = rex_ycom_user::getMe()) {
			$group_ids = preg_grep('/^\s*$/s', explode("|", $media->getValue('med_ycom_auth_media_groups')), PREG_GREP_INVERT);
			foreach($group_ids as $group_id) {
				if($user->isInGroup($group_id)) {
					return TRUE;
				}
			}
		}

		return FALSE;
	}

	/**
	 * Get Redaxo media object that is requested, check permissions and send it
	 * if permissions are OK. If not, forward to YCom error page. If filename is
	 * not found, nothing is done.
	 * @param string $requested_filename Requested filename
	 */
	static function getMedia($requested_filename) {
		if($requested_filename) {
			$requested_media = rex_media::get($requested_filename);
			if($requested_media instanceof rex_media && self::checkPerm($requested_media)) {
				if(file_exists(rex_path::media($requested_file))) {
					set_time_limit(0);
					// Send media
					self::send($requested_media);					
				}
				else {
					// Error file not found
				    header('Location: ' . rex_getUrl(rex_article::getNotfoundArticleId()));
				}
			}
			else {
			    header('Location: ' . rex_getUrl(rex_config::get('ycom/auth', 'article_id_jump_denied'), '', ['rex_ycom_auth_media_filename' => $requested_filename], '&'));
			}
			exit;
		}
	}

	/**
	 * Create .htaccess file in /media folder.
	 * @param booleam $insert If TRUE, file is created, otherwise file will be deleted
	 * @param string[] $fileext array with files extensions mentioned in .htaccess
	 * @return boolean TRUE if successful, otherwise FALSE
	 */
	static function manageHtaccess($insert = TRUE, $fileext = []) {
		// If YRewrite manages .htaccess file
		if(rex_addon::get('yrewrite')->isAvailable()) {
			$htaccess = file_get_contents(rex_path::frontend('.htaccess'));

			$ycom_auth_media_marker_start = '# START ycom/auth_media Plugin: protected file extensions';
			$ycom_auth_media_marker_end = '# END ycom/auth_media Plugin: protected file extensions';

			// Remove old rewrite stuff
			if(strpos($htaccess, $ycom_auth_media_marker_start) > 0) {
				$top = explode($ycom_auth_media_marker_start, $htaccess);
				$top = reset($top);
				$bottom = explode($ycom_auth_media_marker_end, $htaccess);
				$bottom = end($bottom);
				$htaccess = $top . $bottom;
				$htaccess = preg_replace('/\n(\s*\n){2,}/', "\n\n", $htaccess);
			}

			// Insert new rewrite stuff
			if ($insert) {
				$marker = 'RewriteRule ^imagetypes/([^/]*)/([^/]*) %{ENV:BASE}/index.php?rex_media_type=$1&rex_media_file=$2';
				$insert = $marker . PHP_EOL;
				$insert .= '    '. $ycom_auth_media_marker_start . PHP_EOL;
				$insert .= '    RewriteRule ^/?media/(.*\.('. implode('|', $fileext) .'))$ /index.php?rex_ycom_auth_media_filename=$1 [L]'. PHP_EOL;
				$insert .= '    '.$ycom_auth_media_marker_end;
				$htaccess = str_replace($marker, $insert, $htaccess); // Remove double blank lines
			}

			file_put_contents(rex_path::frontend('.htaccess'), $htaccess);
		}
		else {
			$htaccess_filename = rex_path::frontend('.htaccess');

			if ($insert) {
				// Build .htaccess content
				$new_content = '# REWRITING' . PHP_EOL;
				$new_content .= '<IfModule mod_rewrite.c>' . PHP_EOL;
				$new_content .= '    # ENABLE REWRITING' . PHP_EOL;
				$new_content .= '    RewriteEngine On' . PHP_EOL;
				$new_content .= '    RewriteCond %{REQUEST_URI}::$1 ^(/.+)/(.*)::\2$' . PHP_EOL;
				$new_content .= '    RewriteRule ^(.*) - [E=BASE:%1]' . PHP_EOL . PHP_EOL;
				$new_content .= '    RewriteRule ^media/(.*\.('. implode('|', $fileext) .'))$ %{ENV:BASE}/index.php?rex_ycom_auth_media_filename=$1 [L]'. PHP_EOL;
				$new_content .= '    RewriteCond %{REQUEST_FILENAME} !-f'. PHP_EOL;
				$new_content .= '    RewriteCond %{REQUEST_FILENAME} !-d'. PHP_EOL;
				$new_content .= '    RewriteCond %{REQUEST_FILENAME} !-l'. PHP_EOL;
				$new_content .= '    RewriteRule ^(.*)$ %{ENV:BASE}/index.php?%{QUERY_STRING} [L]'. PHP_EOL;
				$new_content .= '</IfModule>';

				return rex_file::put($htaccess_filename, $new_content);
			}
			else {
				@unlink($htaccess_filename);
				return TRUE;
			}
		}
	}
}