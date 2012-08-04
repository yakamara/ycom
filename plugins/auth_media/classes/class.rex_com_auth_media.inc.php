<?php

/**
* Plugin Media-Access - rex_com_auth_media class
* @author m.lorch[at]it-kult[dot]de Markus Lorch
* @author <a href="http://www.it-kult.de">www.it-kult.de</a>
*/

class rex_com_auth_media
{
  var $filename;
  var $filepath;
  var $fullpath;
  var $xsendfile = false;
  var $MEDIA;

  function rex_com_auth_media()
  {
  }

  function send($media)
  {
    global $REX;
  
    if($REX['ADDON']['community']['plugin_auth_media']['xsendfile'])
    {
      header('Content-type: '.$media->getType());
      header('Content-disposition: attachment; filename="'.$media->getFileName().'"');
      header('X-SendFile: '.$media->getFullPath());
    }
    else
    {

      header("Pragma: public");
      header("Expires: 0");
      header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      header("Content-Type: application/force-download");
      header("Content-Type: ".$media->getType());
      header("Content-Type: application/download");
      header("Content-Transfer-Encoding: binary");
      header("Content-Length: ".$media->getSize());
      header("Content-Disposition: attachment; filename=".$media->getFileName().";");
      
      @readfile($media->getFullPath());
    }
    
    exit;
  }

  function checkPerm($media)
  {
    global $REX;
    
    ## starts session if required
    if(session_id() == '')
      session_start();
    
    if($_SESSION[$REX['INSTNAME']]['UID'] > 0)
      return true;

    ## if no access rule - grant access
    if($media->getValue('med_com_auth_media_comusers') == '' || $media->getValue('med_com_auth_media_comusers') == '||')
      if($media->getValue('med_com_groups') == '' || $media->getValue('med_com_groups') == '||')
        return true;

    ## true if user is in one or more required groups
    $me = rex_com_auth::getUser();
    if($me)
    {

      if($media->getValue('med_com_auth_media_comusers') != '' && $media->getValue('med_com_auth_media_comusers') != '||')
        return true;
      
      $media_groups = explode("|",$media->getValue('med_com_groups'));
      $user_groups = explode(",",$me->getValue("rex_com_group"));

      foreach($media_groups as $group)
        if($group != "" && in_array($group,$user_groups))
          return true;
    }

    return false;
  }
  
  function getMedia()
  {
    global $REX;
  
    $filename = rex_request("rex_com_auth_media_filename", 'string');
    if($filename)
    {
      if( ($media = OOMedia::getMediaByFileName($filename)) && rex_com_auth_media::checkPerm($media) )
      {
        rex_com_auth_media::send($media);
      }else
      {
        rex_com_auth_media::forwardErrorPage();
      }
      exit;
    }
  }
  
  function forwardErrorPage()
  {
    global $REX;
    
    header('Location: /'.rex_getUrl($REX['ADDON']['community']['plugin_auth_media']['error_article_id'],'',array($REX['ADDON']['community']['plugin_auth_media']['request']['ref'] => urlencode($_SERVER['REQUEST_URI'])),'&'));
  
    exit;
  }
  
  function createHtaccess()
  {
    global $REX;
    
    $path = $REX['HTDOCS_PATH'].'files/.htaccess';
    
    if($REX['ADDON']['community']['plugin_auth_media']['auth_active'])
    {
      $unsecure_fileext = implode('|',explode(',',$REX['ADDON']['community']['plugin_auth_media']['unsecure_fileext']));
      
      ## build new content
      $new_content = 'RewriteEngine On'.PHP_EOL;
      $new_content .= 'RewriteBase /'.PHP_EOL.PHP_EOL;
      $new_content .= 'RewriteCond %{REQUEST_URI} !files/.*/.*'.PHP_EOL;
      $new_content .= 'RewriteCond %{REQUEST_URI} !files/(.*).('.$unsecure_fileext.')$'.PHP_EOL.PHP_EOL;
      $new_content .= 'RewriteRule ^(.*)$ /?rex_com_auth_media_filename=$1'.PHP_EOL;
      
      return rex_put_file_contents($path, $new_content);
    }else
    {
      return unlink($path);  
    }
  
  }
  
}