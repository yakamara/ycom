<?php 

class rex_com_user {


  private $user = array();

  // ----------------

  function __construct($user) {
    $this->user = $user;
  }

  static function getById($id) {
    $usql = rex_sql::factory();
    $us = $usql->getArray('select * from rex_com_user where id='.intval($id)); 
    if(count($us) == 1) {
      $r = new rex_com_user($us[0]);
      return $r;
    } else {
      return false;
    }
  }

  // ----------------
  
  public function getId() {
    return $this->user["id"];
  }

  public function getEmail() {
    return $this->user["email"];
  }

  public function getName() {
    return $this->user["name"];
  }

  public function getFirstName() {
    return $this->user["firstname"];
  }
  
  public function getFullName() {
    return $this->user["firstname"]." ".$this->user["name"];
  }
  
  public function getUserArray() {
    return $this->user;
  }

  // ----------------

	static function triggerUserCreated($id, $params = array())
	{

    // das hier verwendet die anwendung
    // rex_register_extension('COM_USER_CREATED', 'rex_com_xform_add');
	  
	  rex_register_extension_point("COM_USER_CREATED", $id, $params, true); // read only
	
	}

  static function triggerUserUpdated($id, $params = array())
	{

    // das hier verwendet die anwendung
    // rex_register_extension('COM_USER_CREATED', 'rex_com_xform_add');
	  
	  rex_register_extension_point("COM_USER_UPDATED", $id, $params, true); // read only
	
	}

}	
	
