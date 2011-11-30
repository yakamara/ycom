<?php
## Getting Facebook parameters
$fbstate = rex_get("state","string");
$fbcode = rex_get("code","string");

## Execute only if parameters given
if($fbstate != '' && $fbcode != '')
{
	if($REX['ADDON']['community']['plugin_auth_facebook']['facebook']->getUser())
	{
		if(rex_com_auth_facebook::checkRequiredPerms())
		{
			## Get User Array
			$fbuser = $REX['ADDON']['community']['plugin_auth_facebook']['facebook']->api('/me','GET');
			
			## Check if User Exists in Database
			$sql = new rex_sql();
			$sql->setQuery('SELECT facebookid FROM rex_com_user WHERE facebookid = '.$fbuser['id'].'');
			
			if($sql->getRows() == 0)
			{
				//
				// Sync facebook user to database
				//			
				$fields = ''; $values = '';
				## Translate datafields
				foreach($REX['ADDON']['community']['plugin_auth_facebook']['synctranslation'] as $key => $value)
				{
					$fields .= "$key, ";
					$values .= "'".$fbuser[$value]."', ";
				}
				
				## Adding defaultgroups
				if(isset($REX['ADDON']['community']['plugin_auth_facebook']['defaultgroups']))
				{
					$fields .= "rex_com_group, ";
					$values .= "'".implode(',' , $REX['ADDON']['community']['plugin_auth_facebook']['defaultgroups'])."',";
				}
				
				//$sql->debugsql = true;
				## Create new database user
				$sql->setQuery("INSERT INTO rex_com_user (".$fields." login, password, status, authsource, facebookid) VALUES (".$values." '".$fbuser['first_name'].".".$fbuser['last_name'].".fb.".$fbuser['id']."', '".rex_com_auth_facebook::generatePassword('32')."', '1', 'facebook', '".$fbuser['id']."')");
				//echo $sql->error;
			}
			
			## ToDo: 
			// Bestimte Felder auf wunsch bei jedem Login erneut Syncronisieren.
	
			## Set Login
			$REX['COM_USER'] = new rex_login();
			$REX['COM_USER']->setSqlDb(1);
			$REX['COM_USER']->setSysID(rex_com_auth::getLoginKey());
			$REX['COM_USER']->setSessiontime(7200);
			$REX['COM_USER']->setUserquery('select * from rex_com_user where facebookid='.$fbuser['id'].' and status>0');
			$REX['COM_USER']->setLogin('Dummy','Dummy'); //Setting dummys for Login
			$REX['COM_USER']->setLoginquery('select * from rex_com_user where facebookid='.$fbuser['id'].' and status>0');
			
			if($REX['COM_USER']->checkLogin())		
				rex_redirect($REX['ADDON']['community']['plugin_auth']['article_login_ok']);
		}
	}
	
	if(!$REX['ADDON']['community']['plugin_auth_facebook']['facebook']->getUser() || !(isset($REX["COM_USER"]) && is_object($REX["COM_USER"])))
		rex_redirect($REX['ADDON']['community']['plugin_auth']['article_login_failed'],'',array('rex_com_auth_info'=>'2'));
}
?>