<?php

class rex_xform_com_auth_facebook_loginbutton extends rex_xform_abstract
{
  function enterObject()
  {
    global $REX;
    $this->params["form_output"][$this->getId()] = '
        <p class="com_facebook_loginbutton">
          <a class="com_facebook_loginbutton" href="'.rex_com_auth_facebook::getLoginUrl().'" id="'.$this->getFieldId().'" />'.$this->getElement(1).'</a>
        </p>';
  }

  function getDescription()
  {
    return "com_facebook_loginbutton -> Beispiel: com_auth_facebook_loginbutton|label";
  }
}
?>