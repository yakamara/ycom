<?php

class rex_xform_com_auth_form_logout extends rex_xform_abstract
{

	function enterObject()
	{
		global $REX;
		
		if(!$REX["REDAXO"]) {
			ob_end_clean();
			ob_end_clean();
			header('Location:'.rex_getUrl($REX['START_ARTICLE_ID'],'',array("rex_com_auth_logout"=>1),"&"));
			exit;
		}
		return;

	}

	function getDescription()
	{
		return "com_auth_form_logout -> Beispiel: com_auth_form_logout|label|";
	}

}

?>