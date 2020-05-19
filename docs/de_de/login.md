# Login & Logout

## Login

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

## Logout 

### Per YForm-Formbuilder

1. In der Struktur einen Artikel `Logout` erstellen
2. Im Artikel `Logout` den YForm Formbuilder hinzufügen und folgende Formulardefinition eintragen:

```plaintext
ycom_auth_logout|label|
```

3. Im REDAXO-Backend unter `YCom` > `Einstellungen` den Artikel unter `Logout` verknüpfen.
4. Den Artikel für den Nutzer zugänglich verlinken, z.B. in der Navigation oder im Header.

Nun können sich Nutzer nach einem Login ausloggen und werden dann weitergeleitet.

### Ohne YForm-Formbuilder

Alternativ kann ein Logout mithilfe der Funktion `rex_ycom_auth::clearUserSession()` ausgeführt werden. 

## SAML-Authentifizierung

Mit der SAML-Authentifizierung kann man sich über einen externen Identityprovider in der YCOM registrieren und einloggen.
Dazu muss dieser Provider inkl. Metadaten entsprechend vorbereitet sein.

### Einrichtung

Bei der Installation des Auth-Plugins wurde eine saml.php in den Data-Ordner des YCom-AddOns gelegt. Diesen muss man entsprechend anpassen. Die Identityprovider information müssen dort eingerichtet sein.

Damit die Authentifizierung funktioniert, muss im Loginformular von YCOM ein Feld erweitert werden und es in der saml.php ergänzt werden.

```plaintext
ycom_auth_saml|samllabel|error_msg|[allowed returnTo domains: DomainA,DomainB]|[default Userdata as Json{"ycom_groups": 3, "termsofuse_accepted": 1}]
```

Mit diesem Feld erweitert man das Login um einen Loginbutton, der zum entsprechenden Authentifizierung führt. Wenn man diesen in der Loginmaske anklickt, wird diese Authentifizierung gestartet und eventuelle fehlende oder falsche Informationen werden angezeigt.

**error_msg** ist die entsprechende Fehlermeldung die auttaucht, wenn die SAML Authentifizierung fehlschlägt
**allowed returnTo domains** Optional: sollte man returnTo verwenden um eine Weiterleitung zu einer bestimmten Seite zu bekommen, kann man diese Weiterleitung filter, so dass nicht unerlaubte Domains genutzt werden können
**default Userdata as Json** Optional: Will man bestimmte Userdaten eines Users bei der Anmeldung über SAML festlegen, so kann man dies hier über JSON festlegen. Einfach die Feldnamen der Usertabelle dazu nutzen

> **Technische Erläuterung:** Sobald vom User der SAML-Auth-Login-Button geklickt wurde, wird man anhand der saml.php-Settings zum Identity Provider geschickt, welche den User eigenständig authentifiziert und dem User einen Token mit entsprechenden Usermetadaten zuweist.

> Diese werden von YCom genutzt und der User wird, wenn nicht vorhanden, angelegt und den eventuell individuell festgelegten Userattributen angelegt, oder die Daten werden aktualisiert.

> Danach ist der User auch in YCom authentifiziert und eine weiteres Login ist nicht nötig. Sofern eine Session weiterhin vorhanden ist, wird dieser User nur bei Ablauf der Session oder nach einem Logout wieder über dem Identityprovider authentifiziert.
