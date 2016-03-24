<?php
     
class rex_yform_value_ycom_auth_form_info extends rex_yform_value_abstract
{

  function enterObject()
  {
    $login_name = rex_request(rex_config::get('ycom', 'auth_request_name'),"string");
    $referer = rex_request(rex_config::get('ycom', 'auth_request_ref'),"string");
    $logout = rex_request(rex_config::get('ycom', 'auth_request_logout'),"int");

    $class = "form_warning";
    
    if ($logout) {
      $msg = $this->getElement(3);
    }
    
    if ($login_name) {
      $msg = $this->getElement(4);

    } else if($referer && rex_ycom_auth::getUser()) {
      $msg = $this->getElement(5);

    }

    if (!isset($msg)) {
      $msg = $this->getElement(2);
      $class = "form_info";      
    }

    if ($msg) {
      $this->params["form_output"][$this->getId()] = '<ul class="form_ycom_auth_form_info '.$class.' formlabel-'.$this->getName().'" id="'.$this->getHTMLId().'">';
      $this->params["form_output"][$this->getId()] .= '<li>'.$msg.'</li>';
      $this->params["form_output"][$this->getId()] .= '</ul>';
    }

  }

  function getDescription()
  {
    return "ycom_auth_form_info -> Beispiel: ycom_auth_form_info|label|login_msg|logout_msg|wronglogin_msg|accessdenied_msg";
  }
}

?>
