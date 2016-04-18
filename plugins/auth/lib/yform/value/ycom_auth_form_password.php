<?php

class rex_yform_value_ycom_auth_form_password extends rex_yform_value_abstract
{

	function enterObject()
	{

    $this->params['form_output'][$this->getId()] = $this->parse(array('value.password.tpl.php', 'value.text.tpl.php'), array('type' => 'password', 'value' => ''));
        
    $this->params['form_output'][$this->getId()] = str_replace($this->getFieldName(),rex_config::get('ycom', 'auth_request_psw'), $this->params['form_output'][$this->getId()]);
        
	}

	function getDescription()
	{
		return "ycom_auth_form_password -> Beispiel: ycom_auth_form_password|label|Passwort:|Placeholder";
	}

}

?>
