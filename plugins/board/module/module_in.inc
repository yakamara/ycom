<?php

// module:com_board_basic_in

?>

Threads pro Seite:<br>
<input type="text" name="VALUE[1]" value="REX_VALUE[id=1 ifempty=10]"/><br><br>

Beiträge pro Seite:<br>
<input type="text" name="VALUE[2]" value="REX_VALUE[id=2 ifempty=10]"/><br><br>

E-Mail-Template für Benachrichtigungen:<br>
<?php
$select = new rex_select();
$select->setName('VALUE[3]');
$select->setSize(1);
$select->addOption('–', 0);
$select->addSqlOptions('SELECT name, name FROM rex_xform_email_template ORDER BY name');
$select->setSelected('REX_VALUE[3]');
$select->show();
?>
