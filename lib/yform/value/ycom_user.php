<?php

// ************************************* XFORM USER

class rex_yform_value_ycom_user extends rex_yform_value_abstract
{

  function enterObject()
  {

    $show_label = "email";
    $show_value = "";
    $show_label = $this->getElement(6);

    $this->setValue(-1);
    if (rex_ycom_auth::getUser()) {
      $this->setValue(rex_ycom_auth::getUser()->getValue($this->getElement(2)));
      $show_value = rex_ycom_auth::getUser()->getValue($show_label);
    }

    $wc = "";
    if (isset($this->params["warning"][$this->getId()])) {
      $wc = $this->params["warning"][$this->getId()];
    }

    if (trim($this->getElement(4)) != "hidden")
    {
      $this->params["form_output"][$this->getId()] = '
        <p class="formtext">
          <label class="text ' . $wc . '" for="'.$this->getFieldId().'" >' . $this->getElement(3) . '</label>
          <input type="text" class="text inp_disabled" disabled="disabled"  id="'.$this->getFieldId().'" value="'.htmlspecialchars($show_value) . '" />
        </p>';
    }

    $this->params["value_pool"]["email"][$this->getElement(1)] = stripslashes($this->getValue());
    if ($this->getElement(5) != "no_db") { $this->params["value_pool"]["sql"][$this->getElement(1)] = $this->getValue(); }

  }

  function getDescription()
  {
    return 'ycom_user -> Beispiel: com_user|label|dbfield|Fieldlabel|hidden|[no_db]|showlabel';
  }

}

?>
