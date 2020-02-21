# Passwort zurücksetzen

## Passwort-Zurücksetzen-Formular

Da man auswählen kann, ob man ein Login verwenden möchte, oder die E-Mail als Login genutzt wird, muss man das untenstehende Formular entsprechend anpassen. Im Beispiel geht es von der E-Mail als Authentifizierungsfeld aus. Die Seite muss öffentlich erreichbar sein und kann z.B. im Login-Formular verlinkt werden.

```txt
generate_key|activation_key

text|email|E-Mail:|

captcha|Bitte geben Sie den entsprechenden Sicherheitscode ein. Sollten Sie den Code nicht lesen können klicken Sie bitte auf die Grafik, um einen neuen Code zu generieren.|Sie haben den Sicherheitscode falsch eingegeben.

validate|email|email|Bitte geben Sie die E-Mail ein.
validate|empty|email|Bitte geben Sie Ihre E-Mail ein.
validate|in_table|email|rex_ycom_user|email|Für die angegebene E-Mail-Adresse existiert kein Nutzer.|

action|db_query|update rex_ycom_user set activation_key = ? where email = ?|activation_key,email
action|tpl2email|resetpassword_de|email|
```

## E-Mail-Template `resetpassword_de` für „Passwort zurücksetzen” anlegen

```html
<!--
Bitte bei (888) die ID des Artikels für die "E-Mail-Bestätigungsseite" eintragen
-->
<p>Bitte klicken Sie diesen Link, um das Passwort zurück zu setzen:</p>
<p><a href="<?= trim(rex::getServer(),'/') . rex_getUrl(888) ?>?rex_ycom_activation_key=REX_YFORM_DATA[field=activation_key]&rex_ycom_id=REX_YFORM_DATA[field=email]"><?= trim(rex::getServer(),'/') . rex_getUrl(888) ?>?rex_ycom_activation_key=REX_YFORM_DATA[field=activation_key]&rex_ycom_id=REX_YFORM_DATA[field=email]</a></p>

```

Die Seite zur Bestätigung der `resetpassword_de` E-Mail muss öffentlich erreichbar sein, damit der User geprüft und eingeloggt werden kann. Hier bietet es sich an in der `action|html` noch einen Link zur **Passwort ändern** Seite zu setzen.

## E-Mail-Bestätigungsseite erstellen

```txt
objparams|submit_btn_show|0
objparams|send|1
objparams|csrf_protection|0

validate|ycom_auth_login|activation_key=rex_ycom_activation_key,email=rex_ycom_id|status=2|Zugang wurde bereits bestätigt oder ist schon fehlgeschlagen|status

action|ycom_auth_db|update
action|html|<b>Sie sind eingeloggt. Das Passwort kann nun geändert werden.</b>
```

Der `status=2` muss hier 2 sein, da es sich in der Regel um einen bereits *aktiven Account* handelt, der ein zurücksetzen des Passworts anfordert.
