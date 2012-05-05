<?php

global $REX, $I18N;

## Request variables
$login_name = stripslashes(rex_request($REX['ADDON']['community']['plugin_auth']['request']['name'],"string"));
$login_psw = stripslashes(rex_request($REX['ADDON']['community']['plugin_auth']['request']['psw'],"string"));
$login_stay = rex_request($REX['ADDON']['community']['plugin_auth']['request']['stay'],"string");
$referer = rex_request($REX['ADDON']['community']['plugin_auth']['request']['ref'],"string");
$logout = rex_request($REX['ADDON']['community']['plugin_auth']['request']['logout'],"int");
$redirect = '';

/*
  login_status
  0: not logged in
  1: logged in
  2: has logged in
  3: has logged out
  4: login failed
*/
$login_status = rex_com_auth::login($login_name, $login_psw, $login_stay, $logout);

## set redirect
if($login_status == 2)
{
  if($referer)
    $redirect = urldecode($referer);
  else 
    $redirect = rex_getUrl($REX['ADDON']['community']['plugin_auth']['article_login_ok']);
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