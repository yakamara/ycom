<?php

class rex_ycom_user {

  static $users = array();
  private $user = array();

  // ----------------

  static function getTable()
  {
    return 'rex_ycom_user';
  }

  function __construct($user) {
    $this->user = $user;
  }






  static function triggerUserCreated($id, $params = array())
  {
    // rex_register_extension('YCOM_USER_CREATED', 'rex_ycom_yform_add');

    rex_register_extension_point("YCOM_USER_CREATED", $id, $params, true); // read only

  }

  static function triggerUserUpdated($id, $params = array())
  {

    // rex_register_extension('YCOM_USER_CREATED', 'rex_ycom_yform_add');

    rex_register_extension_point("YCOM_USER_UPDATED", $id, $params, true); // read only

  }




  /*



  static function getMe() {
    global $REX;
    if (isset($REX['YCOM_USER'])) {
      return rex_ycom_user::getById($REX["YCOM_USER"]->USER->getValue('id'));
    } else {
      return false;
    }
  }

  static function getById($id) {

    if(isset(self::$users[$id])) {
      return self::$users[$id];
    }

    $usql = rex_sql::factory();
    $us = $usql->getArray('select * from rex_ycom_user where id = ?', [$id]);
    if(count($us) == 1) {
      $r = new rex_ycom_user($us[0]);
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


  */

}

