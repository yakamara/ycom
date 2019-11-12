# Login 

1. In der Struktur einen Artikel `Login` erstellen
2. Im Artikel `Login` den YForm Formbuilder hinzufügen und folgende Formulardefinition eintragen:

```
validate|ycom_auth|login|psw|stayfield|warning_message_enterloginpsw|warning_message_login_failed

text|login|Benutzername
password|psw|Passwort
ycom_auth_returnto|returnTo|
```


3. Im REDAXO-Backend unter `YCom` > `Einstellungen` den Artikel unter `Login` verknüpfen.
4. Den Artikel für den Nutzer zugänglich verlinken, z.B. in der Navigation oder im Header.

Nun können sich Nutzer im Frontend einloggen.
