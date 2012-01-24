<?php
//TODO: write own user-class

global $REX, $I18N;

if(!isset($_SESSION))
  @session_start();

unset($REX['COM_USER']);

/*
* Init
*/
## local
$login_key = rex_com_auth::getLoginKey();
$session_key = '';
$user_session = '';
$redirect = '';

## Request variables
$login_name = stripslashes(rex_request($REX['ADDON']['community']['plugin_auth']['request']['name'],"string"));
$login_psw = stripslashes(rex_request($REX['ADDON']['community']['plugin_auth']['request']['psw'],"string"));
$login_stay = rex_request($REX['ADDON']['community']['plugin_auth']['request']['stay'],"string");
$referer = rex_request($REX['ADDON']['community']['plugin_auth']['request']['ref'],"string");
$logout = rex_request($REX['ADDON']['community']['plugin_auth']['request']['logout'],"int");

## Sessions
if(isset($_SESSION[$login_key]))
  $user_session = $_SESSION[$login_key];

## Checking for existing Cookie
if($REX['ADDON']['community']['plugin_auth']['stay_active'])
  if(isset($_COOKIE[$login_key]))
    $session_key = rex_cookie($login_key,'string');

/*
* Authentification
*/
## if newlogin or Sessions available setting up User-Object
if(($login_name && $login_psw) || !empty($user_session['UID']) || $session_key)
{
  $login_success = false;
  
  // -> EP COM_REGISTER_AUTHTYPE
  $authtypes = array(/* typename => array('function' => , 'passwdsource' => , 'fields' => array()) */);
  $authtypes = rex_register_extension_point('COM_REGISTER_AUTHTYPE',$authtypes);

  ## User object
  $REX['COM_USER'] = new rex_login();
  $REX['COM_USER']->setSqlDb(1);
  $REX['COM_USER']->setSysID($login_key);
  $REX['COM_USER']->setSessiontime(7200);
  $REX['COM_USER']->setUserID("rex_com_user.id");
  $REX['COM_USER']->setUserquery("select * from rex_com_user where id='USR_UID' and status>0");
  
  ## --- NEW LOGIN ---
  if($login_name)
  {
    
    ## TODO:
    // if user existing in community dbase
          // get authtype und use auth function
              // on success -> do login
    // if not
          // looping all registered authtypes
              // if no user -> login fail
              // else -> use auth function
                  // on success -> sync to community dbase and do login
    
    ## Hash password if required
    if($REX['ADDON']['community']['plugin_auth']['passwd_hashed'])
      $REX['COM_USER']->setPasswordFunction($REX['ADDON']['community']['plugin_auth']['passwd_algorithmus']);
    
    $REX['COM_USER']->setLogin($login_name,$login_psw);
    $REX['COM_USER']->setLoginquery('select * from rex_com_user where `'.$REX['ADDON']['community']['plugin_auth']['login_field'].'`="USR_LOGIN" and password="USR_PSW" and status>0');
  }
  elseif($session_key && !isset($_SESSION[$login_key])) //if cookie available
  {
    $REX['COM_USER']->setLogin('dummy','dummy');
    $REX['COM_USER']->setLoginquery('select * from rex_com_user where session_key="'.$session_key.'" and session_key not "" and status>0');
  }

  ## --- CHECK LOGIN ---
  $login_success = $REX['COM_USER']->checkLogin();

  if($login_success)
  {
    ## Remember User-Session?
    if($REX['ADDON']['community']['plugin_auth']['stay_active'])
    {
      if($login_stay)
      {
        ## creating new Session-Key and write to dbase
        $session_key = sha1($REX['COM_USER']->getValue('id').$REX['COM_USER']->getValue('firstname').$REX['COM_USER']->getValue('name').time().rand(0,1000));
        $sql = rex_sql::factory();
        $sql->setQuery('update rex_com_user set session_key="'.$session_key.'" where id='.$REX['COM_USER']->getValue('id'));
      }

      ## Update cookie
      setcookie($login_key, $session_key, time() + (3600*24*$REX['ADDON']['community']['plugin_auth']['cookie_ttl']), "/" );  
    }
    
    if($login_name)
    {
      // -> EP COM_AUTH_LOGIN
      // Manipulate USER Object oder execute functions on positiv login
      $REX['COM_USER'] = rex_register_extension_point('COM_AUTH_LOGIN', $REX['COM_USER'], array('id' => $REX['COM_USER']->getValue('id'), 'login' => $REX['COM_USER']->getValue($REX['ADDON']['community']['plugin_auth']['login_field'])));
      
      ## set redirect     
      if($referer)
        $redirect = urldecode($referer);
      else 
        $redirect = rex_getUrl($REX['ADDON']['community']['plugin_auth']['article_login_ok']);
    }
    
    // Success Authentification -> Do Nothing
    
  }
  else
  {
    unset($REX['COM_USER']);
    
    //TODO: Adding EP COM_AUTH_LOGINFAILED
    
  }
}

/*
 * Logout process
 */
if($logout && isset($REX['COM_USER']))
{
  // -> EP COM_USER_LOGOUT
  // Use USER Object or execute functions when user logs out.
  rex_register_extension_point('COM_AUTH_LOGOUT',$REX['COM_USER'],array('id' => $REX['COM_USER']->getValue('id'), 'login' => $REX['COM_USER']->getValue($REX['ADDON']['community']['plugin_auth']['login_field'])));
  
  ## Unset Sessions
  unset($REX['COM_USER']);
  unset($_SESSION[$login_key]);
  unset($_COOKIE[$login_key]);
  setcookie($login_key, $session_key, time() - 3600, "/");
}

/*
 * Checking page permissions
 */
if($article = OOArticle::getArticleById($REX["ARTICLE_ID"]))
  if(!rex_com_auth::checkperm($article) && !$redirect  && $REX['ADDON']['community']['plugin_auth']['article_withoutperm'] != $REX['ARTICLE_ID'])
  {
    $params = null;
    
    ## Adding referer only if target is not login_ok Article
    if($REX['ADDON']['community']['plugin_auth']['article_login_ok'] != $REX['ARTICLE_ID'])
      $params = array($REX['ADDON']['community']['plugin_auth']['request']['ref'] => urlencode($_SERVER['REQUEST_URI']));
    
    $redirect = rex_getUrl($REX['ADDON']['community']['plugin_auth']['article_withoutperm'],'',$params,'&');
  }
  
/*
 * Handle redirects if required
 */
if($redirect)
{
  ob_end_clean();
  header('Location:'.$redirect); 
  exit;
}

?>