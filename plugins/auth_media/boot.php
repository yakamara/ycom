<?php
rex_extension::register('PACKAGES_INCLUDED', 'ycom_auth_media_file', rex_extension::LATE);

if(rex::isBackend() && rex_url::currentBackendPage() == "index.php?page=mediapool/media") {
	rex_extension::register('OUTPUT_FILTER', 'append_ycom_auth_media_script');
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

/**
 * Adds JS to media pool, showing goup field only if needed
 * @param rex_extension_point $ep Redaxo extension point
 */
function append_ycom_auth_media_script(rex_extension_point $ep) {
	// Insert befor </body> element
	$insert_body = "<script type='text/javascript'>". PHP_EOL
	."function change_ycom_auth_media_fields() {". PHP_EOL
	."	if($('#rex-metainfo-med_ycom_auth_media_users option:selected').val() != 2) {". PHP_EOL
	."		$('#rex-metainfo-med_ycom_auth_media_groups').closest('.rex-form-group').hide();". PHP_EOL
	."	}". PHP_EOL
	."	else {". PHP_EOL
	."		$('#rex-metainfo-med_ycom_auth_media_groups').closest('.rex-form-group').show();". PHP_EOL
	."	}". PHP_EOL
	."}". PHP_EOL		

	// hide groups if not needed on load
	."$(document).on('rex:ready', function () {". PHP_EOL
	."	change_ycom_auth_media_fields();". PHP_EOL
	."	$('#rex-metainfo-med_ycom_auth_media_users').on('change', function() {". PHP_EOL
	."		change_ycom_auth_media_fields();". PHP_EOL
	."	});". PHP_EOL
	."});". PHP_EOL
	."</script>";
	$ep->setSubject(str_replace('</body>', $insert_body .'</body>', $ep->getSubject()));
}