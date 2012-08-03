<?php

class rex_xform_com_auth_load_user extends rex_xform_abstract
{

  function enterObject()
  {
    global $REX;

  	if(rex_com_auth::getUser() && !$this->params["send"])
  	{
  		$fields = $this->getElement(2);
  		if($fields != "") {
  			$fields = explode(",",$this->getElement(2));
  		}else
  		{
  			$fields = array();
  		}
  
  		foreach($this->getValueObjects() as $o) 
  		{
  			if((count($fields) == 0 || in_array($o->getName(),$fields)) && $o->getName() != $this->getName())
  			{
  				$o->setValue(@rex_com_auth::getUser()->getValue($o->getName()));
  			}
  		}
  	}

    return;

  }

  function getDescription()
  {
    return "com_auth_load_user -> Beispiel: com_auth_load_user|label|opt:field1,field2,field3";
  }

}

?>