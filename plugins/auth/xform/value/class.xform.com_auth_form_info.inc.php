<?php

class rex_xform_com_auth_form_info extends rex_xform_abstract
{

  function enterObject()
  {
    global $REX;
    
    $login_name = rex_request($REX['ADDON']['community']['plugin_auth']['request']['name'],"string");
    $referer = rex_request($REX['ADDON']['community']['plugin_auth']['request']['ref'],"string");
    $logout = rex_request($REX['ADDON']['community']['plugin_auth']['request']['logout'],"int");

    $class = "form_warning";
    
    if ($logout) {
      $msg = $this->getElement(3);
    }
    
    if ($login_name) {
      $msg = $this->getElement(4);

    } else if($referer && rex_com_auth::getUser()) {

      $msg = $this->getElement(5);

    }

    if (!isset($msg)) {
      $msg = $this->getElement(2);
      $class = "form_info";      
    }

    if ($msg) {
      $this->params["form_output"][$this->getId()] = '<ul class="formcom_auth_form_info '.$class.' formlabel-'.$this->getName().'" id="'.$this->getHTMLId().'">';
      $this->params["form_output"][$this->getId()] .= '<li>'.$msg.'</li>';
      $this->params["form_output"][$this->getId()] .= '</ul>';
    }
  }

  function getDescription()
  {
    return "com_auth_form_info -> Beispiel: com_auth_form_info|label|login_msg|logout_msg|wronglogin_msg|accessdenied_msg";
  }

}

?>