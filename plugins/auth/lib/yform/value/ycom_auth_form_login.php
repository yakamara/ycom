<?php

class rex_yform_value_ycom_auth_form_login extends rex_yform_value_abstract
{
  function init()
  {
    ## adding referer to action for redirects
    $params = array(rex_config::get('ycom', 'auth_request_ref') => rex_request(rex_config::get('ycom', 'auth_request_ref'),'string'));
    $this->params['form_action'] = rex_getUrl('', '', $params);
    
    ## Show form after sending
    $this->params['form_showformafterupdate'] = 1;
  }

	function enterObject()
	{

		$l_label = $this->getElement(2);
		$placeholder = $this->getElement(3);		
		$login = rex_request(rex_config::get('ycom', 'auth_request_name'),"string");

		$this->params["form_output"][$this->getId()] = '
		<p class="formtext form-ycom-auth-login '.$this->getHTMLClass().'" id="'.$this->getHTMLId().'">
			<label class="text" for="'.$this->getFieldId().'"  >'.$l_label.'</label>
			<input type="text" class="text" name="'.rex_config::get('ycom', 'auth_request_name').'" id="'.$this->getFieldId().'" value="'.htmlspecialchars(stripslashes($login)).'" placeholder="'.$placeholder.'" />
		</p>
		';

		return;

	}

	function getDescription()
	{
		return "ycom_auth_form_login -> Beispiel: ycom_auth_form_login|label|Benutzername:|placeholder";
	}

}

?>
