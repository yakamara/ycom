<?php

class rex_xform_action_com_auth_db extends rex_xform_action_abstract
{
	
	function execute()
	{
		global $REX;
	
		if(!isset($REX["COM_USER"])) {

			echo "error - access denied - user not logged in";
			
		}else {

			if($this->getElement(2) == "logout") {
				$REX['COM_USER']->setLogout(true);
				echo $REX['COM_USER']->checkLogin();
				unset($REX["COM_USER"]);
				unset($_COOKIE['comrex_auth']);

			}elseif($this->getElement(2) == "delete") {

				rex_com_auth::deleteUser($REX["COM_USER"]->getValue("id"));
				rex_com_auth::clearUserSession();
			
			}else {

				$sql = rex_sql::factory();
				if ($this->params["debug"]) {
					$sql->debugsql = TRUE;
		    	}
		    
		    	$sql->setTable("rex_com_user");
				foreach($this->params["value_pool"]["sql"] as $key => $value) {
					$sql->setValue($key, $value);
				}
	
				$sql->setWhere('id='.$REX["COM_USER"]->getValue("id").'');
				$sql->update();

			}

		}

	}

	function getDescription()
	{
		return "action|com_auth_db|update(default)/delete/logout";
	}

}

?>

<?php
class rex_xform_action_com_login_after_reg extends rex_xform_action_abstract
{
	function execute()
	{
		global $REX;

		#sddebug( 'rex_xform_action_com_login_after_reg' );
		
		$auth_name	= $REX['ADDON']['community']['plugin_auth']['request']['name'];
		$auth_psw	= $REX['ADDON']['community']['plugin_auth']['request']['psw'];
		
		if ( isset($this->params["value_pool"]["sql"]["login"]) && isset($this->params["value_pool"]["sql"]["password"]) )
		{
			$login 		= $this->params["value_pool"]["sql"]["login"];
			$password 	= $this->params["value_pool"]["sql"]["password"];
	
			header( 'Location:'.rex_getUrl( $this->getElement(1) ,$REX['CUR_CLANG'], array($auth_name=>$login,$auth_psw=>$password), '&' ) );
		}

	}

	function getDescription()
	{
		return "<strong>action|com_login_after_reg|article_id</strong><br><i>Benutzer wird nach der Registrierung zum Artikel mit article_id weitergeleitet und automatisch angemeldet.</i><br>Beispiel:action|com_login_after_reg|55";
	}
}
?>