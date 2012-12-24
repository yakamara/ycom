<?PHP

class rex_xform_validate_com_newsletter_auth extends rex_xform_validate_abstract 
{

	function enterObject()
	{
		global $REX;
		
		$error = TRUE;
		
		$vars = array();
		$e = explode(",",$this->getElement(2));
		foreach($e as $v) {
			$w = explode("=",$v);
			$label = $w[0];
			$value = trim(rex_request($w[1],"string",""));
			$vars[] = ' `'.$label.'`="'.mysql_real_escape_string($value).'"';
		}

    $query_extras = "";
    if($this->getElement(3) != "") {
      $vars[] = ' ('.$this->getElement(3).') ';
    }

    $table = mysql_real_escape_string($this->getElement(5));
    if($table == "") {
      $table = "rex_com_user";
    }

    $gu = rex_sql::factory();
    if ($this->params["debug"]) {
      $gu->debugsql = 1;
    }
    $gu->setQuery('select * from `'.$table.'` where '.implode(" and ",$vars).'');
    
    if($gu->getRows() != 1)
    {
      $this->params["warning"][] = 1;
      $this->params["warning_messages"][] = rex_translate($this->getElement(4));
      
    }else {
    
      $main_id = (int) $gu->getValue("id");
      
      $this->params["main_where"] = 'id='.$main_id;
      $this->params["main_id"] = $main_id;
      $this->params["main_table"] = $table;
    
    }
    
  	return;

	}
	
	function getDescription()
	{
		return "com_newsletter_auth -> beispiel: validate|com_newsletter_auth|label1=request1,label2=request2|status>0|warning_message|[rex_com_user] ";
	}
}
?>