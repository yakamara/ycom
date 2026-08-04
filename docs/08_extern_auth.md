# Externe Authentifizierung

> **REDAXO 6:** SAML und OAuth2 sind portiert, **CAS nicht**. Die Konfigurationsdateien liegen
> unverändert unter `var/data/addons/ycom/{saml,oauth2}.php`, die Feldnamen `ycom_auth_saml` und
> `ycom_auth_oauth2` sind gleich geblieben. Neu:
>
> - `onelogin/php-saml` ist auf `^4.3` aktualisiert (v3 ist abgekündigt).
> - Der SAML-RelayState läuft durch die Domain-Allowlist von `Auth::getReturnTo()`.
> - Die IdP-Metadaten werden mit TLS-Zertifikatsprüfung geholt (`idp.metadataValidatePeer`).
> - Der OAuth2-State wird mit `hash_equals()` geprüft und nur einmal akzeptiert.
> - Alle Provider-URLs werden absolut gebildet.
>
> Details in der README des AddOns.


## SAML-Authentifizierung

Mit der SAML-Authentifizierung kann man sich über einen externen Identityprovider in der YCOM registrieren und einloggen.
Dazu muss dieser Provider inkl. Metadaten entsprechend vorbereitet sein.

### Einrichtung

Bei der Installation legt ycom eine `saml.php` unter `var/data/addons/ycom/` ab. Diesen muss man entsprechend anpassen. Die Identityprovider information müssen dort eingerichtet sein.

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
\Redaxo\Core\ExtensionPoint\Extension::register('YCOM_AUTH_SAML_MATCHING', function (rex_extension_point $ep) {

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

## Allgemeines

### Programmatisches Einloggen an der Authentifizierung vorbei

Hat man einen eigenen Authentifizierungsmechanismus implementiert, so genügt es, den gewünschten YCom-User über dessen ID einzuloggen:

```php
$user = \Yakamara\YCom\Auth\Auth::loginWithParams(['login' => $user_id])
```

### Loginseite

Sofern man die externe Authentifikation nicht nutzt, wird die Loginseite meistens so eingestellt, dass nur nicht eingeloggte User diese sehen können. Das ist hier nicht zu empfehlen, da man sich nicht einloggen kann, wenn man über den IdentityProvider zur REDAXO Community kommt und bereits eingeloggt ist. Deswegen sollte die Loginseite verfühgbar, aber nicht sichtbar in der Navigation sein, wenn man eingeloggt ist.

