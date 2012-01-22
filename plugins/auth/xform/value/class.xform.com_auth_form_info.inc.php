<?php

class rex_xform_com_auth_form_info extends rex_xform_abstract
{

  function enterObject()
  {
    global $REX;

    ## Setting up info - errors first
	if(isset($REX['ADDON']['community']['plugin_auth']['errormsg']))
	{
	  $info = $REX['ADDON']['community']['plugin_auth']['errormsg'];
	  $class = "form_warning";
	}
	else
	{
	  if(isset($REX['ADDON']['community']['plugin_auth']['infomsg']))
	    $info = $REX['ADDON']['community']['plugin_auth']['infomsg'];
	  elseif($this->getElement(2))
	    $info = array($this->getElement(2));
	  $class = "form_info";
	}
	
	## Building output
	if(isset($info) && is_array($info))
    {
      $this->params["form_output"][$this->getId()] = '<ul class="formcom_auth_form_info '.$class.' formlabel-'.$this->getName().'" id="'.$this->getHTMLId().'">';
      
      foreach($info as $message)
        $this->params["form_output"][$this->getId()] .= '<li>'.$message.'</li>';
      
      $this->params["form_output"][$this->getId()] .= '</ul>';
    }
  }

  function getDescription()
  {
    return "com_auth_form_info -> Beispiel: com_auth_form_info|label|[defaultmessage]";
  }

}

?>