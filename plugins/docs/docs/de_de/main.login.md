# Login 

1. In der Struktur einen Artikel `Login` erstellen
2. Im Artikel `Login` den YForm Formbuilder hinzufügen und folgende Formulardefinition eintragen:

```
ycom_auth_form_info|label|Bitte einloggen|Benutzer wurde ausgeloggt|Login ist fehlgeschlagen|Benutzer wurde erfolgreich eingeloggt|
ycom_auth_form_login|label|Benutzername / E-Mail:
ycom_auth_form_password|label|Passwort:
ycom_auth_form_stayactive|auth|eingeloggt bleiben:|0
```

3. Im REDAXO-Backend unter `YCom` > `Einstellungen` den Artikel unter `Login` verknüpfen.
4. Den Artikel für den Nutzer zugänglich verlinken, z.B. in der Navigation oder im Header.

Nun können sich Nutzer im Frontend einloggen.