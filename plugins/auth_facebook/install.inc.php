<?php
/**
 * Plugin Facebook-Auth
 * @author m.lorch[at]it-kult[dot]de Markus Lorch
 * @author <a href="http://www.it-kult.de">www.it-kult.de</a>
 */

$REX['ADDON']['install']['auth_facebook'] = 1;
$REX['ADDON']['installmsg']['auth_facebook'] = '';

if (isset($I18N) && is_object($I18N))
	$I18N->appendFile($REX['INCLUDE_PATH'].'/addons/community/plugins/auth_facebook/lang'); 

## Checking dependencies
## http://www.redaxo.org/de/forum/post96341.html#p96341
## Not working with redaxo5 -> use in e.g. r5: OOAddon::isAvailable('facebook_sdk')
if($ADDONSsic['status']['community'] && $ADDONSsic['status']['xform'])
{
	//
	// Install Database
	//
	$sql = new rex_sql();
	
	## Field: authsource
	$sql->setQuery("SHOW COLUMNS FROM rex_com_user WHERE Field='authsource'");
	$REX['ADDON']['installmsg']['facebook'] = $sql->getError();
	
	if(!$sql->getRows() && $REX['ADDON']['installmsg']['auth_facebook'] == '')
	{
		$sql->setQuery("INSERT INTO rex_xform_field (id, table_name, prio, type_id, type_name, f1, f2, list_hidden, search) VALUES (NULL, 'rex_com_user', '100', 'value', 'text', 'authsource', 'translate:com_auth_authsource', '1', '0')");
		$REX['ADDON']['installmsg']['auth_facebook'] = $sql->getError();

		if($REX['ADDON']['installmsg']['auth_facebook'] == '')
		{
			$sql->setQuery("ALTER TABLE rex_com_user ADD authsource TEXT NOT NULL");
			$REX['ADDON']['installmsg']['auth_facebook'] = $sql->getError();
		}
	}
	
	## Field: facebookid
	$sql->setQuery("SHOW COLUMNS FROM rex_com_user WHERE Field='facebookid'");
	$REX['ADDON']['installmsg']['auth_facebook'] = $sql->getError();
	
	if(!$sql->getRows() && $REX['ADDON']['installmsg']['auth_facebook'] == '')
	{
		$sql->setQuery("INSERT INTO rex_xform_field (id, table_name, prio, type_id, type_name, f1, f2, list_hidden, search) VALUES (NULL, 'rex_com_user', '100', 'value', 'text', 'facebookid', 'translate:com_auth_facebook_facebookid', '1', '0')");
		$REX['ADDON']['installmsg']['auth_facebook'] = $sql->getError();
	
		if($REX['ADDON']['installmsg']['auth_facebook'] == '')
		{
			$sql->setQuery("ALTER TABLE rex_com_user ADD facebookid TEXT NOT NULL");
			$REX['ADDON']['installmsg']['auth_facebook'] = $sql->getError();
		}
	}
}
else
{
	$REX['ADDON']['installmsg']['auth_facebook'] = $I18N->msg('com_auth_facebook_error_missingaddons');
}


?>