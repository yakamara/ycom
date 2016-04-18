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
    $login = rex_request(rex_config::get('ycom', 'auth_request_name'),"string");
		$this->setValue($login);

		$this->params['form_output'][$this->getId()] = $this->parse('value.text.tpl.php');

    $this->params['form_output'][$this->getId()] = str_replace($this->getFieldName(),rex_config::get('ycom', 'auth_request_name'), $this->params['form_output'][$this->getId()]);

	}

	function getDescription()
	{
		return "ycom_auth_form_login -> Beispiel: ycom_auth_form_login|label|Benutzername:|placeholder";
	}

}

?>
