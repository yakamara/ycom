<?php

class rex_com_auth_facebook
{
	//
	// Returns the Facebook login URL
	//
	static function getLoginUrl($params = array())
	{
		global $REX;

		if(!isset($params["scope"])) {
  			$params["scope"] = $REX['ADDON']['community']['plugin_auth_facebook']['appAccess'];
  		}

  		if(!isset($params["redirect_uri"])) {
  			$params["redirect_uri"] = $REX["SERVER"].ltrim(rex_getUrl($REX['ADDON']['community']['plugin_auth']['article_login_ok']), '/') . "?";
		}

		return $REX['ADDON']['community']['plugin_auth_facebook']['facebook']->getLoginUrl($params);
	}
	
	//
	// Returns the Current URI like http://www.example.com (including http or https)
	//	
	static function getCurrentUri() {
		if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
			$protocol = 'https://';
		else
			$protocol = 'http://';
	
		return $protocol.$_SERVER['HTTP_HOST'];
	}

	static function in_array_r($item, $array){
    	return preg_match('/"'.$item.'"/i' , json_encode($array));
	}
	
	//
	// Check if all required Facebook perms are granted
	//
	static function checkRequiredPerms()
	{
		global $REX;
		$result = true;
		$perms = $REX['ADDON']['community']['plugin_auth_facebook']['facebook']->api("/".$REX['ADDON']['community']['plugin_auth_facebook']['facebook']->getUser()."/permissions");
		
		foreach(explode(',', $REX['ADDON']['community']['plugin_auth_facebook']['appAccess']) as $perm) {

			if(!self::in_array_r($perm,$perms)) {
				return false;
			}
		}
		
		return true;
	}
	
	//
	// Generates a simple random password
	//
	static function generatePassword($lenght = 10)
	{
		## Soruce: http://www.tsql.de/php/zufaelliges-passwort-erzeugen-md5
		## Not realy safe, but very smart ;)
		$string = md5((string)mt_rand().$_SERVER['REMOTE_ADDR'].time());
		$start = rand(0,strlen($string)-$lenght);
		return substr($string, $start, $lenght);
	}
	
}