<?PHP

class rex_xform_validate_com_auth_login extends rex_xform_validate_abstract 
{

	function enterObject()
	{
		global $REX;

		$this->params["submit_btn_show"] = FALSE;
		$e = explode(",",$this->getElement(2));
		$s = array();
		foreach($e as $v)
		{
			$w = explode("=",$v);
			$label = $w[0];
			$value = trim(rex_request($w[1],"string",""));

			if($value == "")
			{
				$this->params["warning"][] = 1;
				$this->params["warning_messages"][] = $this->getElement(4);
				return FALSE;
			}
			$s[] = '`'.$label.'`="'.mysql_real_escape_string($value).'"';
		}

		if($this->getElement(3) != "")
		{
			$e = explode(",",$this->getElement(3));
			foreach($e as $v) {
				$s[] = $v;
			}
		}		

		$loginquery = 'select * from rex_com_user where '.implode(" AND ",$s).'';

		if($this->params["debug"]) 
			echo $loginquery;

		$pagekey = 'comrex';
		$REX['COM_USER'] = new rex_login();
		$REX['COM_USER']->setSqlDb(1);
		$REX['COM_USER']->setSysID($pagekey);
		$REX['COM_USER']->setSessiontime(3000);
		$REX['COM_USER']->setUserID("rex_com_user.id");
		$REX['COM_USER']->setUserquery("select * from rex_com_user where id='USR_UID'");

		// Bei normalem Login
		$REX['COM_USER']->setLogin("11","22"); // quatsch setzen, login gefaked
		$REX['COM_USER']->setLoginquery($loginquery);

		if ($REX['COM_USER']->checkLogin())
		{
			// Eingeloggt
			$fields = $this->getElement(5);
			if($fields != "")
			{
				$fields = explode(",",$fields);
				foreach($fields as $field)
				{
					$this->params["value_pool"]["email"][$field] = $REX["COM_USER"]->getValue($field);
			        if ($this->getElement(6) != "no_db") {
						$this->params["value_pool"]["sql"][$field] = $REX["COM_USER"]->getValue($field);
					}
				}
			}

		}else
		{
			// Nicht eingeloggt
			$this->params["warning"][] = 1;
			$this->params["warning_messages"][] = $this->getElement(4);
			unset($REX["COM_USER"]);
		}
		// exit;
		return;

	}
	
	function getDescription()
	{
		return "com_auth_login -> prüft ob leer, beispiel: validate|com_auth_login|label1=request1,label2=request2|status>0|warning_message|opt:load_field1,load_field2,load_field3 ";
	}
}
?>