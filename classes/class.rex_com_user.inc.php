<?php 

class rex_com_user {


  /*
  
  
  
  
  */


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
	
