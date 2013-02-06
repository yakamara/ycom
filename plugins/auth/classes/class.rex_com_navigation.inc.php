<?php

/**
 * Erweitert die rex_navigation um Rechte die abgefragt werden
 *
 * @package redaxo4
 * @version svn:$Id$
 */

class rex_com_navigation extends rex_navigation
{

  function rex_com_navigation() {
    $this->addCallback("rex_com_navigation::checkPerm");
  }

  function checkPerm($nav, $depth)
  {
    return rex_com_auth::checkPerm($nav);

  }
  
}
