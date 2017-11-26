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
		if ($REX['ADDON']['community']['plugin_auth_media']['xsendfile']) { // FIXME
			header('Content-type: ' . $media->getType());
			header('Content-disposition: attachment; filename="' . $media->getFileName() . '"');
			header('X-SendFile: ' . rex_path::media($media->getFileName()));
		}
		else {
			header('Pragma: public');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Content-Type: ' . $media->getType());
			header('Content-Transfer-Encoding: binary');
			header('Content-Length: ' . $media->getSize());
			if (rex_request('media_download', 'int') == 1) {
				header('Content-Type: application/force-download');
				header('Content-Type: application/download');
				header('Content-Disposition: attachment; filename=' . $media->getFileName() . ';');
			}
			@readfile(rex_path::media($media->getFileName()));
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
		if(\rex::getUser()) {
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
	 */
	static function getMedia() {
		$filename = rex_request('rex_ycom_auth_media_filename', 'string');
		if($filename) {
			if(($media = rex_media::get($filename)) && self::checkPerm($media) ) {
				self::send($media);
			}
			else {
			    header('Location: ' . rex_getUrl(rex_config::get('ycom', 'article_id_jump_denied'), '', ['rex_ycom_auth_ref' => urlencode($_SERVER['REQUEST_URI'])], '&'));
			}
			exit;
		}
	}

	/**
	 * Create .htaccess file in /media folder.
	 * @param booleam $create If TRUE, file is created, otherwise file will be deleted
	 * @param string[] $unsecure_fileext array with files extensions that will not be protected
	 * @return boolean TRUE if successful, otherwise FALSE
	 */
	static function manageHtaccess($create = TRUE, $unsecure_fileext = []) {
		$file = rex_path::media('.htaccess');
		if ($create) {
			$unsecure_fileext = implode('|', $unsecure_fileext);

			## build new content
			$new_content = 'RewriteEngine On' . PHP_EOL;
			$new_content .= 'RewriteBase /' . PHP_EOL . PHP_EOL;
			$new_content .= 'RewriteCond %{REQUEST_URI} !media/.*/.*' . PHP_EOL;
			$new_content .= 'RewriteCond %{REQUEST_URI} !media/(.*).(' . $unsecure_fileext . ')$' . PHP_EOL . PHP_EOL;
			$new_content .= 'RewriteRule ^(.*)$ /?rex_ycom_auth_media_filename=$1&%{QUERY_STRING}' . PHP_EOL;

			return rex_file::put($file, $new_content);
		}
		else {
			@unlink($file);
			return TRUE;
		}
	}
}