<!--
http://redaxo/bestaetigung/?rex_ycom_activation_key=xxxx&rex_ycom_id=yyyy 
Bitte die Id (888) durch die Id des register_proof Artikels ersetzen
-->
<p>Bitte klicken Sie diesen Link, um die Anmeldung zu bestätigen:</p>
<p><a href="<?= trim(rex::getServer(),'/') . rex_getUrl(888) ?>?rex_ycom_activation_key=REX_YFORM_DATA[field=activation_key]&rex_ycom_id=REX_YFORM_DATA[field=email]"><?= trim(rex::getServer(),'/') . rex_getUrl(888) ?>?rex_ycom_activation_key=REX_YFORM_DATA[field=activation_key]&rex_ycom_id=REX_YFORM_DATA[field=email]</a></p>
