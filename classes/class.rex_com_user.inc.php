<?php 

class rex_com_user {

  static $users = array();
  private $user = array();

  // ----------------

  function __construct($user) {
    $this->user = $user;
  }

  static function getMe() {
    global $REX;
    if (isset($REX['COM_USER'])) {
      return rex_com_user::getById($REX["COM_USER"]->USER->getValue('id'));
    } else {
      return false;
    }
  }

  static function getById($id) {
  
    if(isset(self::$users[$id])) {
      return self::$users[$id];
    }
  
    $usql = rex_sql::factory();
    $us = $usql->getArray('select * from rex_com_user where id='.intval($id)); 
    if(count($us) == 1) {
      $r = new rex_com_user($us[0]);
      self::$users[$us[0]["id"]] = $r;
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

  public function isAdmin() {
    if($this->user["admin"] == 1) {
      return true;
    } else {
      return false;
    }
  }
  
  public function getValue($val) {
    return (@$this->user[$val]);
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
	
