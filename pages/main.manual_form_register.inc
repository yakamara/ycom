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
