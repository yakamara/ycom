# Nutzer und Profile

## Registrierung

### Registrierungs-Formular

```plaintext
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

checkbox|termsofuse_accepted|Ich habe die Nutzungsbedingungen akzeptiert.|0|0|

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
```

> Hinweis: Sollen keine "Terms of Use" abgefragt werden, dann diese Zeile:
```
checkbox|termsofuse_accepted|Ich habe die Nutzungsbedingungen akzeptiert.|0|0|
```
...ersetzt werden durch:
```
hidden|termsofuse_accepted|1
```

### E-Mail-Template `access_request_de` für die Bestätigung erstellen

Diese E-Mail fordert den Nutzer dazu auf, die Anmeldung zu bestätigen. Der endgültige Link sieht bspw. aus wie folgt: <code>https://www.redaxo.org/anmeldung/bestaetigen/?rex_ycom_activation_key=ACTIVATION_KEY&rex_ycom_id=YCOM_LOGIN</code>

```
<?php
$article_id = 999; // Hier die Artikel-ID der Bestätigungsseite eintragen

$article_url = rex_getUrl($article_id,'',array('rex_ycom_activation_key'=>'REX_YFORM_DATA[field=activation_key]','rex_ycom_id'=>'REX_YFORM_DATA[field=email]'));
$full_url = trim(rex::getServer(),'/').trim($article_url,'.');
?>
<p>Bitte klicken Sie diesen Link, um die Anmeldung zu bestätigen:</p>
<p><a href="<?=$full_url;?>"><?=$full_url;?></a></p>
```

### Registrierungsbestätigung

Der Artikel für die Registrierungsbestätigung muss öffentlich zugänglich sein und unter dem Link erreichbar sein, der im o.g. E-Mail-Template angegeben wurde, z.B.: <code>https://www.redaxo.org/anmeldung/bestaetigen/?rex_ycom_activation_key=ACTIVATION_KEY&rex_ycom_id=YCOM_LOGIN</code>

```plaintext
hidden|status|1
objparams|submit_btn_show|0
objparams|send|1
objparams|csrf_protection|0

validate|ycom_auth_login|activation_key=rex_ycom_activation_key,email=rex_ycom_id|status=1|Zugang wurde bereits bestätigt oder ist schon fehlgeschlagen|status

action|ycom_auth_db|update
action|html|<b>Vielen Dank, Sie sind nun eingeloggt und haben Ihre E-Mail bestätigt</b>
```

> **Tipp:** Möchte man stattdessen den Nutzer nicht direkt einloggen, kann man die Logout-Action direkt im Anschluss ausführen: `ycom_auth_logout|label|`

## Profil bearbeiten

Jeder YCom-Nutzer hat ein Profil, das unter `YCom` > `User` eingesehen werden kann. Dort können Profilfelder beliebig erweitert werden.

> **Achtung:** Bitte bestehende Felder nicht ändern oder löschen.

### Profil-Formular

Eingeloggte Nutzer können auf Wunsch ihr Profil bearbeiten.

1. In der Struktur eine Kategorie / einen Artikel `Profil` erstellen.
2. Im Artikel `Profil` den YForm Formbuilder hinzufügen und folgende Formulardefinition eintragen:

```
ycom_auth_load_user|userinfo|email,firstname,name
objparams|form_showformafterupdate|1
showvalue|email|E-Mail / Login:

text|firstname|Vorname:
validate|empty|firstname|Bitte geben Sie Ihren Vornamen ein.
text|name|Nachname:
validate|empty|name|Bitte geben Sie Ihren Namen ein.

action|showtext|<div class="alert alert-success">Profildaten wurden aktualisiert</div>|||1
action|ycom_auth_db
```

3. Im REDAXO-Backend unter `YCom` > `Einstellungen` den Artikel unter `Profil ändern` verknüpfen.
4. Den Artikel für den Nutzer zugänglich verlinken, z.B. in der Navigation oder im Header.

> Hinweis: Über den Formularcode-Generator in YForm kann man die Pipe-Schreibweise des Formulars auch für andere Felder des Nutzers abrufen.

> Hinweis: Wird als Login nicht die E-Mail-Adresse herangezogen, sondern der Nutzername, lautet die 3. Zeile nicht `showvalue|email|E-Mail / Login:`, sondern `showvalue|login|E-Mail / Login:`.

## User und Gruppen auslesen

Da YCom auf YForm basiert, können auf Details von YCom-Nutzern und Gruppen auf Basis von `rex_sql` oder `YOrm` ausgelesen werden.

### YOrm-Variante (empfohlen)

Siehe auch
* YForm-Dokumentation, Kapitel "YOrm": https://github.com/yakamara/redaxo_yform_docs/blob/master/de_de/yorm.md

**Details des YCom-Nutzers auslesen**
```
$ycom_user = rex_ycom_auth::getUser();

# dump($ycom_user); // auskommentieren, um alle Eigenschaften des Objekts auszugeben

if($ycom_user) {
    echo $ycom_user->getValue('login');             // Benutzer-Login
    echo $ycom_user->getValue('firstname');         // Vorname
    echo $ycom_user->getValue('name');              // Nachname
    echo $ycom_user->getValue('last_action_time');  // Letzte Aktion auf der Seite
    echo $ycom_user->getValue('last_login_time');   // Letzter Login
    echo $ycom_user->getValue('ycom_groups');       // IDs der zugeordneten Gruppen
}
```

> Hinweis: Die Felder lassen sich über den Table Manager in YForm um eigene Felder erweitern.

**Details zur Gruppe eines YCom-Nutzers auslesen**

```php
$ycom_user = rex_ycom_auth::getUser();

if($ycom_user) {
        $ycom_group = $ycom_user->getRelatedDataset('ycom_groups');
        $ycom_group->getValue("name"); // Name der Gruppe

        if($ycom_user->isInGroup($group_id)) {
            // User ist in der Gruppe   
        } else {
            // User ist nicht in der Gruppe
        };
}
```

> Hinweis: Die Felder lassen sich über den Table Manager in YForm um eigene Felder erweitern.

**Gruppen-Berechtigung eines YCom-Nutzers auslesen:**

```php
$article_id = 42; // ID des Artikels
$ycom_user = rex_ycom_auth::getUser();
if($ycom_user->checkPerm($article_id)) {
    // User hat Berechtigung, den Artikel zu sehen  
} else {
    // User hat nicht die Berechtigung, den Artikel zu sehen
};
```
Weitere Methoden der rex_ycom_auth-Klasse: https://github.com/yakamara/redaxo_ycom/blob/master/plugins/auth/lib/ycom_auth.php

### SQL-Variante

Siehe auch:
* REDAXO API-Dokumentation: https://redaxo.org/api/master/class-rex_sql.html
* Datenbank-Queries in REDAXO 5: https://redaxo.org/doku/master/datenbank-queries

```
$ycom_user = rex_ycom_auth::getUser();

if($ycom_user) {
    $user = $rex_sql::factory()->getArray('SELECT id, name FROM rex_ycom_user WHERE id = :id', [$ycom_user->getId()]);
    # dump($user); // auskommentieren, um alle Schlüssel und Werte des Arrays auszugeben
}
```
