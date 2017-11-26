ycom_auth_password|password|#placeholder:Neues Passwort|Neues Passwort*|{"length":{"min":8},"letter":{"min":0},"lowercase":{"min":0},"uppercase":{"min":0},"digit":{"min":0},"symbol":{"min":0}}|Passwort muss mind. 8 Zeichen lang sein|1|
password|password_2|Passwort wiederholen:||no_db
validate|empty|password|Bitte geben Sie ein Passwort ein.
validate|compare|password|password_2|!=|Bitte geben Sie zweimal das gleiche Passwort ein
action|showtext|<div class="alert alert-success">Ihre Daten wurden aktualisiert. Das neue Passwort ist ab sofort aktiv.</div>|||1
action|ycom_auth_db
