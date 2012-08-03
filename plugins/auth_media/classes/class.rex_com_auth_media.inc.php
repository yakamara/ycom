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

  function rex_com_auth_media($oomedia)
  {
    global $REX;

    $this->MEDIA = $oomedia;

    $this->filepath = $REX['MEDIAFOLDER'];
    $this->filename = $this->MEDIA->getFileName();
    $this->fullpath = $this->filepath.'/'.$this->filename;
  }

  function getMediaByFilename($filename)
  {
    $oomedia = OOMedia::getMediaByFileName($filename);

    return new rex_com_auth_media($oomedia);
  }

  function send()
  {
    echo "hohoh";exit;
  
  
    if($this->xsendfile)
    {
      header('Content-type: application/octet-stream');
      header('Content-disposition: attachment; filename="'.$this->filename.'"');
      header('X-SendFile: '.$this->fullpath);
    }
    else
    {
      header("Pragma: public");
      header("Expires: 0");
      header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      header("Content-Type: application/force-download");
      header("Content-Type: application/octet-stream");
      header("Content-Type: application/download");
      header("Content-Transfer-Encoding: binary");
      header("Content-Length: ".$this->MEDIA->getSize());
      header("Content-Disposition: attachment; filename=".$this->filename.";");
      
      @readfile($this->fullpath);
    }
    
    exit;
  }

  function checkPerm()
  {
    global $REX;
    
    ## if no access rule - grant access
    if($this->MEDIA->getValue('med_com_auth_media_comusers') == '' || $this->MEDIA->getValue('med_com_auth_media_comusers') == '||')
      if($this->MEDIA->getValue('med_com_groups') == '' || $this->MEDIA->getValue('med_com_groups') == '||')
        return true;

    ## true if user is in one or more required groups
    if(isset($REX['COM_USER']))
    {
      if($this->MEDIA->getValue('med_com_auth_media_comusers') != '' && $this->MEDIA->getValue('med_com_auth_media_comusers') != '||')
        return true;
      
      $media_groups = explode("|",$this->MEDIA->getValue('med_com_groups'));
      $user_groups = explode(",",$REX["COM_USER"]->getValue("rex_com_group"));

      foreach($media_groups as $group)
        if($group != "" && in_array($group,$user_groups))
          return true;
    }

    return false;
  }

  /*
  * Use this option if mod_xsenfile on Apache is available
  */
  function setXsendfile($option = true)
  {
    $this->xsendfile = $option;
  }
  
  /**
 * Does the job on frontend
 */
  function setMediaEP()
  {
    global $REX;
  
    $file = rex_request("rex_com_auth_media_filename", 'string');

    if($file)
    {
      $media = rex_com_auth_media::getMediaByFilename($file);
      $media->setXsendfile($REX['ADDON']['community']['plugin_auth_media']['xsendfile']);
  
      if($media->checkPerm())
        $media->send();
      else
        header('Location: '.rex_getUrl($REX['ADDON']['community']['plugin_auth']['article_withoutperm'],'',array($REX['ADDON']['community']['plugin_auth']['request']['ref'] => urlencode($_SERVER['REQUEST_URI'])),'&'));
        //echo rex_getUrl($REX['ADDON']['community']['plugin_auth']['article_withoutperm'],'',array($REX['ADDON']['community']['plugin_auth']['request']['ref'] => urlencode($_SERVER['REQUEST_URI'])) );
  
      exit;
    }
  }
  
  /**
   * Checks perms for Image-Manager, Image-Manager EP and Image-Resize
   * @param array $params
   * @return boolean
   */
  function setMediaEPImages($params)
  {
    global $REX;
  
    if($params['extension_point'] == 'IMAGE_RESIZE_SEND')
      $file = $params['filename'];
    else
      $file = $params['img']['file'];
  
    ## get auth - isn't loaded yet
    require_once $REX["INCLUDE_PATH"]."/addons/community/plugins/auth/inc/auth.php";
      
    $media = rex_com_auth_media::getMediaByFilename($file);
    if($media->checkPerm())
      return true;
  
    return false;
  }
  
  /**
 * Updates /files/.htaccess file according to user config
 * @return boolean
 */
  function updateHtaccess()
  {
    global $REX;
    
    $unsecure_fileext = implode('|',explode(',',$REX['ADDON']['community']['plugin_auth_media']['unsecure_fileext']));
    
    ## build new content
    $new_content = '### auth_media'.PHP_EOL;
    $new_content .= 'RewriteCond %{REQUEST_URI} !files/.*/.*'.PHP_EOL;
    $new_content .= 'RewriteCond %{REQUEST_URI} !files/(.*).('.$unsecure_fileext.')$'.PHP_EOL;
    $new_content .= 'RewriteRule ^(.*):SSL$ https://%{HTTP_HOST}/?rex_com_auth_media_filename=\$1 [R=301,L]'.PHP_EOL;
    $new_content .= 'RewriteRule ^(.*):NOSSL$ http://%{HTTP_HOST}/?rex_com_auth_media_filename=\$1 [R=301,L]'.PHP_EOL;
    $new_content .= '### /auth_media'.PHP_EOL;
    
    ## write to htaccess
    $path = $REX['HTDOCS_PATH'].'files/.htaccess';
    $old_content = rex_get_file_contents($path);
    
    if(preg_match("@(### auth_media.*### /auth_media)@s",$old_content) == 1)
    {  
      $new_content = preg_replace("@(### auth_media.*### /auth_media)@s", $new_content, $old_content);
      return rex_put_file_contents($path, $new_content);
    }
    
    return false;
  }
  
  /**
   * Copy File from source to target
   * returns true on success
   * @param string $file
   * @param string $source
   * @param string $target
   * @return boolean
   */
  function copyfile($file, $source, $target)
  {
    global $REX;
  
    if(!rex_is_writable($target))
    {
      echo rex_warning('Keine Schreibrechte für das Verzeichnis "'.$target.'" !');
      return false;
    }
  
    if(!is_file($source.$file))
    {
      echo rex_warning('Datei "'.$source.$file.'" ist nicht vorhanden und kann nicht kopiert werden!');
      return false;
    }
  
    if(is_file($target.$file))
    {
      if(!rename($target.$file,$target.date("d.m.y_H.i.s_").$file))
      {
        echo rex_warning('Datei "'.$target.$file.'" konnte nicht umbenannt werden!');
        return false;
      }
    }
  
    if(!copy($source.$file,$target.$file))
    {
      echo rex_warning('Datei "'.$target.$file.'" konnte nicht geschrieben werden!');
      return false;
    }
  
    if(!chmod($target.$file,$REX['FILEPERM']))
    {
      echo rex_warning('Rechte für "'.$target.$file.'" konnten nicht gesetzt werden!');
      return false;
    }
  
    echo rex_info('Datei "'.$target.$file.'" wurde erfolgreich angelegt.');
    return true;
  }
  
  
}