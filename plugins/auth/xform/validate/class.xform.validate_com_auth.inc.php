<?PHP

class rex_xform_validate_com_auth extends rex_xform_validate_abstract 
{

	function postValueAction()
	{
		global $REX;

    $login = "";
    $psw = "";
    $stay = "";

    foreach($this->params["value_pool"]["sql"] as $k => $v)
    {
      if($k == $this->getElement(2)) {
      	$login = $v;
      }elseif($k == $this->getElement(3)) {
      	$psw = $v;
      }elseif($k == $this->getElement(4)) {
      	$stay = $v;
      }
    }

    if($login == "" || $psw == "")
    {
      $this->params["warning"][] = 1;
      $this->params["warning_messages"][] = $this->getElement(5);
      rex_com_auth::clearUserSession();
      return;
    }

    /*
      login_status
      0: not logged in
      1: logged in
      2: has logged in
      3: has logged out
      4: login failed
    */

    $status = rex_com_auth::login($login, $psw, $stay, false); // no logout

    if($status != 2)
    {
      $this->params["warning"][] = 1;
      $this->params["warning_messages"][] = $this->getElement(6);
      rex_com_auth::clearUserSession();
    }

		return;

	}
	
	function getDescription()
	{
		return "com_auth -> prüft ob login und registriert user, beispiel: validate|com_auth|loginfield|passwordfield|stayfield|warning_message_enterloginpsw|warning_message_login_failed";
	}
	
}

?>