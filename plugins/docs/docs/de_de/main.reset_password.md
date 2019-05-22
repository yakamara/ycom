# Passwort zurücksetzen 


### Passwort zurücksetzen- Formular

Da man auswählen kann, ob man ein Login verwenden möchte, oder die E-mail als Login genutzt wird, muss man das untenstehende Formular entsprechend anpassen. Im Beispiel geht es von der E-Mail als Authentifizierungsfeld aus.

```
generate_key|activation_key

text|email|E-Mail:|

captcha|Bitte geben Sie den entsprechenden Sicherheitscode ein. Sollten Sie den Code nicht lesen können klicken Sie bitte auf die Grafik, um einen neuen Code zu generieren.|Sie haben den Sicherheitscode falsch eingegeben.

validate|email|email|Bitte geben Sie die E-Mail ein.
validate|empty|email|Bitte geben Sie Ihre E-Mail ein.
validate|in_table|email|rex_ycom_user|email|Für die angegebene E-Mail-Adresse existiert kein Nutzer.|

action|db_query|update rex_ycom_user set activation_key = ? where email = ?|activation_key,email
action|tpl2email|resetpassword_de|email|
```


### E-Mail-Template `resetpassword_de` für Passwort zurücksetzen anlegen

```
<!--
Bitte bei (888) die Id des Artikels für die Seite "Passwort zurücksetzen" eintragen
-->
<p>Bitte klicken Sie diesen Link, um das Passwort zurück zu setzen:</p>
<p><a href="<?= trim(rex::getServer(),'/') . rex_getUrl(888) ?>?rex_ycom_activation_key=REX_YFORM_DATA[field=activation_key]&rex_ycom_id=REX_YFORM_DATA[field=email]"><?= trim(rex::getServer(),'/') . rex_getUrl(888) ?>?rex_ycom_activation_key=REX_YFORM_DATA[field=activation_key]&rex_ycom_id=REX_YFORM_DATA[field=email]</a></p>

```


### E-Mail Bestätigungsseite erstellen

```
objparams|submit_btn_show|0
objparams|send|1
objparams|csrf_protection|0

validate|ycom_auth_login|activation_key=rex_ycom_activation_key,email=rex_ycom_id|status=1|Zugang wurde bereits bestätigt oder ist schon fehlgeschlagen|status

action|ycom_auth_db|update
action|html|<b>Sie sind eingeloggt. Das Passwort kann nun geändert werden.</b>
```
