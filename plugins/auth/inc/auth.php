<?php

// -------------------------------------------------------------- USER AUTH

global $REX;
if(!isset($_SESSION))
  @session_start();

$login_key = rex_com_auth::getLoginKey();

$sk = ''; // Cookie-Session-Key
$url_params = array();

unset($REX['COM_USER']);
unset($jump_id);

$rex_com_auth_login_name = stripslashes(rex_request($REX['ADDON']['community']['plugin_auth']['request']['name'],"string"));
$rex_com_auth_login_psw = stripslashes(rex_request($REX['ADDON']['community']['plugin_auth']['request']['psw'],"string"));
$rex_com_auth_login_stay = rex_request($REX['ADDON']['community']['plugin_auth']['request']['stay'],"string");
$rex_com_auth_login_jump = rex_request($REX['ADDON']['community']['plugin_auth']['request']['jump'],"string");
$rex_com_auth_use_jump_url = FALSE;
$rex_com_auth_info = 0; // 0 - nichts / 1 - logout / 2 - failed login / 3 - logged in

// is rememberme is active, get the cookie - if available
if($REX['ADDON']['community']['plugin_auth']['stay_active'] == "1")
{
  if(isset($_COOKIE[$login_key]))
    $sk = $_COOKIE[$login_key];

}else
{
  unset($_COOKIE[$login_key]);
}

//var_dump( $_COOKIE);

// ----- the authentifikation
if (
  (isset($_SESSION[$login_key]['UID']) && $_SESSION[$login_key]['UID'] != "") 
  or (isset($_REQUEST[$REX['ADDON']['community']['plugin_auth']['request']['name']]) and isset($_REQUEST[$REX['ADDON']['community']['plugin_auth']['request']['psw']])) 
  or ($REX['ADDON']['community']['plugin_auth']['stay_active'] == "1" and $sk != '')
)
{

  $rex_com_auth_logout = rex_request("rex_com_auth_logout","int");
  $REX['COM_USER'] = new rex_login();
  $REX['COM_USER']->setSqlDb(1);
  $REX['COM_USER']->setSysID($login_key);
  $REX['COM_USER']->setSessiontime(7200);
  if ($rex_com_auth_logout == 1) { 
    $REX['COM_USER']->setLogout(true);
  }
  $REX['COM_USER']->setUserID("rex_com_user.id");
  $REX['COM_USER']->setUserquery("select * from rex_com_user where id='USR_UID' and status>0");

  // Bei normalem Login
  $REX['COM_USER']->setLogin($rex_com_auth_login_name,$rex_com_auth_login_psw);
  $REX['COM_USER']->setLoginquery('select * from rex_com_user where `'.$REX['ADDON']['community']['plugin_auth']['login_field'].'`="USR_LOGIN" and password="USR_PSW" and status>0');

  if ($REX['COM_USER']->checkLogin())
  {

    // ----- Login ok / ###you_have_logged_in###
    if ($rex_com_auth_login_name != "")
    {
      $jump_aid = $REX['ADDON']['community']['plugin_auth']['article_login_ok'];

      // if login was ok, and stay_active is allowed, and user want to be rememberd, then set sk
      if($REX['ADDON']['community']['plugin_auth']['stay_active'] == "1" && $rex_com_auth_login_stay == 1)
      {

        // create sk if it does not exist
        $sk = trim($REX['COM_USER']->getValue('session_key'));
        $uid = $REX['COM_USER']->getValue('id');
        $ufn = $REX['COM_USER']->getValue('firstname');
        $uln = $REX['COM_USER']->getValue('name');
        $sk = sha1($uid.$ufn.$uln.time().rand(0,1000));
        $uu = rex_sql::factory();
        $uu->setQuery('update rex_com_user set session_key="'.$sk.'" where id='.$uid);
        
      }
      
    }

    $rex_com_auth_info = 3; // 0 - nichts / 1 - logout / 2 - failed login / 3 - logged in

      $rex_com_auth_use_jump_url = TRUE;
  
  }else
  {
    if($REX['ADDON']['community']['plugin_auth']['stay_active'] == "1" && $sk != '')
    {
      if($rex_com_auth_logout == 1)
      {
        $sk = '';
        unset($REX['COM_USER']);
        unset($_COOKIE[$login_key]);
        $url_params = array();
        $url_params['rex_com_auth_logout'] = 1;
        $jump_aid = $REX['ADDON']['community']['plugin_auth']['article_login_failed'];
        $rex_com_auth_use_jump_url = TRUE;
        $rex_com_auth_info = 1; // 0 - nichts / 1 - logout / 2 - failed login / 3 - logged in

      }else
      {
        // Check Again
        $sk = mysql_real_escape_string($sk);
        $REX['COM_USER']->setLogin("aaa","bbb"); // must be set, so the Loginquery will be executed
        $REX['COM_USER']->setLoginquery('select * from rex_com_user where session_key="'.$sk.'" and status>0');
        if (!$REX['COM_USER']->checkLogin())
        {
          // sessionlogin failed
          unset($REX['COM_USER']);
          unset($_COOKIE[$login_key]);
          $sk = '';

          $rex_com_auth_info = 2; // 0 - nichts / 1 - logout / 2 - failed login / 3 - logged in

        }else
        {
          // relogged
          $sk = trim($REX['COM_USER']->getValue('session_key')); // create new session key
          $uid = $REX['COM_USER']->getValue('id');
          $ufn = $REX['COM_USER']->getValue('firstname');
          $uln = $REX['COM_USER']->getValue('name');
          $sk = sha1($uid.$ufn.$uln.time().rand(0,1000));
          $uu = rex_sql::factory();
          $uu->setQuery('update rex_com_user set session_key="'.$sk.'" where id='.$uid);
          $rex_com_auth_use_jump_url = TRUE;
          $rex_com_auth_info = 3; // 0 - nichts / 1 - logout / 2 - failed login / 3 - logged in
          
        }
      }
    }else
    {
      // ----- Login failed
      $jump_aid = $REX['ADDON']['community']['plugin_auth']['article_login_failed'];
      $rex_com_auth_info = 2; // 0 - nichts / 1 - logout / 2 - failed login / 3 - logged in
      if ($rex_com_auth_logout == 1) {
        $jump_aid = $REX['ADDON']['community']['plugin_auth']['article_logout'];
        $rex_com_auth_info = 1; // 0 - nichts / 1 - logout / 2 - failed login / 3 - logged in
      }
        
      unset($REX['COM_USER']);
      unset($_COOKIE[$login_key]);
      $sk = '';
      
      $url_params[$REX['ADDON']['community']['plugin_auth']['request']['name']] = $rex_com_auth_login_name;
      if($REX['ADDON']['community']['plugin_auth']['stay_active'] == "1")
      {
        $url_params[$REX['ADDON']['community']['plugin_auth']['request']['stay']] = $rex_com_auth_login_stay;
      } 

    }
  }

}else
{
  // ----- nicht eingeloggt und kein login
  $REX["COM_LOGIN_MSG"] = '';
  unset($REX['COM_USER']);
  unset($_COOKIE[$login_key]);
  $sk = '';
}

// save cookie if stay active and sk exists and login worked aut
if($REX['ADDON']['community']['plugin_auth']['stay_active'] == "1")
{
  if($sk == "") {
    setcookie($login_key, "", time() -1 , "/" );  /* verfŠllt in 14 Tagen */
  } else {
    setcookie($login_key, $sk, time() + (3600*24*14), "/" );  /* verfŠllt in 14 Tagen */
  }
  
  $_COOKIE[$login_key] = $sk;

}

if (
      (isset($jump_aid) && $article = OOArticle::getArticleById($jump_aid))
      ||
      ($rex_com_auth_use_jump_url && $rex_com_auth_login_jump != "")
   )
{
  ob_end_clean();
  
  $url_params['rex_com_auth_info'] = $rex_com_auth_info;
  
  if($rex_com_auth_use_jump_url && $rex_com_auth_login_jump != "")
  {
    header('Location: http://'.$REX["SERVER"].'/'.rex_com_auth_urldecode($rex_com_auth_login_jump));
  }else
  {
    if($rex_com_auth_login_jump != "") {
      $url_params[$REX['ADDON']['community']['plugin_auth']['request']['jump']] = $rex_com_auth_login_jump;
	}
    header('Location:'.rex_getUrl($jump_aid,'',$url_params,'&'));
  }
  exit;
}



// ---------- page_permissions
if($article = OOArticle::getArticleById($REX["ARTICLE_ID"]))
{
  if(!rex_com_auth::checkperm($article))
  {
    ob_end_clean();
    header('Location:'.rex_getUrl($REX['ADDON']['community']['plugin_auth']['article_withoutperm'],'',$url_params,'&'));
    exit;
  }
}else
{
  // Wenn Article nicht vorhanden - nichts machen -> wird dann von der index.php geregelt sodass eine fehlerseite auftaucht
  // $jump_aid = $REX['ADDON']['community']['plugin_auth']['article_withoutperm'];
}

?>