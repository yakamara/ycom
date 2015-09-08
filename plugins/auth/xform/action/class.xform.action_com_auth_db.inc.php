<?php

class rex_xform_action_com_auth_db extends rex_xform_action_abstract
{
	
	function execute()
	{
		global $REX;
	
		if (!isset($REX["COM_USER"])) {

			echo "error - access denied - user not logged in";
			
		} else {

			switch($this->getElement(2)){

				case("logout"):
					$REX['COM_USER']->setLogout(true);
					echo $REX['COM_USER']->checkLogin();
					unset($REX["COM_USER"]);
					unset($_COOKIE['comrex_auth']);
					break;

				case("delete"):
					rex_com_auth::deleteUser($REX["COM_USER"]->getValue("id"));
					rex_com_auth::clearUserSession();
					break;

				case("update"):
				default:
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
					break;
			}

		}

	}

	function getDescription()
	{
		return "action|com_auth_db|update(default)/delete/logout";
	}

}

?>