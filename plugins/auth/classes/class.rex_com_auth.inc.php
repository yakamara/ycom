<?php

class rex_com_auth
{
 
    /*
     * return Article right rekursive
     * 0:translate:com_perm_extends|1:translate:com_perm_only_logged_in|2:translate:com_perm_only_not_logged_in|3:translate:com_perm_all
     */
	function checkPerm(&$obj)
	{
		global $REX;
    	
		// auth inactive
		if($REX['ADDON']['community']['plugin_auth']['auth_active'] != "1")
			return TRUE;
		
		$permtype = (int) $obj->getValue('art_com_permtype');
		
		if($permtype == 3)
			return TRUE;

		if($permtype != 1 && $permtype != 2)
		{
			// perm extends
			if ($o = $obj->getParent())
				return self::checkPerm($o);
			
			// no parent, no perm set -> for all accessable
			return true;
		}

		// ---- nur für nicht eingeloggte freigegeben
		if($permtype == 2)
			if(!isset($REX["COM_USER"]) || !is_object($REX["COM_USER"]))
				return TRUE;
			else
				return FALSE;
	
		if($permtype == 1 && (!isset($REX["COM_USER"]) || !is_object($REX["COM_USER"])))
		{
			return FALSE;
		}

		// permtype = 1 / group check

		// ----- wenn für alle gruppen freigegeben
		if($obj->getValue('art_com_grouptype') == 0 || $obj->getValue('art_com_grouptype') == "")
			return TRUE;
	
		// ----- muss in jeder gruppe sein
		if($obj->getValue('art_com_grouptype') == 1)
		{
			$art_groups = explode("|",$obj->getValue('art_com_groups'));
			$user_groups = explode(",",$REX["COM_USER"]->getValue("rex_com_group"));
			foreach($art_groups as $ag)
			{
				if($ag != "" && !in_array($ag,$user_groups))
				{
					return FALSE;
				}
			}
			return TRUE;
		}
		
		// ----- muss nur in einer gruppe sein
		if($obj->getValue('art_com_grouptype') == 2)
		{
			$art_groups = explode("|",$obj->getValue('art_com_groups'));
			$user_groups = explode(",",$REX["COM_USER"]->getValue("rex_com_group"));
			foreach($art_groups as $ag)
			{
				if($ag != "" && in_array($ag,$user_groups))
				{
					return TRUE;
				}
			}
		}
		
		// ----- ist in keiner gruppe
		if($obj->getValue('art_com_grouptype') == 3)
		{
			$user_groups = explode(",",$REX["COM_USER"]->getValue("rex_com_group"));
			if(count($user_groups) == 0)
				return TRUE;
		}
		
		return FALSE;
	  
    } 
    

	/*
	 * Removing article from com_user Database
	 */
	function deleteUser($id)
	{
	    $delete = TRUE;
		$delete = rex_register_extension_point("COM_AUTH_USER_DELETE", $delete, $id);
		if(!$delete) { return FALSE; }
		
		$id = (int) $id;
		$gu = rex_sql::factory();
		$gu->setQuery('delete from rex_com_user where id='.$id);

		rex_register_extension_point("COM_AUTH_USER_DELETED", "", $id);

		return TRUE;
	}

	/*
	 * Clears User Session
	 */
	function clearUserSession()
	{
		global $REX;
		
		unset($REX["COM_USER"]);
		unset($_SESSION[self::getLoginKey()]);
		unset($_COOKIE[self::getLoginKey()]);
		setcookie(self::getLoginKey(), '0', time() - 3600, "/");
	}

	/*
	 * reutrns Login-Key used for Sessions and Cookies
	 */
	function getLoginKey()
	{
		return 'comrex';
	}

	/*
	 * Removes locked articles from rexSeo XML-Sitemap
	 */
	function rexseo_removeSitemapArticles($params)
	{
	  foreach($params['subject'] as $id => $item)
	  {
	    $article = OOArticle::getArticleById($id);
	    if(!rex_com_auth::checkPerm($article))
	      unset($params['subject'][$id]);
	  }
	
	  return $params['subject'];
	}

}