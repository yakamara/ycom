<?php

class rex_xform_com_auth_form_stayactive extends rex_xform_abstract
{

	function enterObject()
	{
		global $REX;

		if($REX['ADDON']['community']['plugin_auth']['stay_active'] != 1)
			return;

		$l_label = $this->getElement(2);

		$checked = '';
		if ($this->getElement(3) == 1) $checked = ' checked="checked"';

		$sa = rex_request($REX['ADDON']['community']['plugin_auth']['request']['stay'],"int");
		if($sa == 1) $checked = ' checked="checked"';
		
		$this->params["form_output"][$this->getId()] = '
		<p class="formcheckbox form-com-auth-stayactive '.$this->getHTMLClass().'" id="'.$this->getHTMLId().'">
			<input type="checkbox" class="checkbox " name="'.$REX['ADDON']['community']['plugin_auth']['request']['stay'].'" id="'.$this->getFieldId().'" value="1" '.$checked.' />
			<label class="checkbox " for="'.$this->getFieldId().'" >'.$l_label.'</label>
		</p>
		';

	}

	function getDescription()
	{
		return "com_auth_form_stayactive -> Beispiel: com_auth_form_stayactive|auth|eingeloggt bleiben:|0/1 angeklickt";
	}

}

?>