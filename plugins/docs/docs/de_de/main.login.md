# Login

1. In der Struktur einen Artikel `Login` erstellen
2. Im Artikel `Login` den YForm Formbuilder hinzufügen und folgende Formulardefinition eintragen:

```plaintext
validate|ycom_auth|login|psw|stayfield|warning_message_enterloginpsw|warning_message_login_failed
text|login|Benutzername|
password|psw|Passwort
checkbox|stayfield|eingeloggt bleiben
ycom_auth_returnto|returnTo|
```

3. Im REDAXO-Backend unter `YCom` > `Einstellungen` den Artikel unter `Login` verknüpfen.
4. Den Artikel für den Nutzer zugänglich verlinken, z.B. in der Navigation oder im Header.

Nun können sich Nutzer im Frontend einloggen.

**Erklärung für `ycom_auth_returnto`**

Dieses Feld sorgt dafür, dass man entsprechend weitergeleitet wird.  Im Normalfall wird man entsprechend der Einstellung in der YCom auf einen bestimmten Artikel weitergeleitet. Aber es könnte auch sein, dass man auf einen geschützten Artikel zugegriffen hat, auf den man nach dem erfolgreichen Login hin geleitet wird.

Beispiel:

```plaintext
ycom_auth_returnto|returnTo|[Liste Domains, kommasepariert, für Freigabe https://domain1.de, https://domain2.de ]|[oder feste URL auf die IMMER geleitet wird]
```

[optional] Weiterhin könnte man auch von Extern kommen und wieder zurückgeleitet werden. Dazu kann man eine Liste von Domains anlegen, welche man für Weiterleitungen freigibt.

[optional] Oder man will eine feste Weiterleitung einstellen, auf die man immer nach einem erfolgreichen Login geleitet wird.
