<?php
rex_extension::register('PACKAGES_INCLUDED', 'ycom_auth_media_file', rex_extension::LATE);

if(rex::isBackend() && (rex_url::currentBackendPage() == "index.php?page=mediapool/media" || rex_url::currentBackendPage() == "index.php?page=mediapool/sync")) {
	rex_view::addJsFile($this->getAssetsUrl('ycom_auth_media_backend.js'));
}

/**
 * Check file permissions.
 */
function ycom_auth_media_file() {
	// Check file download
	if(rex_config::get('ycom/auth_media', 'auth_media_active') == 'true') {
		$requested_filename = trim(rex_get('rex_ycom_auth_media_filename', 'string', ''));
		if($requested_filename != '' && rex_config::get('ycom/auth', 'article_id_jump_denied') != rex_article::getCurrentId()) {
			if(!rex_ycom_user::getMe()) {
				// Not logged in - store filename in Session
				if (session_status() == PHP_SESSION_NONE) {
					session_start();
				}
				$_SESSION['rex_ycom_auth_media_filename'] = $requested_filename;
			}
			rex_ycom_auth_media::getMedia($requested_filename);
		}
		elseif (rex_config::get('ycom/auth', 'article_id_jump_ok') == rex_article::getCurrentId() && rex_ycom_user::getMe()) {
			// Just logged in and download still in session: perform download
			if (session_status() == PHP_SESSION_NONE) {
				session_start();
			}
			$requested_filename = $_SESSION['rex_ycom_auth_media_filename'];
			unset($_SESSION['rex_ycom_auth_media_filename']);
			rex_ycom_auth_media::getMedia($requested_filename);
		}
	}
}