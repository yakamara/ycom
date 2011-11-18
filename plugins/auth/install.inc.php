<?php

/**
 * auth
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

$error = '';

// --- metainfo
$a = rex_sql::factory();
$a->setTable("rex_62_params");
$a->setValue("title","translate:com_permtype");
$a->setValue("name","art_com_permtype");
$a->setValue("prior","11");
$a->setValue("type","3");
$a->setValue("params","0:translate:com_perm_all|1:translate:com_perm_only_logged_in|2:translate:com_perm_only_not_logged_in");
$a->setValue("validate",NULL);
$a->addGlobalCreateFields();
$g = rex_sql::factory();
$g->setQuery('select * from rex_62_params where name="art_com_permtype"');
if ($g->getRows()==1) {
	$a->setWhere('name="art_com_permtype"');
	$a->update();

}else {
	$a->insert();
}
$g = new rex_sql;
$g->setQuery('show columns from rex_article like "art_com_permtype"');
if ($g->getRows()==0) {
	$a->setQuery("ALTER TABLE `rex_article` ADD `art_com_permtype` VARCHAR( 255 ) NOT NULL"); 
}

// ************************************************************** CACHE LOESCHEN

$info = rex_generateAll(); // quasi kill cache .. 

$REX['ADDON']['install']['auth'] = 1;
if($error != "") {

	$REX['ADDON']['install']['auth'] = 0;
	$REX['ADDON']['installmsg']['auth'] = $error;
	
}else
{


}

?>