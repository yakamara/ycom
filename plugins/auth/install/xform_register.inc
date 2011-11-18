generate_key|activation_key
hidden|status|0

fieldset|Login-Daten:

text|email|E-Mail:*|
text|firstname|Vorname
text|name|Nachname

password|password|Ihr persönliches Passwort:*|
password|password_2|Ihr persönliches Passwort bestätigen:*||no_db

mailto|email

checkbox|newsletter|Newsletter

html|<div class="split"></div>

html|<p class="form-required">* Pflichtfelder</p>

captcha|Bitte geben Sie den entsprechenden Sicherheitscode ein|Falsche Eingabe des Codes

validate|email|email|Bitte geben Sie die E-Mail ein.
validate|unique|email|Diese E-Mail existiert schon|rex_com_user
validate|empty|email|Bitte geben Sie Ihre e-Mail ein.
validate|empty|password|Bitte geben Sie ein Passwort ein.
validate|compare|password|password_2|Bitte geben Sie zweimal das gleiche Passwort ein

action|db|rex_com_user
action|db2email|register_de|email
