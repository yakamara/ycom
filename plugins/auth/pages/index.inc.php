<?php

$info = '';
$warning = '';
$modules = array(	
//		"login"		=> array(		"key" => "login", 			"search" => "module:com_auth_login",			"name" => "com:auth - Login"),
// 		"pswchange"	=> array(		"key" => "pswchange", 		"search" => "module:com_auth_pswchange",		"name" => "com:auth - Change Password"),
// 		"profilechange"	=> array(	"key" => "profilechange", 	"search" => "module:com_auth_profilechange",	"name" => "com:auth - Profile"),
	);

$xform_user_fields = rex_xform_manager_table::getXFormFieldsByType("rex_com_user","value");

if(rex_request("func","string")=="update")
{

	$REX['ADDON']['community']['plugin_auth']['auth_active'] = rex_request("auth_active","int");
	$REX['ADDON']['community']['plugin_auth']['stay_active'] = rex_request("stay_active","int");
	$REX['ADDON']['community']['plugin_auth']['article_login_ok'] = rex_request("article_login_ok","int");;
	$REX['ADDON']['community']['plugin_auth']['article_login_failed'] = rex_request("article_login_failed","int");;
	$REX['ADDON']['community']['plugin_auth']['article_logout'] = rex_request("article_logout","int");;
	$REX['ADDON']['community']['plugin_auth']['article_withoutperm'] = rex_request("article_withoutperm","int");;

	$REX['ADDON']['community']['plugin_auth']['login_field'] = stripslashes(str_replace('"','',rex_request("login_field","string")));
	if(!array_key_exists($REX['ADDON']['community']['plugin_auth']['login_field'],$xform_user_fields)) {
		$REX['ADDON']['community']['plugin_auth']['login_field'] = "login";
	}

	$config_file = $REX['INCLUDE_PATH'].'/addons/community/plugins/auth/config.inc.php';

	$content = '
$REX[\'ADDON\'][\'community\'][\'plugin_auth\'][\'auth_active\'] = "'.$REX['ADDON']['community']['plugin_auth']['auth_active'].'";
$REX[\'ADDON\'][\'community\'][\'plugin_auth\'][\'stay_active\'] = "'.$REX['ADDON']['community']['plugin_auth']['stay_active'].'";
$REX[\'ADDON\'][\'community\'][\'plugin_auth\'][\'article_login_ok\'] = '.$REX['ADDON']['community']['plugin_auth']['article_login_ok'].';
$REX[\'ADDON\'][\'community\'][\'plugin_auth\'][\'article_login_failed\'] = '.$REX['ADDON']['community']['plugin_auth']['article_login_failed'].';
$REX[\'ADDON\'][\'community\'][\'plugin_auth\'][\'article_logout\'] = '.$REX['ADDON']['community']['plugin_auth']['article_logout'].';
$REX[\'ADDON\'][\'community\'][\'plugin_auth\'][\'article_withoutperm\'] = '.$REX['ADDON']['community']['plugin_auth']['article_withoutperm'].';
$REX[\'ADDON\'][\'community\'][\'plugin_auth\'][\'login_field\'] = "'.$REX['ADDON']['community']['plugin_auth']['login_field'].'";
';

	if(rex_replace_dynamic_contents($config_file, $content) !== false)
		echo rex_info('Daten wurden aktualisiert');
	else
		echo rex_warning('Fehler beim Schreiben der Configdatei '.$config_file);

	if(!is_writable($config_file))
	  echo rex_warning($I18N->msg('imanager_config_not_writable', $config_file));

}elseif(rex_request("func","string")=="add_module")
{
	$module = rex_request("module","string");
	if(array_key_exists($module,$modules))
	{
		$module = $modules[$module];

		$in = rex_get_file_contents($REX["INCLUDE_PATH"]."/addons/community/plugins/auth/modules/module_".$module["key"].".in.inc");
		$out = rex_get_file_contents($REX["INCLUDE_PATH"]."/addons/community/plugins/auth/modules/module_".$module["key"].".out.inc");
	
		$mi = rex_sql::factory();
		// $mi->debugsql = 1;
		$mi->setTable("rex_module");
		$mi->setValue("eingabe",addslashes($in));
		$mi->setValue("ausgabe",addslashes($out));
	
		if (rex_request("module_id","string") != "")
		{
			$module_id = rex_request("module_id","int");
			$mi->setWhere('id="'.$module_id.'"');
			$mi->update();
			echo rex_info($I18N->msg("com_module_updated",$module["name"]));
	
		}else
		{
			$mi->setValue("name",$module["name"]);
			$mi->insert();
			$module_id = (int) $mi->getLastId();
			echo rex_info($I18N->msg("com_module_installed",$module["name"]));
			
		}
		$info = rex_generateAll();
	}

}

$sel_userfields = new rex_select();
$sel_userfields->setName("login_field");
$sel_userfields->setSize(1);
foreach($xform_user_fields as $k => $xf) {
	$sel_userfields->addOption($k,$k);
}
$sel_userfields->setSelected($REX['ADDON']['community']['plugin_auth']['login_field']);

echo '
	<div class="rex-form" id="rex-form-system-setup">
  	<form action="index.php" method="post">
    	<input type="hidden" name="page" value="community" />
    	<input type="hidden" name="subpage" value="plugin.auth" />
    	<input type="hidden" name="func" value="update" />
		
			<div class="rex-area-col-2">
				<div class="rex-area-col-a">
	
					<h3 class="rex-hl2">'.$I18N->msg("description").'</h3>
	
					<div class="rex-area-content">

<p class="rex-tx1">Bei der Installation von "Auth" wurde 1 Feld in der Metainfo hinzugefügt, welches
direkt in den Metadaten eines jeden Artikels vorhanden sind.</p>

<h4 class="rex-hl3">1) MetaInfo: Zugriffsrechte<br />ob man eingeloggt sein muss oder nicht. [art_com_permtype]</h4>

<p class="rex-tx1">Um sich einzuloggen muss ein Formular erstellt werden, welches diese Feldnamen
hat (<b>rex_com_auth_name, rex_com_auth_psw</b>). Werden diese übergeben (als einfach abgeschickt und an irgendeinen Artikel geschickt) wird automatisch eine 
Authentifizierung durchgeführt und auf die entsprechenden Artikel verwiesen. Zusätzlich muss der User mindestens auf status=1 stehen,
damit ein erfolgreicher Login möglich ist</p>

<p class="rex-tx1">Will man sich nun wieder ausloggen, kann jeder Link verwendet werden, welcher auf einen REDAXO-Artikel
verweist und man den Parameter und Wert <b>?rex_com_auth_logout=1</b> mitgibt.</p>

<p class="rex-tx1">Ist man erfolgreich eingeloggt, hat man über PHP das Object <b>$REX[\'COM_USER\']</b> zur Verfügung. Über 
<b>$REX[\'COM_USER\']->getValue(\'name\'), wie \'id\' etc,</b> kann man die Werte eines Users auslesen. Ist man nicht
eingeloggt, dann ist das <b>$REX[\'COM_USER\']</b>-Objekt nicht gesetzt. Formulare um das Profil
zu pflegen, die Registrierung durchzuführen oder ähnliches, wird nicht über das Auth-Plugin geloest. <br /></p>

<p class="rex-tx1">Um die Authentifizierung in der Navigation zu nutzen, sprich, dafür zu Sorgen, dass nur die richtigen Navigationspunkte auftauchen,
am besten die <b>rex_navigation</b>-Funktion von REDAXO verwenden. Wenn man eigene Navigationen gebaut hat, dann kann man dies prüfen indem man das entsprechende Artikel-Objekt 
an die Funktion übergibt <b>rex_com_auth::checkperm(&$obj (OOArticle-Objekt))</b></p>

<p class="rex-tx1">Angemeldet bleiben lässt auch aktivieren, wobei hier beim Formular nur die entsprechende Checkbox hinzugefügrt werden muss. Weiterhin ist/muss in der 
Userverwaltung das Feld <b>session_key</b> vorhanden sein, in welchem die entsprechende Session gespeichert wird, welche dann in den lokalen Cookies der Browser
gespeichert werden.</p>

<p class="rex-tx1">Über <b>rex_com_auth_jump</b> kann man übergeben, wohin man nach einem erfolgreichen Login springen soll.</p>
		
					</div>
				</div>
			
				<div class="rex-area-col-b">
					
					<h3 class="rex-hl2">'.$I18N->msg("com_auth_settings").'</h3>
					
					<div class="rex-area-content">
					
						<fieldset class="rex-form-col-1">
							<legend>'.$I18N->msg("com_status").'</legend>
							
							<div class="rex-form-wrapper">
							
								<div class="rex-form-row">
									<p class="rex-form-col-a rex-form-checkbox">
										<label for="rex-form-auth">Authentifizierung aktiviert</label>
										<input class="rex-form-text" type="checkbox" id="rex-form-auth" name="auth_active" value="1" ';
								if($REX['ADDON']['community']['plugin_auth']['auth_active']=="1") echo 'checked="checked"';
								echo ' />
									</p>
								</div>

								<div class="rex-form-row">
									<p class="rex-form-col-a rex-form-checkbox">
										<label for="rex-form-stay">"Angemeldet bleiben" aktiviert</label>
										<input class="rex-form-text" type="checkbox" id="rex-form-stay" name="stay_active" value="1" ';
								if($REX['ADDON']['community']['plugin_auth']['stay_active']=="1") echo 'checked="checked"';
								echo ' />
									</p>
								</div>

							</div>
						</fieldset>


						<fieldset class="rex-form-col-1">
							<legend>'.$I18N->msg("com_forwarder").'</legend>
							
							<div class="rex-form-wrapper">

								<div class="rex-form-row">
									<p class="rex-form-col-a rex-form-widget">
										<label for="rex-form-article_login_ok">'.$I18N->msg("com_auth_info_id_jump_ok").'</label>
										'. rex_var_link::_getLinkButton('article_login_ok', 1, stripslashes($REX['ADDON']['community']['plugin_auth']['article_login_ok'])) .'
									</p>
								</div>
							
								<div class="rex-form-row">
									<p class="rex-form-col-a rex-form-widget">
										<label for="rex-form-article_login_failed">'.$I18N->msg("com_auth_info_id_jump_not_ok").'</label>
                    					'. rex_var_link::_getLinkButton('article_login_failed', 2, stripslashes($REX['ADDON']['community']['plugin_auth']['article_login_failed'])) .'
									</p>
								</div>
								
								<div class="rex-form-row">
									<p class="rex-form-col-a rex-form-widget">
										<label for="rex-form-article_logout">'.$I18N->msg("com_auth_info_id_jump_logout").'</label>
                    					'. rex_var_link::_getLinkButton('article_logout', 3, stripslashes($REX['ADDON']['community']['plugin_auth']['article_logout'])) .'
									</p>
								</div>

								<div class="rex-form-row">
									<p class="rex-form-col-a rex-form-widget">
										<label for="rex-form-article_logout">'.$I18N->msg("com_auth_info_id_jump_denied").'</label>
                    					'. rex_var_link::_getLinkButton('article_withoutperm', 4, stripslashes($REX['ADDON']['community']['plugin_auth']['article_withoutperm'])) .'
									</p>
								</div>

								<div class="rex-form-row">
									<p class="rex-form-col-a rex-form-select">
										<label for="rex-form-default-template-id">'.$I18N->msg("com_auth_login_field").'</label>
											'.$sel_userfields->get().'
									</p>
								</div>
							
								<div class="rex-form-row">
									<p class="rex-form-col-a rex-form-submit">
										<input type="submit" class="rex-form-submit" name="sendit" value="'.$I18N->msg("specials_update").'"'. rex_accesskey($I18N->msg('specials_update'), $REX['ACKEY']['SAVE']) .' />
									</p>
								</div>

						</fieldset>
					</div> <!-- Ende rex-area-content //-->';

echo '					
					<h3 class="rex-hl2">'.$I18N->msg("setup").'</h3>
					<div class="rex-area-content">
					';

foreach($modules as $module)
{
	$gm = rex_sql::factory();
	$gm->setQuery('select * from rex_module where ausgabe LIKE "%'.mysql_real_escape_string($module["search"]).'%"');

	$link = 'index.php?page=community&subpage=plugin.auth&func=add_module&module='.urlencode($module["key"]);
	if($gm->getRows() == 1) {
		echo '* <a href="'.$link.'&module_id='.$gm->getValue("id").'">'.$I18N->msg("com_auth_update_module",$module["name"]).'</a><br />';
	}else
	{
		echo '* <a href="'.$link.'">'.$I18N->msg("com_auth_install_module",$module["name"]).'</a><br />';
	}

}
			
					
					
					
					
					
echo '					
					</div>
					
					
				</div> <!-- Ende rex-area-col-b //-->
			</div> <!-- Ende rex-area-col-2 //-->
			
		</form>
	</div>
  ';


