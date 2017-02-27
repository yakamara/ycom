<?php

/**
 * Erweitert die rex_navigation um Rechte die abgefragt werden
 *
 * @package redaxo4
 * @version svn:$Id$
 */

class rex_com_navigation extends rex_navigation
{

  // this is the new style constructor used by newer php versions.
  // important: if you change the signatur of this method, change also the signature of rex_com_navigation()
  function __construct()
  {
    $this->rex_com_navigation();
  }

  // this is the deprecated old style constructor kept for compat reasons. 
  // important: if you change the signatur of this method, change also the signature of __construct()
  function rex_com_navigation() {
    $this->addCallback("rex_com_navigation::checkPerm");
  }

  function checkPerm($nav, $depth)
  {
    return rex_com_auth::checkPerm($nav);

  }
  
}
