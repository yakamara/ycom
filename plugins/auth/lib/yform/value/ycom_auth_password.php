<?php

class rex_yform_value_ycom_auth_password extends rex_yform_value_abstract
{

    function enterObject()
	{

		if ($this->params['send']) {

			$placeholder = rex_i18n::translate('translate:ycom_auth_password_not_updated');
			if ($this->getValue() != '') {
				$placeholder = rex_i18n::translate('translate:ycom_auth_password_updated');

			}

		} else {

			$placeholder = rex_i18n::translate('translate:ycom_auth_password_exists');
			if ($this->getValue() == '') {
				$placeholder = rex_i18n::translate('translate:ycom_auth_password_isempty');

			}

		}

		if ($this->getElement('placeholder') == "") {
			$this->setElement('placeholder', $placeholder);

		}

		$this->params['form_output'][$this->getId()] = $this->parse(array('value.password.tpl.php', 'value.text.tpl.php'), array('type' => 'password', 'value' => ''));

    }

	function preAction()
	{
		if ($this->getValue() != '') {

			$password = $this->getValue();
			$hashed_value = rex_login::passwordHash($password);
			self::enterObject();
			$this->params['value_pool']['sql'][$this->getName()] = $hashed_value;
			$this->params['value_pool']['email'][$this->getName()] = $password;

		}

	}

	function getDescription()
	{
		return "ycom_auth_password -> Beispiel: ycom_auth_password|name|label";
	}

	function getDefinitions()
	{
		return array(
						'type' => 'value',
						'name' => 'ycom_auth_password',
						'values' => array(
									'name'      => array( 'type' => 'name',    'label' => 'Feld' ),
									'label' => array( 'type' => 'text',    'label' => 'Bezeichnung'),
								),

						'description' => 'Erzeugt den Hash-Wert des Passwortes',
						'dbtype' => 'varchar(255)',
						'famous' => FALSE
        );
	}
}
