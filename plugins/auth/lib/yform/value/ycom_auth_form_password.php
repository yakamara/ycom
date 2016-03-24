<?php

class rex_yform_value_ycom_auth_form_password extends rex_yform_value_abstract
{

	function enterObject()
	{
		$l_label = $this->getElement(2);
		$placeholder = $this->getElement(3);	

		$this->params["form_output"][$this->getId()] = '
		<p class="formpassword form-ycom-auth-password formlabel-'.$this->getName().'" id="'.$this->getHTMLId().'">
			<label class="password " for="'.$this->getFieldId().'" >'.$l_label.'</label>
			<input type="password" class="password " name="'.rex_config::get('ycom', 'auth_request_psw').'" id="'.$this->getFieldId().'" value="" placeholder="'.$placeholder.'" />
		</p>
		';

		return;

	}

	function getDescription()
	{
		return "ycom_auth_form_password -> Beispiel: ycom_auth_form_password|label|Passwort:|Placeholder";
	}

}

?>
