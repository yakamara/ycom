<?php

class rex_yform_value_ycom_auth_form_stayactive extends rex_yform_value_abstract
{
    public function enterObject()
    {
        $value = rex_request(rex_config::get('ycom', 'auth_request_stay'), 'string');
        $this->setValue($value);

        $v = 1;
        $w = 0;

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

        $form_output = $this->parse('value.checkbox.tpl.php', ['value' => $v]);
        $form_output = str_replace('name="'.$this->getFieldName().'"', 'name="'.rex_config::get('ycom', 'auth_request_stay').'"', $form_output);
        $this->params['form_output'][$this->getId()] = $form_output;
    }

    public function getDescription()
    {
        return 'ycom_auth_form_stayactive -> Beispiel: ycom_auth_form_stayactive|auth|eingeloggt bleiben:|0/1 angeklickt';
    }
}
