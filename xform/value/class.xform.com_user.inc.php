<?php

// ************************************* XFORM USER

class rex_xform_com_user extends rex_xform_abstract
{

  function enterObject()
  {
    global $REX;

    $show_label = "email";
    $show_value = "";
    $show_label = $this->getElement(6);

    $this->setValue(-1);
    if (isset($REX["COM_USER"]) && is_object($REX["COM_USER"])) {
      $this->setValue($REX["COM_USER"]->getValue($this->getElement(2)));
      $show_value = $REX["COM_USER"]->getValue($show_label);
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
    return "com_user -> Beispiel: com_user|label|dbfield|Fieldlabel|hidden|[no_db]|showlabel";
  }

}

?>