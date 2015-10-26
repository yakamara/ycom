<?php

class rex_com_auth
{
 
    /*
     * return Article right rekursive
     * 0:translate:com_perm_extends|1:translate:com_perm_only_logged_in|2:translate:com_perm_only_not_logged_in|3:translate:com_perm_all
     */
	static function checkPerm(&$obj)
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
		$delete = rex_register_extension_point("COM_AUTH_USER_DELETE", $delete, array('id' => $id));
		if(!$delete) { return FALSE; }
		
		$id = (int) $id;
		$gu = rex_sql::factory();
		$gu->setQuery('delete from rex_com_user where id='.$id);

		rex_register_extension_point("COM_AUTH_USER_DELETED", "", array('id' => $id));

		return TRUE;
	}

	/*
	 * Clears User Session
	 */
	static function clearUserSession()
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
	static function getLoginKey()
	{
		return 'comrex';
	}


  static function getUser()
  {
    global $REX;
    if(isset($REX['COM_USER']))
      return $REX['COM_USER'];
    else 
      return false;
  }


  /*
   * login in
   */
  
  static function login($login_name = "", $login_psw = "", $login_stay = "", $logout = false, $query_extras = ' and status>0', $login_psw_hashed = false)
  {
    
    global $REX;
    
    $login_status = 0; // not logged in
    
    $login_key = rex_com_auth::getLoginKey();
    $session_key = '';
    $user_session = '';
    
    if(!isset($_SESSION))
      session_start();
    
    ## Sessions
    if(isset($_SESSION[$login_key]))
      $user_session = $_SESSION[$login_key];

    if($REX['ADDON']['community']['plugin_auth']['stay_active'])
      if(isset($_COOKIE[$login_key]))
        $session_key = rex_cookie($login_key,'string');
    
    /*
    * Authentification
    */
    ## if newlogin or Sessions available setting up User-Object
    if (($login_name && $login_psw) || !empty($user_session['UID']) || $session_key) {
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
      $REX['COM_USER']->setUserquery("select * from rex_com_user where id='USR_UID' ".$query_extras);

      ## --- NEW LOGIN ---
      if($login_name) {
        
        ## TODO:
        // if user existing in community dbase
              // get authtype und use auth function
                  // on success -> do login
        // if not
              // looping all registered authtypes
                  // if no user -> login fail
                  // else -> use auth function
                      // on success -> sync to community dbase and do login
        
        ## Hash password if required and not already hashed (javascript
        $hash_func = $REX['ADDON']['community']['plugin_auth']['passwd_algorithmus'];
        if($REX['ADDON']['community']['plugin_auth']['passwd_hashed'] && !$login_psw_hashed)
          $REX['COM_USER']->setPasswordFunction($hash_func);
        
        $REX['COM_USER']->setLogin($login_name,$login_psw);
        $REX['COM_USER']->setLoginquery('select * from rex_com_user where `'.$REX['ADDON']['community']['plugin_auth']['login_field'].'`="USR_LOGIN" and password="USR_PSW" '.$query_extras);

      } else if($session_key && !isset($_SESSION[$login_key])) { //if cookie available
        $REX['COM_USER']->setLogin('dummy','dummy');
        $REX['COM_USER']->setLoginquery('select * from rex_com_user where session_key="'.$session_key.'" and (session_key != "" and session_key is not NULL) '.$query_extras);
      }
    
      ## --- CHECK LOGIN ---
      $login_success = $REX['COM_USER']->checkLogin();
    
      if ($login_success) {
        $login_status = 1; // is logged in
        
        ## Remember User-Session?
        if ($REX['ADDON']['community']['plugin_auth']['stay_active']) {
          if ($login_stay) {
            ## creating new Session-Key and write to dbase
            $session_key = sha1($REX['COM_USER']->getValue('id').$REX['COM_USER']->getValue('firstname').$REX['COM_USER']->getValue('name').time().rand(0,1000));
            $sql = rex_sql::factory();
            $sql->setQuery('update rex_com_user set session_key="'.$session_key.'" where id='.$REX['COM_USER']->getValue('id'));
          }
    
          ## Update cookie
          setcookie($login_key, $session_key, time() + (3600*24*$REX['ADDON']['community']['plugin_auth']['cookie_ttl']), "/" );  
        }
        
        if ($login_name) {
          $login_status = 2; // has just logged in
        
          $REX['COM_USER'] = rex_register_extension_point('COM_AUTH_LOGIN_SUCESS', $REX['COM_USER'], array('id' => $REX['COM_USER']->getValue('id'), 'login' => $REX['COM_USER']->getValue($REX['ADDON']['community']['plugin_auth']['login_field'])));

          // track last_login_date
          // $u = rex_sql::factory();
          // $u->setQuery('update rex_com_user set last_login_date="'.date("U").'" where id="'.$REX['COM_USER']->getValue('id').'"');
          
        }
        
        $REX['COM_USER'] = rex_register_extension_point('COM_AUTH_LOGIN', $REX['COM_USER'], array('id' => $REX['COM_USER']->getValue('id'), 'login' => $REX['COM_USER']->getValue($REX['ADDON']['community']['plugin_auth']['login_field'])));
        
        // track last_action_time
        // $u = rex_sql::factory();
        // $u->setQuery('update rex_com_user set last_action_time="'.date("U").'" where id="'.$REX['COM_USER']->getValue('id').'"');
        
        // Success Authentification -> Do Nothing
        
      } else {
        $login_status = 0; // not logged in
        unset($REX['COM_USER']);
        
        if($login_name) {
          $login_status = 4; // login failed
        }
        
        $login_status = rex_register_extension_point('COM_AUTH_LOGIN_FAILED', $login_status, array(
            'login_name' => $login_name, 'login_psw' => $login_psw, 'login_stay' => $login_stay, 'logout' => $logout, 'query_extras' => $query_extras));
        
      }
    }
    
    /*
     * Logout process
     */
    if($logout && isset($REX['COM_USER'])) {
      $login_status = 4;
      
      // -> EP COM_USER_LOGOUT
      // Use USER Object or execute functions when user logs out.
      rex_register_extension_point('COM_AUTH_LOGOUT',$REX['COM_USER'],array('id' => $REX['COM_USER']->getValue('id'), 'login' => $REX['COM_USER']->getValue($REX['ADDON']['community']['plugin_auth']['login_field'])));
      
      ## Unset Sessions
      rex_com_auth::clearUserSession();
    }
  
    rex_register_extension_point('COM_AUTH_LOGIN_PROCESS_END','','');

    return $login_status;
  
  }

  /*
    $params = array("login" => "jan", "activation_key" => "jkjkj");
    
    return $userobject || false
  */

  static function loginWithParams($params, $query_extras = "")
  {
  
    global $REX;
    
    $s = array();
    foreach($params as $l => $v) {
      $s[] = ' `'.mysql_real_escape_string($l).'` = "'.mysql_real_escape_string($v).'" ';
    }
    
    $u = rex_sql::factory();
    // $u->debugsql = 1;
    $u_array = $u->getArray('select * from rex_com_user where '.implode(" AND ",$s).' '.$query_extras.' LIMIT 2');
        
    if(count($u_array) != 1)
      return false;
    
    $user = $u_array[0];
    
    $login_name = $user[$REX['ADDON']['community']['plugin_auth']['login_field']];
    $login_psw = $user["password"];
    
    rex_com_auth::login($login_name, $login_psw, "", false, "", true);

    return rex_com_auth::getUser();
  
  }

}
