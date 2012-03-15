<?php

class rex_com_auth
{
 
    /*
     * return Article right rekursive
     */
    function checkPerm(&$obj)
    {
      if($obj->getValue('art_com_permtype'))
        return self::checkArticlePerm($obj);
      
      if(!$obj->isStartArticle())
        $obj = $obj->getCategory();
   
      $tree = $obj->getParentTree();
      foreach($tree as $obj)
        if(!self::checkArticlePerm($obj))
          return false;
        
      return true;
    } 
    
    /*
     * return Article rights non rekursive
     */
	function checkArticlePerm(&$obj)
	{
		global $REX;
	
		// Authentifizierung ist ausgeschaltet
		if($REX['ADDON']['community']['plugin_auth']['auth_active'] != "1")
			return TRUE;
	
		// echo "<br />*".$obj->getValue('art_com_permtype');
		// echo " -- ".$obj->getValue('art_com_groups');
		// if(isset($REX["COM_USER"])) echo " ## ".$REX["COM_USER"]->getValue("group");
		
		// ---- Wenn für alle freigegeben
		if($obj->getValue('art_com_permtype') == 0 || $obj->getValue('art_com_permtype') == "")
			return TRUE;
	
		// ---- nur für nicht eingeloggte freigegeben
		if($obj->getValue('art_com_permtype') == 2)
			if(!isset($REX["COM_USER"]) || !is_object($REX["COM_USER"]))
				return TRUE;
			else
				return FALSE;
	
		if($obj->getValue('art_com_permtype') == 1 && (!isset($REX["COM_USER"]) || !is_object($REX["COM_USER"])))
		{
			return FALSE;
		}
	
		// ---------- ab hier nur für eingeloggte -> permtype = 1

	
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
	    if(!rex_com_auth::checkperm($article))
	      unset($params['subject'][$id]);
	  }
	
	  return $params['subject'];
	}

}