<?php
// Loading frontend
if($this->getConfig('auth_media_active') == 'true') {
	// Register extension point if download is pending
	if(rex_request("rex_ycom_auth_media_filename", "string") != "") {
		rex_extension::register('YCOM_AUTH_LOGIN_PROCESS_END', function (rex_extension_point $ep) {
			rex_ycom_auth_media::getMedia();
		});
	}

	// Media manager
/*
	function rex_ycom_auth_media_im($params) {

    if(!empty($params["subject"]["rex_img_init"])) {
      if( ($media = OOMedia::getMediaByFileName($params["subject"]["rex_img_file"])) && rex_ycom_auth_media::checkPerm($media) ) {
      } else {
        rex_ycom_auth_media::forwardErrorPage();
      }
    }

  }
  rex_register_extension('IMAGE_MANAGER_INIT', 'rex_ycom_auth_media_im');
*/
}