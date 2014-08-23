<?php

/**
* Plugin Media-Access
* @author m.lorch[at]it-kult[dot]de Markus Lorch, Jan kristinus
* @author <a href="http://www.it-kult.de">www.it-kult.de</a>
* @version 1.0
*
* Aufruf - wenn Download erzwungen werden soll: files/dateiname.jpg?media_download=1
*
*/

$mypage = "auth_media";
$REX['ADDON']['version'][$mypage] = '4.7.1';
$REX['ADDON']['author'][$mypage] = 'Markus Lorch, Jan Kristinus';
$REX['ADDON']['supportpage'][$mypage] = 'www.it-kult.de';
$REX['ADDON']['community']['plugin_auth_media']['xsendfile'] = 0;

// --- DYN
$REX['ADDON']['community']['plugin_auth_media']['auth_active'] = 1;
$REX['ADDON']['community']['plugin_auth_media']['unsecure_fileext'] = "png,gif,ico,css,js,swf";
$REX['ADDON']['community']['plugin_auth_media']['error_article_id'] = 1;
// --- /DYN

## Loading Plugin
include_once $REX["INCLUDE_PATH"]."/addons/community/plugins/auth_media/classes/class.rex_com_auth_media.inc.php";

## Loading backend files
if($REX["REDAXO"] && $REX['USER']) {
  if(isset($I18N) && is_object($I18N)) {
    $I18N->appendFile($REX['INCLUDE_PATH'].'/addons/community/plugins/auth_media/lang');
  }

  $REX['ADDON']['community']['SUBPAGES'][] = array('plugin.auth_media',$I18N->msg('com_auth_media'));
}

## Loading frontend
if($REX['ADDON']['community']['plugin_auth_media']['auth_active']) {
  ## init auth media
  function rex_com_auth_media_init($params) {
    global $REX, $I18N;

    rex_com_auth_media::getMedia();

  }

  ## register EPs (only if required)
  if(rex_request("rex_com_auth_media_filename","string") != "") {
    rex_register_extension('COM_AUTH_LOGIN_PROCESS_END', 'rex_com_auth_media_init');

  }

  // image_manager
  function rex_com_auth_media_im($params) {

    if(!empty($params["subject"]["rex_img_init"])) {
      if( ($media = OOMedia::getMediaByFileName($params["subject"]["rex_img_file"])) && rex_com_auth_media::checkPerm($media) ) {
      } else {
        rex_com_auth_media::forwardErrorPage();
      }
    }

  }
  rex_register_extension('IMAGE_MANAGER_INIT', 'rex_com_auth_media_im');

}


