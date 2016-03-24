<?php

/**
 * Erweitert die rex_navigation um Rechte die abgefragt werden
 *
 * @package redaxo5
 * @version svn:$Id$
 */

class rex_ycom_navigation extends rex_navigation
{

  function rex_ycom_navigation() {
    $this->addCallback("rex_ycom_navigation::checkPerm");
  }

  function checkPerm($nav, $depth)
  {
    return rex_ycom_auth::checkPerm($nav);
  }
  
}
