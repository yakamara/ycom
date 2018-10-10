# Passwort ändern


### Passwort ändern-Formular



```
ycom_auth_password|password|Ihr Passwort:*|{"length":{"min":6},"letter":{"min":1},"lowercase":{"min":0},"uppercase":{"min":0},"digit":{"min":1},"symbol":{"min":0}}|Das Passwort muss mindestens 6 Zeichen lang sein und mindestens eine Ziffer enthalten
password|password_2|Passwort wiederholen:||no_db
validate|empty|password|Bitte geben Sie ein Passwort ein.
validate|compare|password|password_2|!=|Bitte geben Sie zweimal das gleiche Passwort ein
action|showtext|<div class="alert alert-success">Ihre Daten wurden aktualisiert. Das neue Passwort ist ab sofort aktiv.</div>|||1
action|ycom_auth_db
```