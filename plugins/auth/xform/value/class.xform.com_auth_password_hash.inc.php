<?php

class rex_xform_com_auth_password_hash extends rex_xform_abstract
{
	function postFormAction()
	{
		global $REX;		
		
		if($REX['ADDON']['community']['plugin_auth']['passwd_hashed'] == "1" && $this->params["send"])
		{
		
			// ignore already hashed values !!
			// - only password hash - if hashsize != password size
		
			$name = $this->getElement(2);
			$hash_func = $REX['ADDON']['community']['plugin_auth']['passwd_algorithmus'];
			$sql_value = $this->params["value_pool"]["sql"][$name];

			if(strlen($sql_value) != strlen(hash($hash_func,"xyz")))
			{
				$value = hash($hash_func,$sql_value);
				$this->params["value_pool"]["sql"][$name] = $value;
			}
			
			// ignore: email pool -> because of emails and clear password send
			
		}
		
	}

	function getDescription()
	{
		return "com_auth_password_hash -> Beispiel: com_auth_password_hash|name|label|";
	}

	function getDefinitions()
	{
		return array(
						'type' => 'value',
						'name' => 'com_auth_password_hash',
						'values' => array(
									array( 'type' => 'name',    'label' => 'Name'),
									array( 'type' => 'text',    'label' => 'Hashname')
								),
						'description' => 'Erzeugt Hash-Wert von einem Feld und überschreibt ihn',
						'dbtype' => 'text',
						'famous' => FALSE
						);
	}
}

?>