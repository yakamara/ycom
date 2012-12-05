<?php

$sql = new rex_sql();
$sql->setQuery('ALTER TABLE `' . $REX['TABLE_PREFIX'] . 'com_user` DROP `bounce_counter`');

$REX['ADDON']['install']['newsletter_bounce'] = false;

?>
