<?php

class rex_yform_value_ycom_auth_form_stayactive extends rex_yform_value_abstract
{

	function enterObject()
	{
	

    $value = rex_request(rex_config::get('ycom', 'auth_request_stay'),"string");
	  $this->setValue($value);

    $v = 1; // gecheckt
    $w = 0; // nicht gecheckt

    // first time and default is true -> checked
    if ($this->params['send'] != 1 && $this->getElement('3') == 1 && $this->getValue() === '') {
        $this->setValue($v);

    // if check value is given -> checked
    } elseif ($this->getValue() == $v) {
        $this->setValue($v);

    // not checked
    } else {
        $this->setValue($w);
    }

    $this->params['form_output'][$this->getId()] = $this->parse('value.checkbox.tpl.php', array('value' => $v));

    $this->params['form_output'][$this->getId()] = str_replace($this->getFieldName(), rex_config::get('ycom', 'auth_request_stay'), $this->params['form_output'][$this->getId()]);
	
	}

	function getDescription()
	{
		return "ycom_auth_form_stayactive -> Beispiel: ycom_auth_form_stayactive|auth|eingeloggt bleiben:|0/1 angeklickt";
	}

}

?>
