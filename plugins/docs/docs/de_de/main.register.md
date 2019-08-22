# Registrierung



### Registrierungs-Formular

```
generate_key|activation_key
hidden|status|0

fieldset|label|Login-Daten:

text|email|E-Mail:*|
text|firstname|Vorname:*
validate|empty|firstname|Bitte geben Sie Ihren Vornamen ein.

text|name|Nachname:*
validate|empty|name|Bitte geben Sie Ihren Namen ein.

ycom_auth_password|password|Ihr Passwort:*|{"length":{"min":6},"letter":{"min":1},"lowercase":{"min":0},"uppercase":{"min":0},"digit":{"min":1},"symbol":{"min":0}}|Das Passwort muss mindestens 6 Zeichen lang sein und mindestens eine Ziffer enthalten
password|password_2|Passwort bestätigen:*||no_db

checkbox|termsofuse_accepted|Ich habe die Nutzungsbedingungen akzeptiert.|0|0|

html|required|<p class="form-required">* Pflichtfelder</p>

captcha|Bitte geben Sie den entsprechenden Sicherheitscode ein. Sollten Sie den Code nicht lesen können klicken Sie bitte auf die Grafik, um einen neuen Code zu generieren.|Sie haben den Sicherheitscode falsch eingegeben.

validate|email|email|Bitte geben Sie die E-Mail ein.
validate|unique|email|Diese E-Mail existiert schon|rex_ycom_user
validate|empty|email|Bitte geben Sie Ihre e-Mail ein.
validate|empty|password|Bitte geben Sie ein Passwort ein.

validate|compare|password|password_2||Bitte geben Sie zweimal das gleiche Passwort ein

# email als Login verwenden
action|copy_value|email|login
action|db|rex_ycom_user
action|tpl2email|access_request_de|email|
```

> Hinweis: Sollen keine "Terms of Use" abgefragt werden, dann diese Zeile:
```
checkbox|termsofuse_accepted|Ich habe die Nutzungsbedingungen akzeptiert.|0|0|
```
...ersetzt werden durch:
```
hidden|termsofuse_accepted|1
```

### E-Mail-Template `access_request_de` für die Bestätigung erstellen

```
<?php
$article_id = 888; // wichtig: hier die Artikel-ID der Bestätigungsseite (register_proof Artikel) eintragen

$article_url = rex_getUrl($article_id,'',array('rex_ycom_activation_key'=>'REX_YFORM_DATA[field=activation_key]','rex_ycom_id'=>'REX_YFORM_DATA[field=email]'));
$full_url = trim(rex::getServer(),'/').trim($article_url,'.');
?>
<p>Bitte klicken Sie diesen Link, um die Anmeldung zu bestätigen:</p>
<p><a href="<?=$full_url;?>"><?=$full_url;?></a></p>
```

### Registrierungsbestätigung


/* http://redaxo/bestaetigung/?rex_ycom_activation_key=xxxx&rex_ycom_id=yyyy */

```
hidden|status|1
objparams|submit_btn_show|0
objparams|send|1
objparams|csrf_protection|0

validate|ycom_auth_login|activation_key=rex_ycom_activation_key,email=rex_ycom_id|status=0|Zugang wurde bereits bestätigt oder ist schon fehlgeschlagen|status

action|ycom_auth_db|update
action|html|<b>Vielen Dank, Sie sind nun eingeloggt und haben Ihre E-Mail bestätigt</b>
```

> **Tipp:** Möchte man stattdessen den Nutzer nicht direkt einloggen, kann man die Logout-Action direkt im Anschluss ausführen: `ycom_auth_form_logout|label|`
