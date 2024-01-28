# Externe Authentifizierungen

## SAML-Authentifizierung

Mit der SAML-Authentifizierung kann man sich über einen externen Identityprovider in der YCOM registrieren und einloggen.
Dazu muss dieser Provider inkl. Metadaten entsprechend vorbereitet sein.

### Einrichtung

Bei der Installation des Auth-Plugins wurde eine saml.php in den Data-Ordner des YCom-AddOns gelegt. Diesen muss man entsprechend anpassen. Die Identityprovider information müssen dort eingerichtet sein.

Damit die Authentifizierung funktioniert, muss im Loginformular von YCOM ein Feld erweitert werden und es in der saml.php ergänzt werden.

```php
ycom_auth_saml|samllabel|error_msg|[allowed returnTo domains: DomainA,DomainB]|default Userdata as Json{"ycom_groups": 3, "termsofuse_accepted": 1}
```

Mit diesem Feld erweitert man das Login um einen Loginbutton, der zum entsprechenden Authentifizierung führt. Wenn man diesen in der Loginmaske anklickt, wird diese Authentifizierung gestartet und eventuelle fehlende oder falsche Informationen werden angezeigt.

**error_msg** ist die entsprechende Fehlermeldung die auttaucht, wenn die SAML Authentifizierung fehlschlägt
**allowed returnTo domains** Optional: sollte man returnTo verwenden um eine Weiterleitung zu einer bestimmten Seite zu bekommen, kann man diese Weiterleitung filter, so dass nicht unerlaubte Domains genutzt werden können
**default Userdata as Json** Optional: Will man bestimmte Userdaten eines Users bei der Anmeldung über SAML festlegen, so kann man dies hier über JSON festlegen. Einfach die Feldnamen der Usertabelle dazu nutzen

> **Technische Erläuterung:** Sobald vom User der SAML-Auth-Login-Button geklickt wurde, wird man anhand der saml.php-Settings zum Identity Provider geschickt, welche den User eigenständig authentifiziert und dem User einen Token mit entsprechenden Usermetadaten zuweist.

> Diese werden von YCom genutzt und der User wird, wenn nicht vorhanden, angelegt und den eventuell individuell festgelegten Userattributen angelegt, oder die Daten werden aktualisiert.

> Danach ist der User auch in YCom authentifiziert und eine weiteres Login ist nicht nötig. Sofern eine Session weiterhin vorhanden ist, wird dieser User nur bei Ablauf der Session oder nach einem Logout wieder über dem Identityprovider authentifiziert.

Sofern die automatische E-Mail Erkennung nicht klappt, oder/und man eigene Felder bei der Usergenerierung festlegen will, folgendes am besten in die `addons/project/boot.php` legen und anpassen.

```php
rex_extension::register('YCOM_AUTH_SAML_MATCHING', function (rex_extension_point $ep) {

    $data = $ep->getSubject();
    $params = $ep->getParams();
    $Userdata = $params['Userdata'];

    echo '<p>Hier auslesen welche Parameter übergeben werden und eventuell übernehmen/auswerten</p>';
    echo '<p>Danach diesen Block löschen</p>';
    echo '<p>Das hier ist bereits erkannt worden</p>';
    dump($data);
    echo '<p>Das hier kam über die SAML Schnittstelle</p>';
    dump($Userdata);

    exit;

    // z.B.
    $emailKey = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress';
    $givennameKey = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname';
    $surnameKey = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname';

    $data['email'] = @$Userdata[$emailKey][0];

    // Userdatensatz mit gewünschten Einstellungen anreichern.
    $data['termsofuse_accepted'] = 1;
    $data['login_tries'] = 0;

    return $data;

});
```

## CAS

## OAuth2

## OAuth2 mit twitch

Mit der OAuth2 Authentifizierung via twitch ist es möglich, sich mit einem twitch-Account in der YCOM zu registrieren und einzuloggen.
Dazu muss dieser Provider entsprechend vorbereitet sein.

### Einrichtung

Im ersten Schritt muss man eine App anlegen bei twitch. Hierfür einmal zu https://dev.twitch.tv/console/apps wechseln. Dort über den Button "Deine Anwendung registrieren" eine neue App erstellen - Kategorie "Website integration" auswählen.

Anschließend die App bearbeiten und die folgenden Einstellungen vornehmen:

- Name: Name der App
- OAuth Redirect URLs: https://your-url.com/maybe-a-subpage/?rex_ycom_auth_mode=oauth2_twitch&rex_ycom_auth_func=code
- Category: Website Integration

Anschließend die App speichern und die Client-ID kopieren.
Dann ein neues Secret erzeugen und dieses ebenfalls kopieren / sichern.

In den Ordner `redaxo/data/addons/ycom/` sollte bereits die Datei `oauth2_twitch.php` kopiert worden sein. Diese Datei muss nun entsprechend angepasst werden mit den kopierten Daten.

Damit die Authentifizierung funktioniert, muss im Loginformular von YCOM folgender String (angepasst auf die eigenen Bedürfnisse) eingefügt werden:

```php
ycom_auth_oauth2_twitch|twitch|error_msg|[allowed returnTo domains: DomainA,DomainB]|{"ycom_groups": 1, "termsofuse_accepted": 1}|direct_link 0,1
```

#### Scope

Zusätzlice Scopes lassen sich in der Datei `oauth2_twitch.php` als Array in der Variable `scopes` eintragen. Die Scopes müssen mit einem Komma getrennt werden. Weitere Scopes findet man hier: https://dev.twitch.tv/docs/authentication/scopes/ - z.B. `user:read:email` um die E-Mail-Adresse des Users zu erhalten (diese bringt der Provider aber bereits nativ mit).

## OAuth2 mit Google

Mit der OAuth2 Authentifizierung via Google ist es möglich, sich mit einem Google-Account in der YCOM zu registrieren und einzuloggen. Dazu muss dieser Provider entsprechend vorbereitet sein.

### Einrichtung

Im ersten Schritt muss man eine App anlegen bei Google. Hierfür einmal zu https://console.developers.google.com/ wechseln. Dort über den Button "Projekt auswählen" eine neues Projekt erstellen. Anschließend die App bearbeiten und die folgenden Einstellungen vornehmen:

- OAuth Zustimmungsbildschirm: Einstellungen vornehmen
-- Anwendungsname: Name der App
-- Nutzersupport-E-Mail: E-Mail-Adresse für Support
-- Anwendungslogo: Logo der App
-- Startseite der App: Deine Domain
-- Kontaktdaten des Entwicklers: E-Mail-Adresse für Support
-- Autorisierte Domains: Deine Domain (optional für GSuite Nutzer)
-- Bereiche:
--- ./auth/userinfo.email (E-Mail-Adresse des Users)
--- ./auth/userinfo.profile (Profilinformationen des Users)

Anschließend auf Anmeldedaten klicken und die folgenden Einstellungen vornehmen:
- Anmeldedaten erstellen: OAuth 2.0 Client IDs erstellen
-- Autorisierte JavaScript-Quellen: Deine Domain
-- Autorisierte Weiterleitungs-URIs: https://your-url.com/maybe-a-subpage/?rex_ycom_auth_mode=oauth2_google&rex_ycom_auth_func=code

Danach kann die Client ID und der Clientschlüssel kopiert oder als JSON Datei heruntergeladen werden.

In den Ordner `redaxo/data/addons/ycom/` sollte bereits die Datei `oauth2_google.php` kopiert worden sein. Diese Datei muss nun entsprechend angepasst werden mit den kopierten Daten.

Damit die Authentifizierung funktioniert, muss im Loginformular von YCOM folgender String (angepasst auf die eigenen Bedürfnisse) eingefügt werden:

```php
ycom_auth_oauth2_google|label|error_msg|[allowed returnTo domains: DomainA,DomainB]|default Userdata as Json{"ycom_groups": 3, "termsofuse_accepted": 1}|direct_link 0,1
```

#### GSuite / Google Workspace Nutzer
In der Datei `oauth2_google.php` kann die Variable `hostedDomain` mit der Domain des GSuite / Google Workspace Accounts befüllt werden. Damit wird die Anmeldung auf Nutzer mit dieser Domain beschränkt.

## OAuth2 mit GitHub

Mit der OAuth2 Authentifizierung via GitHub ist es möglich, sich mit einem GitHub-Account in der YCOM zu registrieren und einzuloggen. Dazu muss dieser Provider entsprechend vorbereitet sein.

### Einrichtung

Im ersten Schritt muss man eine App anlegen bei GitHub. Hierfür einmal zu https://github.com/settings/apps wechseln. Dort über den Button "New GitHub App" eine neue App erstellen. Anschließend die App bearbeiten und die folgenden Einstellungen vornehmen:

- Name: Name der App
- Callback-URL: https://your-url.com/maybe-a-subpage/?rex_ycom_auth_mode=oauth2_github&rex_ycom_auth_func=code
- Haken setzen bei "Request user authorization (OAuth) during installation"
- Webhook kann ausgemacht werden
- Account Permissions: Email adresses => "Read-only" und Profile=> "Read & write" auswählen

App speichern und die Client-ID kopieren.
Dann ein neues Secret erzeugen und dieses ebenfalls kopieren / sichern.

In den Ordner `redaxo/data/addons/ycom/` sollte bereits die Datei `oauth2_github.php` kopiert worden sein. Diese Datei muss nun entsprechend angepasst werden mit den kopierten Daten.

Damit die Authentifizierung funktioniert, muss im Loginformular von YCOM folgender String (angepasst auf die eigenen Bedürfnisse) eingefügt werden:

```php
ycom_auth_oauth2_github|label|error_msg|[allowed returnTo domains: DomainA,DomainB]|default Userdata as Json{"ycom_groups": 2, "termsofuse_accepted": 1}|direct_link 0,1
```


## Allgemeines

### Loginseite

Sofern man die externe Authentifikation nicht nutzt, wird die Loginseite meistens so eingestellt, dass nur nicht eingeloggte User diese sehen können. Das ist hier nicht zu empfehlen, da man sich nicht einloggen kann, wenn man über den IdentityProvider zur REDAXO Community kommt und bereits eingeloggt ist. Deswegen sollte die Loginseite verfühgbar, aber nicht sichtbar in der Navigation sein, wenn man eingeloggt ist.

