<?php
class rex_com_auth_facebook
{
	public function getLoginUrl()
	{
		global $REX;
		return $REX['ADDON']['community']['plugin_auth_facebook']['facebook']->getLoginUrl(array("scope" => $REX['ADDON']['community']['plugin_auth_facebook']['appAccess']));
	}
	
	public function getCurrentUri() {
		if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
			$protocol = 'https://';
		else
			$protocol = 'http://';
	
		return $protocol.$_SERVER['HTTP_HOST'];
	}
	
	public function generatePassword($lenght = 10)
	{
		## Soruce: http://www.tsql.de/php/zufaelliges-passwort-erzeugen-md5
		## Not realy safe, but very smart ;)
		$string = md5((string)mt_rand().$_SERVER['REMOTE_ADDR'].time());
		$start = rand(0,strlen($string)-$lenght);
		return substr($string, $start, $lenght);
	}
}