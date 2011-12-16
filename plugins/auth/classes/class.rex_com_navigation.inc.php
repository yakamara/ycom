<?php

/**
 * Erweitert die rex_navigation um Rechte die abgefragt werden
 *
 * @package redaxo4
 * @version svn:$Id$
 
 
 * $nav->setFilter(
  array('art_type' => 1)
    );
 
 
 */

class rex_com_navigation extends rex_navigation
{

  var $filter = array();
  var $wrap_names = array('0'=>'<span>|</span>','1'=>'<span>|</span>','2'=>'<span>|</span>','3'=>'<span>|</span>','4'=>'<span>|</span>','5'=>'<span>|</span>');
  var $wrap_links = array();

  /*public*/ function setFilter($filter)
  {
    $this->filter = $filter;
  }

  /*public*/ function setNameWrap($wrap)
  {
    $this->wrap_names = $wrap; // <span>|</span>
  }

  /*public*/ function setLinkWrap($wrap)
  {
    $this->wrap_links = $wrap; // <span>|</span>
  }


  /**
   * Generiert eine Breadcrumb-Navigation
   * 
   * @param $startPageLabel Label der Startseite, falls FALSE keine Start-Page anzeigen
   * @param $includeCurrent True wenn der aktuelle Artikel enthalten sein soll, sonst FALSE
   * @param $category_id Id der Wurzelkategorie
   */
  /*public*/ function getBreadcrumb($startPageLabel, $includeCurrent = FALSE, $category_id = 0)
  {
    if(!$this->_setActivePath()) return FALSE;
    
    global $REX;
    
    $path = $this->path;
      
    $i = 1;
    $lis = '';
    
    if($startPageLabel)
    {
      $lis .= '<li class="rex-lvl'. $i .'"><a href="'. rex_getUrl($REX['START_ARTICLE_ID']) .'">'. htmlspecialchars($startPageLabel) .'</a></li>';
      $i++;
    
      // StartArticle nicht doppelt anzeigen
      if(isset($path[0]) && $path[0] == $REX['START_ARTICLE_ID'])
      {
        unset($path[0]);
      }
    }
    
    foreach($path as $pathItem)
    {
      $cat = OOCategory::getCategoryById($pathItem);
      
      if($this->_check($cat,$i))
      {
        echo 'haha';
        $lis .= '<li class="rex-lvl'. $i .'"><a href="'. $cat->getUrl() .'">'. htmlspecialchars($cat->getName()) .'</a></li>';
        $i++;
      }
    }
  
    if($includeCurrent)
    {
      $art = OOArticle::getArticleById($this->current_article_id);
      
      if($this->_check($art,$i))
        if(!$art->isStartpage())
        {
          $lis .= '<li class="rex-lvl'. $i .'">'. htmlspecialchars($art->getName()) .'</li>';
        }else
        {
          $cat = OOCategory::getCategoryById($this->current_article_id);
          $lis .= '<li class="rex-lvl'. $i .'">'. htmlspecialchars($cat->getName()) .'</li>';
        }
    }
    
    return '<ul class="rex-breadcrumb">'. $lis .'</ul>';
  }

    
  /*protected*/ function _getNavigation($category_id,$ignore_offlines = TRUE)
  {
  
    static $depth = 0;
    
    if($category_id < 1)
      $nav_obj = OOCategory::getRootCategories($ignore_offlines);
    else
      $nav_obj = OOCategory::getChildrenById($category_id, $ignore_offlines);
    

    $nav_real = array();    
    
    foreach($nav_obj as $nav)
    {
      // Filter und Rechte pr�fen
      if($this->_check($nav,$depth))
      {
        $nav_real[] = $nav;
      }
    }
    
    $counter = 0;
    $count = count($nav_real);
//    $count = 4;

    $return = "";
    if(count($nav_real)>0)
      $return .= '<ul class="navi-lev-'. ($depth+1) .'">';

    foreach($nav_real as $nav)
    {
    
      $counter++;
      
      $liClass = '';
      $linkClass = '';
      
      if ($counter == 1)
        $liClass .= ' first';

      if ($counter == $count)
        $liClass .= ' last';
        
      // classes abhaengig vom pfad
      if($nav->getId() == $this->current_category_id)
      {
        $liClass .= ' current';
        $linkClass .= ' current';
      }elseif (in_array($nav->getId(),$this->path))
      {
        $liClass .= ' active';
        $linkClass .= ' active';
      }else
      {
        $liClass .= ' normal';
      }
    
      // classes abhaengig vom level
      if(isset($this->classes[$depth]))
        $liClass .= ' '. $this->classes[$depth];
    
      if(isset($this->linkclasses[$depth]))
        $linkClass .= ' '. $this->linkclasses[$depth];
      
      $name = str_replace(' ## ', '<br />', htmlspecialchars($nav->getName()), $str_count);
      if ($str_count >= 1)
        $linkClass .= ' manbreak';
      
      $linkClass = $linkClass == '' ? '' : ' class="'. ltrim($linkClass) .'"';

      if(isset($this->wrap_names[$depth]) && $this->wrap_names[$depth] != "")
      {
        $wrap = explode('|',$this->wrap_names[$depth]);
        $name = $wrap[0].$name.$wrap[1];
      }
      
      $link = '<a'. $linkClass .' href="'.$nav->getUrl().'">'.$name.'</a>';
      if(isset($this->wrap_links[$depth]) && $this->wrap_links[$depth] != "")
      {
        $wrap = explode('|',$this->wrap_links[$depth]);
        $link = $wrap[0].$link.$wrap[1];
      }

      $return .= '<li class="navi-id-'. $nav->getId() . $liClass .'">'.$link;
    
      $depth++;
      if(($this->open || $nav->getId() == $this->current_category_id || in_array($nav->getId(),$this->path)) && ($this->depth > $depth || $this->depth < 0))
      {
        $return .= $this->_getNavigation($nav->getId(),$ignore_offlines);
      }
      $depth--;
    
      $return .= '</li>';
    }
    
    if(count($nav_real)>0)
      $return .= '</ul>';
    
    return $return;
  }
  
  
  function _check($nav,$level)
  {
    if(!$this->_checkFilter($nav,$level)) 
      return FALSE;

    if(!$this->_checkPerm($nav,$level)) 
      return FALSE;
    return TRUE;
  }
  
  function _checkFilter($nav,$level)
  {
    foreach($this->filter as $k => $v)
    {
      if($nav->getValue($k) != $v)
        return FALSE;
    }
    return TRUE;  
  }
  
  function _checkPerm($nav,$level)
  {
    return rex_com_auth::checkperm($nav);
  }
  
  
  
  
  
}


