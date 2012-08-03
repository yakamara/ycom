<?php

/**
* Plugin Media-Access
* @author m.lorch[at]it-kult[dot]de Markus Lorch
* @author <a href="http://www.it-kult.de">www.it-kult.de</a>
* @version 1.0
* 
* Some parts are inspired from rexseo - thx jeandeluxe ;)
*/

$mypage = "auth_media";
$REX['ADDON']['version'][$mypage] = '2.9.3';
$REX['ADDON']['author'][$mypage] = 'Markus Lorch, Jan Kristinus';
$REX['ADDON']['supportpage'][$mypage] = 'www.it-kult.de';

$REX['ADDON']['community']['plugin_auth_media']['xsendfile'] = 0;

/*
 * Options
 */

// --- DYN
$REX['ADDON']['community']['plugin_auth_media']['unsecure_fileext'] = "jpg,jpeg,png,gif,ico,css,js,swf";
// --- /DYN

// --- END OF CONFIG ---
// --- DON'T CHANGE ANYTHING BELOW THIS LINE ---

/*
* Loading Plugin
*/

## only required in frontend
include $REX["INCLUDE_PATH"]."/addons/community/plugins/auth_media/classes/class.rex_com_auth_media.inc.php";


if($REX["REDAXO"] && $REX['USER'])
{
  ## Include lang files
  if(isset($I18N) && is_object($I18N))
    $I18N->appendFile($REX['INCLUDE_PATH'].'/addons/community/plugins/auth_media/lang');


  ## register to community addon navigation
  $REX['ADDON']['community']['SUBPAGES'][] = array('plugin.auth_media','auth_media');

}else
{
  
  ## starts session if required
  if(session_id() == '')
    session_start();
  
  ## Register extension Point for rex_com_auth_media function
  function rex_com_auth_media_setMediaEP($params){ return rex_com_auth_media::setMediaEP($params); }
  rex_register_extension('ADDONS_INCLUDED', 'rex_com_auth_media_setMediaEP');

  ## register extension points if needed
  $unsecure_fileext = explode(',',$REX['ADDON']['community']['plugin_auth_media']['unsecure_fileext']);
  $image_fileext = array('jpeg', 'jpg', 'gif', 'png');
  
  if(count(array_intersect($image_fileext, $unsecure_fileext)) < count($image_fileext) && $_SESSION[$REX['INSTNAME']]['UID'] > 0)
  {
    function rex_com_auth_media_setMediaEPImages($params){ return rex_com_auth_media::setMediaEPImages($params); }
    rex_register_extension('IMAGE_SEND', 'rex_com_auth_media_setMediaEPImages'); //Image-Manager & Image-Manager EP
    rex_register_extension('IMAGE_RESIZE_SEND', 'rex_com_auth_media_setMediaEPImages'); //Image-Resize
  }

}
