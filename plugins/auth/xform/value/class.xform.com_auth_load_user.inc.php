<?php

class rex_xform_com_auth_load_user extends rex_xform_abstract
{

  function enterObject()
  {
    global $REX;

	if(isset($REX["COM_USER"]) && !$this->params["send"])
	{
		$fields = $this->getElement(2);
		if($fields != "") {
			$fields = explode(",",$this->getElement(2));
		}else
		{
			$fields = array();
		}

		foreach($this->obj as $o) 
		{
			if((count($fields) == 0 || in_array($o->getName(),$fields)) && $o->getName() != $this->getName())
			{
				$o->setValue(@$REX["COM_USER"]->getValue($o->getName()));
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