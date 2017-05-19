<?php

class rex_ycom_media_handle
{
	protected static function plugin($plugin = 'media')
	{
		return rex_addon::get('ycom')->getPlugin($plugin);
	}

	public static function init(rex_extension_point $ep)
	{
		if (rex::isBackend())
		{
			rex_extension::register('MEDIA_FORM_EDIT', [get_called_class(), 'extendForm']);
			rex_extension::register('MEDIA_FORM_ADD', [get_called_class(), 'extendForm']);

			rex_extension::register('MEDIA_ADDED', [get_called_class(), 'extendForm']);
			rex_extension::register('MEDIA_UPDATED', [get_called_class(), 'extendForm']);
		}
	}

	public static function extendForm(rex_extension_point $ep)
    {
        $params = $ep->getParams();

        // Nur beim EDIT gibts auch ein Medium zum bearbeiten
        if ($ep->getName() == 'MEDIA_ADDED') 
        {
            $sql = rex_sql::factory();
            $qry = 'SELECT id FROM ' . rex::getTablePrefix() . 'media WHERE filename="' . $params['filename'] . '"';
            $sql->setQuery($qry);
            if ($sql->getRows() == 1)
            {
                $params['id'] = $sql->getValue('id');
            }
            else
            {
                throw new rex_exception('Error occured during file upload!');
            }
        }

        return $ep->getSubject() . static::renderFormAndSave($params);
    }

    protected static function getGroups()
    {
    	$groups = [];
    	if(class_exists('rex_ycom_group'))
    	{
    		$groups = array_replace($groups, rex_ycom_group::getGroups());
    	}
    	return $groups;
    }

    protected static function getUsers()
    {
    	$users = [];

    	$query = "SELECT `id`, `" . static::plugin('auth')->getConfig('login_field') . "` AS `label`, `status` FROM " . rex::getTable('ycom_user') ." ORDER BY `" . static::plugin('auth')->getConfig('login_field') . "`";
    	$sql = rex_sql::factory();
    	$sql->setQuery($query);

        if(count($rows = $sql->getArray()))
        {
        	foreach($rows as $row)
        	{
        		$users[$row['id']] = $row['label'] . ($row['status'] == 0 ? ' (' . static::plugin()->i18n('is_inactive') .')' : '');
        	}
        }
        unset($sql, $query, $rows, $row);

    	return $users;
    }

/*
    public static function getGroupsForMedia(rex_media $media)
    {
    	if($media->hasValue('ycom_groups'))
    	{
    		return rex_var::toArray($media->getValue('ycom_groups'));
    	}

    	return [];
    }

    public static function getUsersForMedia(rex_media $media)
    {
    	if($media->hasValue('ycom_users'))
    	{
    		return rex_var::toArray($media->getValue('ycom_users'));
    	}
    	
    	return [];
    }

    public static function isMediaSecured(rex_media $media)
    {
    	return $media->hasValue('ycom_auth') ? (bool) $media->getValue('ycom_auth') : false;
    }

    public static function isMediaAllowedForUserGroups(rex_media $media, rex_ycom_user $ycom_user)
    {
    	if($ycom_user->hasValue('ycom_groups') && $media->hasValue('ycom_groups'))
		{
			// Gruppenzuordnung der Datei aus dem Feld med_ycom_groups auslesen
			$ycom_file_groups = rex_var::toArray($media->getValue('ycom_groups'));
			$ycom_file_groups = array_filter($ycom_file_groups);

			if(!empty($ycom_file_groups))
			{
				// Nutzergruppen auslesen
				$ycom_user_groups = explode(',', $ycom_user->getValue('ycom_groups'));
				$ycom_user_groups = array_filter($ycom_user_groups);

				// Wenn mindestens eine Gruppe übereinstimmt, dann erlauben!
				return count ( array_intersect($ycom_user_groups, $ycom_file_groups) ) > 0;
			}
		}

		return true;
    }

    public static function isMediaAllowedForUser(rex_media $media, rex_ycom_user $ycom_user)
    {
    	if($media->hasValue('ycom_auth_users'))
		{
			// Nutzerzuordnung der Datei aus dem Feld med_ycom_users auslesen
			$ycom_file_users = rex_var::toArray($media->getValue('ycom_auth_users'));
			$ycom_file_users = array_filter($ycom_file_users);

			if(!empty($ycom_file_users))
			{
				// Nutzer erlauben wenn in Nutzerzuordnung enthalten 
				return in_array($ycom_user->getValue('id'), $ycom_file_users);
			}
		}
		
		return true;
    }

    public static function hasUserMediaPermission(rex_media $media, rex_ycom_user $ycom_user = null)
    {
    	if($media->hasValue('ycom_auth'))
    	{
    		if((bool) $media->getValue('ycom_auth'))
    		{
    			if(empty($ycom_user))
    			{
    				return false;
    			}

    			return static::isMediaAllowedForUserGroup($media, $ycom_user) && static::isMediaAllowedForUser($media, $ycom_user);				
    		}
    	}

    	return true;
    }

*/
    protected static function getFormFields(rex_media $media)
    {

		$dropdowns = [];
		$html = '';

    	if($groups = static::getGroups())
    	{
	    	$select = new rex_select();
	    	$select->setAttribute('multiple', 'multiple');
	    	$select->setAttribute('size', 5);
	    	$select->setAttribute('id', 'rex-mediapool-ycom_groups');
	    	$select->setAttribute('name', 'ycom_groups[]');
	    	$select->setAttribute('class', 'form-control');
	    	$select->setSelected(rex_ycom_media::getGroupsForMedia($media, false));
	    	$select->addOptions($groups);

	    	$dropdowns[] = '<label for="rex-mediapool-ycom_groups">' . static::plugin()->i18n('allowed_groups') . '</label>' . $select->get() . '<p class="help-block rex-note">' . static::plugin()->i18n('empty_for_all_groups') . '</p>';
    	}

    	if($users = static::getUsers())
    	{
	    	$select = new rex_select();
	    	$select->setAttribute('multiple', 'multiple');
	    	$select->setAttribute('size', 5);
	    	$select->setAttribute('id', 'rex-mediapool-ycom_users');
	    	$select->setAttribute('name', 'ycom_users[]');
	    	$select->setAttribute('class', 'form-control');
	    	$select->setSelected(rex_ycom_media::getUsersForMedia($media, false));
	    	$select->addOptions($users);

	    	$dropdowns[] = '<label for="rex-mediapool-ycom_users">' . static::plugin()->i18n('allowed_users') . '</label>' . $select->get() . '<p class="help-block rex-note">' . static::plugin()->i18n('empty_for_all_users') . '</p>';
    	}

    	$html.= '<dl class="rex-form-group form-group">' .
    				'<dd>' .
    					'<div class="checkbox"><label for="rex-ycom_auth"><input type="hidden" name="ycom_auth" value="0" /><input type="checkbox" id="rex-ycom_auth" name="ycom_auth" value="1"' . (rex_ycom_media::isMediaSecured($media) ? ' checked="checked"' : '') . (rex_ycom_media::isMediaWithinSecuredCategory($media) ? ' disabled="disabled"' : '') . '>' . static::plugin()->i18n('file_is_secured') . (rex_ycom_media::isMediaWithinSecuredCategory($media) ? ' (' . static::plugin()->i18n('secured_by_cat_permissions') . ')' : '') . '</label></div>';

    	if(!empty($dropdowns))
    	{
    		$html.= '<script type="text/javascript"><!--
jQuery(function($){
	$(document).on(\'rex:ready\', function(e) {
		console.log("LOADED");
		var checkbox = document.querySelector(\'#rex-ycom_auth\'),
			func = function(e){
				console.log("CLICK");
				var div = document.querySelector(\'#rex-ycom_auth-auth-hidden\');
				if(div)
				{
					if(this.checked)
					{
						div.style.display = \'block\';
					}
					else
					{
						div.style.display = \'none\';
					}
				}
			};

		if(checkbox)
		{
			checkbox.addEventListener(\'click\', func);
			func.bind(checkbox)();
		}
	});
})
//-->
</script>';

			$html.= '<div id="rex-ycom_auth-auth-hidden" style="display: none"><div class="row">';

			foreach($dropdowns as $i => $dropdown)
			{
				$html.= '<div class="col-sm-' . floor(12 / count($dropdowns)) . '">' . $dropdown . '</div>';
			}

			$html.= '</div></div>';
    	}

    	$html.= '</dd></dl>';

    	return $html;

    }

    protected static function saveFormFields(rex_media $media)
    {
    	if (rex_request_method() == 'post')
    	{
    		$update_fields = [];

    		$auth = rex_post('ycom_auth', 'bool', null);
    		if($auth !== null)
    		{
    			$update_fields['ycom_auth'] = $auth ? '1' : '0';
    		}

    		$groups = rex_post('ycom_groups', 'array', null);
    		if($groups !== null)
    		{
    			$groups = array_filter($groups);
    			$update_fields['ycom_groups'] = !empty($groups) ? json_encode($groups) : '';
    		}

    		$users = rex_post('ycom_users', 'array', null);
    		if($users !== null)
    		{
    			$users = array_filter($users);
    			$update_fields['ycom_users'] = !empty($users) ? json_encode($users) : '';
    		}

    		if(!empty($update_fields))
    		{

    			$mediasql = rex_sql::factory();
		    //  $media->setDebug();
		        $mediasql->setTable(rex::getTablePrefix() . 'media');
		        $mediasql->setWhere('id=:mediaid', ['mediaid' => $media->getId()]);

		        foreach($update_fields as $k => $v)
		        {
		        	$mediasql->setValue($k, $v);
		        }

		        $mediasql->update();
				rex_media_cache::delete($media->getFileName());

		        $media = rex_media::get($media->getFileName());
    		}
        }
        return $media;
    }

    protected static function renderFormAndSave(array $params = [])
    {
    	$html = '';
    	
    	if(!empty($params['id']))
    	{
    		$sql = rex_sql::factory();
            $qry = 'SELECT `filename` FROM `' . rex::getTable('media') . '` WHERE id="' . $params['id'] . '"';
    		$sql->setQuery($qry);
            if ($sql->getRows() == 1)
            {
                if($media = rex_media::get($sql->getValue('filename')))
                {
                	$media = static::saveFormFields($media);

                	$html = static::getFormFields($media);
                }
            }
            unset($sql, $qry);

    		if(!empty($media))
    		{
    			
    		}
    	}

    	return $html;
    }
}