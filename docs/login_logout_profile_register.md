# Login/Logout/Profile/Registrierung

## Login

1. In der Struktur einen Artikel `Login` erstellen
2. Im Artikel `Login` den YForm Formbuilder hinzufügen und folgende Formulardefinition eintragen:

```php
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

```php
ycom_auth_returnto|returnTo|[Liste Domains, kommasepariert, für Freigabe https://domain1.de, https://domain2.de ]|[oder feste URL auf die IMMER geleitet wird]
```

[optional] Weiterhin könnte man auch von Extern kommen und wieder zurückgeleitet werden. Dazu kann man eine Liste von Domains anlegen, welche man für Weiterleitungen freigibt.

[optional] Oder man will eine feste Weiterleitung einstellen, auf die man immer nach einem erfolgreichen Login geleitet wird.

## Logout

### Per YForm-Formbuilder

1. In der Struktur einen Artikel `Logout` erstellen
2. Im Artikel `Logout` den YForm Formbuilder hinzufügen und folgende Formulardefinition eintragen:

```php
ycom_auth_logout|label|
```

3. Im REDAXO-Backend unter `YCom` > `Einstellungen` den Artikel unter `Logout` verknüpfen.
4. Den Artikel für den Nutzer zugänglich verlinken, z.B. in der Navigation oder im Header.

Nun können sich Nutzer nach einem Login ausloggen und werden dann weitergeleitet.

### Ohne YForm-Formbuilder
Alternativ kann ein Logout mithilfe der Funktion `rex_ycom_auth::clearUserSession()` ausgeführt werden. 

## Nutzer und Profile

Jeder YCom-Nutzer hat ein Profil, das unter `YCom` > `User` eingesehen werden kann. Dort können Profilfelder beliebig erweitert werden.

> **Achtung:** Bitte bestehende Felder nicht ändern oder löschen.

### Profil-Formular

Eingeloggte Nutzer können auf Wunsch ihr Profil bearbeiten.

1. In der Struktur eine Kategorie / einen Artikel `Profil` erstellen.
2. Im Artikel `Profil` den YForm Formbuilder hinzufügen und folgende Formulardefinition eintragen:

```php
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

3. Den Artikel für den Nutzer zugänglich verlinken, z.B. in der Navigation oder im Header.

> Hinweis: Über den Formularcode-Generator in YForm kann man die Pipe-Schreibweise des Formulars auch für andere Felder des Nutzers abrufen.

> Hinweis: Wird als Login nicht die E-Mail-Adresse herangezogen, sondern der Nutzername, lautet die 3. Zeile nicht `showvalue|email|E-Mail / Login:`, sondern `showvalue|login|E-Mail / Login:`.

## Registrierung

### Registrierungs-Formular

```php
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
validate|password_policy|password|Passwort muss mindestens 8 Zeichen lang sein und sowohl Gross- als auch Kleinbuchstaben enthalten|
validate|compare|password|password_2||Bitte geben Sie zweimal das gleiche Passwort ein

# email als Login verwenden
action|copy_value|email|login
action|db|rex_ycom_user
action|tpl2email|access_request_de|email|
```

> Hinweis: Sollen keine "Terms of Use" abgefragt werden, dann diese Zeile:
> 
```php
checkbox|termsofuse_accepted|Ich habe die Nutzungsbedingungen akzeptiert.|0|0|
```

ersetzt werden durch:

```php
hidden|termsofuse_accepted|1
```

#### E-Mail-Template `access_request_de` für die Bestätigung erstellen

Diese E-Mail fordert den Nutzer dazu auf, die Anmeldung zu bestätigen. Der endgültige Link sieht bspw. aus wie folgt: <code>https://www.redaxo.org/anmeldung/bestaetigen/?rex_ycom_activation_key=ACTIVATION_KEY&rex_ycom_id=YCOM_LOGIN</code>

```php
<?php
$article_id = 999; // Hier die Artikel-ID der Bestätigungsseite eintragen

$article_url = rex_getUrl($article_id,'',['rex_ycom_activation_key'=>'REX_YFORM_DATA[field=activation_key]','rex_ycom_id'=>'REX_YFORM_DATA[field=email]','&']);
$full_url = trim(rex::getServer(),'/').trim($article_url,'.');
?>
<p>Bitte klicken Sie diesen Link, um die Anmeldung zu bestätigen:</p>
<p><a href="<?=$full_url;?>"><?=$full_url;?></a></p>
```

#### Registrierungsbestätigung

Der Artikel für die Registrierungsbestätigung muss öffentlich zugänglich sein und unter dem Link erreichbar sein, der im o.g. E-Mail-Template angegeben wurde, z.B.: <code>https://www.redaxo.org/anmeldung/bestaetigen/?rex_ycom_activation_key=ACTIVATION_KEY&rex_ycom_id=YCOM_LOGIN</code>

```php
hidden|status|1
objparams|submit_btn_show|0
objparams|send|1
objparams|csrf_protection|0

validate|ycom_auth_login|activation_key=rex_ycom_activation_key,email=rex_ycom_id|status=0|Zugang wurde bereits bestätigt oder ist schon fehlgeschlagen|status

action|ycom_auth_db|update
action|html|<b>Vielen Dank, Sie sind nun eingeloggt und haben Ihre E-Mail bestätigt</b>
```

> **Tipp:** Möchte man stattdessen den Nutzer nicht direkt einloggen, kann man die Logout-Action direkt im Anschluss ausführen: `ycom_auth_logout|label|`
