<?php

class rex_xform_com_auth_form_info extends rex_xform_abstract
{

  function enterObject()
  {
    global $REX;

    $messages = array();
    $messages[0] = $this->getElement(2);
    $messages[1] = $this->getElement(3);
    $messages[2] = $this->getElement(4);
    $messages[3] = $this->getElement(5);
    
	$info = rex_request('rex_com_auth_info',"string");
	if(!isset($messages[$info])) {
		$info = 0;
	}
    $message = $messages[$info];
    $class = "form_info";
    if($info == 2) $class = "form_warning";
    
    if($message != "") {
      $this->params["form_output"][$this->getId()] = '<ul class="formcom_auth_form_info '.$class.' formlabel-'.$this->getName().'" id="'.$this->getHTMLId().'"><li>'.$message.'</li></ul>';
    }

    return;

  }

  function getDescription()
  {
    return "com_auth_form_info [0 - nichts / 1 - logout / 2 - failed login / 3 - logged in] -> Beispiel: com_auth_form_info|label|msg_login|msg_logged_out|msg_failed|msg_logged_in|";
  }

}

?>