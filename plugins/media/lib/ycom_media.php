<?php

class rex_ycom_media extends \rex_yform_manager_dataset
{
    public static $debug = false;
    public static $secured_categories = null;

    const MEDIA_FIELD_AUTH = 'ycom_auth';
    const MEDIA_FIELD_GROUPS = 'ycom_groups';
    const MEDIA_FIELD_USERS = 'ycom_users';

    const TABLE_FIELD_CAT = 'ycom_media_cat';
    const TABLE_FIELD_GROUPS = 'ycom_media_cat_groups';
    const TABLE_FIELD_USERS = 'ycom_media_cat_users';

    static function getCategories()
    {
    	if(!isset(self::$secured_categories))
    	{
    		self::$secured_categories = [];

    		foreach (self::query()->find() as $category)
	        {
	        	if($category = rex_media_category::get($category->ycom_media_cat))
	        	{
	        		self::$secured_categories[$category->getId()] = $category->getName();	
	        	}
	        }
	        unset($category);

	        asort(self::$secured_categories);
    	}
        
        return self::$secured_categories;
    }

    static function getCategory($category_id)
    {
    	$return = [
    		'groups' => [],
    		'users' => []
    	];

    	$groupfield = self::TABLE_FIELD_GROUPS;
    	$userfield = self::TABLE_FIELD_USERS;

    	foreach (self::query()->where(self::TABLE_FIELD_CAT, $category_id)->find() as $category)
        {
        	if(!empty($category->$groupfield))
        	{
        		$return['groups'] = array_merge($return['groups'], explode(',', $category->$groupfield));
        	}

        	if(!empty($category->$userfield))
        	{
        		$return['users'] = array_merge($return['users'], explode(',', $category->$userfield));
        	}

        	if($media_cat = rex_media_category::get($category_id))
        	{
        		$media_categories = $media_cat->getPathAsArray();
        		$matches = array_intersect($media_categories, self::getCategories());
        		if(!empty($matches))
        		{
        			foreach($matches as $match_id)
        			{
        				foreach(self::getCategory($match_id) as $k => $v)
        				{
        					$return[$k] = array_merge($return[$k], $v);
        				}
        				unset($k, $v);
        			}
        			unset($match_id);
        		}
        		unset($matches, $media_categories);
        	}
        	unset($media_cat);

        }
        unset($category);


        foreach($return as &$field)
        {
        	$field = array_filter($field);
        	$field = array_unique($field);
        }
        
        return $return;
    }

    protected static function getUser()
    {
    	return rex_ycom_auth::getUser();
    }


    protected static function getMediaObject($media)
    {
    	if(empty($media))
    	{
    		return null;
    	}
        
    	if(!is_object($media))
    	{
    		if( (string) $media === strval((int) $media) )
			{
				// get media by ID
				$sql = rex_sql::factory();
				$sql->setQuery("SELECT `filename` FROM `" . rex::getTable('media') . "` WHERE `id` = " . strval((int) $media));
				if($sql->getRows())
				{
					return self::getMediaObject($sql);	
				}
			}
			else
			{
				// get media by string (filename)
				return rex_media::get($media);
			}
    	}
    	else
    	{
	    	if($media instanceof rex_media)
	    	{
	    		// media is already an rex_media object
	    		return $media;
	    	}

	    	if(is_callable([$media, 'hasValue']) && is_callable([$media, 'getValue']))
	    	{
	    		// $media can be an SQL object and we extract the filename out of it...
	    		if($media->hasValue('filename'))
	    		{
	    			if($media->getvalue('filename'))
	    			{
	    				return rex_media::get($media->getvalue('filename'));
	    			}
	    		}
	    	}
    	}

    	return null;
    }

    public static function getSecuredCategoryIdsMediaIsWithin(rex_media $media)
    {
        $secured_categories = array_keys(self::getCategories());
        if(empty($secured_categories))
        {
        	// es gibt keine geschützten Kategorien
        	return [];
        }

    	$media_category_id = (int) $media->getCategoryId();
        if(empty($media_category_id))
        {
        	// Datei liegt in keiner Kategorie, also auch in keiner geschuetzten
        	return [];
        }

        $media_category = rex_media_category::get($media_category_id);
        if(!$media_category)
        {
        	// wenn die Kategorie nicht existiert...
        	return [];
        }

        $media_categories = $media_category->getPathAsArray();

        return array_intersect($secured_categories, $media_categories);
    }


    public static function isMediaWithinSecuredCategory(rex_media $media)
    {
        return count ( self::getSecuredCategoryIdsMediaIsWithin($media) ) > 0;
    }


    public static function isMediaSecured(rex_media &$media)
    {
    	if($media->hasValue(self::MEDIA_FIELD_AUTH) && (bool) $media->getValue(self::MEDIA_FIELD_AUTH))
    	{
    		// Die Datei ist individuell auf 'geschützt' gesetzt.
    		return true;
    	}

    	if(self::isMediaWithinSecuredCategory($media))
    	{
    		// Die Datei ist aufgrund ihrer Kategoriezugehörigkeit auf 'geschützt' gesetzt.
    		return true;
    	}

    	return false;
    }

	public static function getGroupsForMedia(rex_media &$media, $use_category_defintions = true)
    {
    	$groups = [];
    	
    	if(self::isMediaSecured($media))
    	{
    		// die Datei-Gruppen-Zuordnung speichern
    		if($media->hasValue(self::MEDIA_FIELD_GROUPS) && $media->getValue(self::MEDIA_FIELD_GROUPS))
	    	{
	    		$groups = array_merge($groups, rex_var::toArray($media->getValue(self::MEDIA_FIELD_GROUPS)));
	    	}

	    	// die Gruppen aus den Kategorie-Zuordnungen lesen
	    	if((bool) $use_category_defintions)
	    	{
		    	if($secured_cats = self::getSecuredCategoryIdsMediaIsWithin($media))
		    	{
		    		foreach($secured_cats as $cat_id)
		    		{
		    			if($auth = self::getCategory($cat_id))
		    			{
		    				$groups = array_merge($groups, $auth['groups']);
		    			}
		    			unset($auth);
		    		}
		    		unset($cat_id);
		    	}
		    	unset($secured_cats);
	    	}
    	}

    	$groups = array_unique($groups);
    	$groups = array_filter($groups);

    	return $groups;
    }

	public static function getUsersForMedia(rex_media &$media, $use_category_defintions = true)
    {
    	$users = [];
    	
    	if(self::isMediaSecured($media))
    	{
    		// die Datei-Gruppen-Zuordnung speichern
    		if($media->hasValue(self::MEDIA_FIELD_USERS) && $media->getValue(self::MEDIA_FIELD_USERS))
	    	{
	    		$users = array_merge($users, rex_var::toArray($media->getValue(self::MEDIA_FIELD_USERS)));
	    	}

	    	// die Gruppen aus den Kategorie-Zuordnungen lesen
	    	if((bool) $use_category_defintions)
	    	{
		    	if($secured_cats = self::getSecuredCategoryIdsMediaIsWithin($media))
		    	{
		    		foreach($secured_cats as $cat_id)
		    		{
		    			if($auth = self::getCategory($cat_id))
		    			{
		    				$users = array_merge($users, $auth['users']);
		    			}
		    			unset($auth);
		    		}
		    		unset($cat_id);
		    	}
		    	unset($secured_cats);
		    }
    	}

    	$users = array_unique($users);
    	$users = array_filter($users);

    	return $users;
    }

    public static function checkPerm($media_var)
    {
    	$media = self::getMediaObject($media_var);

        if(empty($media) || !($media instanceof rex_media))
        {
        	return true;
        }

        // wenn die Authentifizierung deaktiviert ist, die Datei als "erlaubt" zurückgeben.
        if (rex_addon::get('ycom')->getPlugin('auth')->getConfig('auth_active') != '1') {
            return true;
        }

        if(!self::isMediaSecured($media))
        {
        	// Datei ist nicht geschützt, also als "erlaubt" zurückgeben.
        	return true;
        }

        if(self::getUser())
        {
			if($users = self::getUsersForMedia($media))
	        {
	        	// wenn Nutzer in Nutzerzuordnung enthalten - erlauben!
	        	if(in_array(self::getUser()->getValue('id'), $users))
	        	{
	        		return true;
	        	}
			}

    		if($groups = self::getGroupsForMedia($media))
	        {
	        	// wenn die Datei nur für bestimmte Gruppen erlaubt ist, prüfe die Nutzer-Gruppen-Zugehörigkeit

	        	$ycom_user_groups = explode(',', self::getUser()->getValue(MEDIA_FIELD_GROUPS));
				$ycom_user_groups = array_filter($ycom_user_groups);

				if(count ( array_intersect($ycom_user_groups, $groups) ) > 0)
				{
					// Wenn mindestens eine Gruppe übereinstimmt, dann erlauben!
					return true;
				}
			}

			// Keine Gruppen und keine Nutzer definiert, also per se erlauben!
			if(empty($users) && empty($groups))
			{
				return true;
			}
        }

        return false;
    }
}
