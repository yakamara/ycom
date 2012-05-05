<?PHP

class rex_xform_validate_com_auth_login extends rex_xform_validate_abstract 
{

	function enterObject()
	{
		global $REX;
		
		$error = TRUE;
		
		$vars = array();
		
		$e = explode(",",$this->getElement(2));
		$s = array();
		foreach($e as $v)
		{
			$w = explode("=",$v);
			$label = $w[0];
			$value = trim(rex_request($w[1],"string",""));
			$vars[$label] = $value;
		}

    $query_extras = "";
    if($this->getElement(3) != "")
      $query_extras = ' and ('.$this->getElement(3).')';

    rex_com_auth::loginWithParams($vars,$query_extras);

    if(!rex_com_auth::getUser())
    {
      $this->params["warning"][] = 1;
      $this->params["warning_messages"][] = rex_translate($this->getElement(4));
      rex_com_auth::clearUserSession();
      
    }
    
    // TODO .load fields
    /*
    $fields = explode(",",$fields);
    foreach($fields as $field)
    {
      $this->params["value_pool"]["email"][$field] = $REX["COM_USER"]->getValue($field);
      if ($this->getElement(6) != "no_db") 
        $this->params["value_pool"]["sql"][$field] = $REX["COM_USER"]->getValue($field);
    }
    */
    
  	return;

	}
	
	function getDescription()
	{
		return "com_auth_login -> prüft ob leer, beispiel: validate|com_auth_login|label1=request1,label2=request2|status>0|warning_message|opt:load_field1,load_field2,load_field3|[no_db] ";
	}
}
?>