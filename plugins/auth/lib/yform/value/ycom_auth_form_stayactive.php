<?php

class rex_yform_value_ycom_auth_form_stayactive extends rex_yform_value_abstract
{

	function enterObject()
	{
		if(rex_config::get('ycom', 'auth_request_stay') != 1) {
            // return;
        }

		$l_label = $this->getElement(2);

		$checked = '';
		if ($this->getElement(3) == 1) $checked = ' checked="checked"';

		$sa = rex_request(rex_config::get('ycom', 'auth_request_stay'), "int");
		if($sa == 1) $checked = ' checked="checked"';
		
		$this->params["form_output"][$this->getId()] = '
		<p class="formcheckbox form-com-auth-stayactive '.$this->getHTMLClass().'" id="'.$this->getHTMLId().'">
			<input type="checkbox" class="checkbox " name="'.rex_config::get('ycom', 'auth_request_stay').'" id="'.$this->getFieldId().'" value="1" '.$checked.' />
			<label class="checkbox " for="'.$this->getFieldId().'" >'.$l_label.'</label>
		</p>
		';

	}

	function getDescription()
	{
		return "ycom_auth_form_stayactive -> Beispiel: ycom_auth_form_stayactive|auth|eingeloggt bleiben:|0/1 angeklickt";
	}

}

?>
