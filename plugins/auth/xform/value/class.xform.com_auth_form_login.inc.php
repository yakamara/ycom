<?php

class rex_xform_com_auth_form_login extends rex_xform_abstract
{

	function enterObject()
	{
		global $REX;

		$l_label = $this->getElement(2);
		$login = rex_request($REX['ADDON']['community']['plugin_auth']['request']['name'],"string");

		$this->params["form_output"][$this->getId()] = '
		<p class="formtext form-com-auth-login '.$this->getHTMLClass().'" id="'.$this->getHTMLId().'">
			<label class="text" for="'.$this->getFieldId().'" >'.$l_label.'</label>
			<input type="text" class="text" name="'.$REX['ADDON']['community']['plugin_auth']['request']['name'].'" id="'.$this->getFieldId().'" value="'.htmlspecialchars(stripslashes($login)).'" />
		</p>
		';

		return;

	}

	function getDescription()
	{
		return "com_auth_form_login -> Beispiel: com_auth_form_login|label|Benutzername:";
	}

}

?>