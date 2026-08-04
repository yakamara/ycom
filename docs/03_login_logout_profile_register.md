# Login/Logout/Profile/Registrierung

## Login

1. In der Struktur einen Artikel `Login` erstellen
2. Im Artikel `Login` den YForm Formbuilder hinzufügen und folgende Formulardefinition eintragen:

```text
validate|ycom_auth|login|password|stayfield|warning_message_enterloginpsw|warning_message_login_failed
text|login|Benutzername|||{"autocomplete":"username"}
password|password|Passwort|||{"autocomplete":"current-password"}
checkbox|stayfield|eingeloggt bleiben
ycom_auth_returnto|returnTo|
```

```php
<?php
$form = \Yakamara\YForm\YForm::factory();

$form->setValidateField('ycom_auth', ['login', 'password', null, 'warning_message_enterloginpsw', 'warning_message_login_failed']);

$form->setObjectparams('form_name', 'login_form');
$form->setObjectparams('form_action', \Redaxo\Core\Filesystem\Url::article());

$form->setValueField('text', ['login', 'Benutzername', '', '', '{"autocomplete":"username"}']);
$form->setValueField('password', ['password', 'Passwort', '', '', '{"autocomplete":"current-password"}']);

$form->setValidateField('empty', ['login', 'Bitte geben Sie Ihren Benutzernamen ein']);
$form->setValidateField('empty', ['password', 'Bitte geben Sie Ihr Passwort ein']);

$form->setValueField('ycom_auth_returnto', ['returnTo']);

echo $form->getForm();
```

3. Im REDAXO-Backend unter `YCom` > `Einstellungen` den Artikel unter `Login` verknüpfen.
4. Den Artikel für den Nutzer zugänglich verlinken, z.B. in der Navigation oder im Header.

Nun können sich Nutzer im Frontend einloggen.

**Erklärung für `ycom_auth_returnto`**

Dieses Feld sorgt dafür, dass man entsprechend weitergeleitet wird.  Im Normalfall wird man entsprechend der Einstellung in der YCom auf einen bestimmten Artikel weitergeleitet. Aber es könnte auch sein, dass man auf einen geschützten Artikel zugegriffen hat, auf den man nach dem erfolgreichen Login hin geleitet wird.

Beispiel:

```text
ycom_auth_returnto|returnTo|[Liste Domains, kommasepariert, für Freigabe https://domain1.de, https://domain2.de ]|[oder feste URL auf die IMMER geleitet wird]
```

[optional] Weiterhin könnte man auch von Extern kommen und wieder zurückgeleitet werden. Dazu kann man eine Liste von Domains anlegen, welche man für Weiterleitungen freigibt.

[optional] Oder man will eine feste Weiterleitung einstellen, auf die man immer nach einem erfolgreichen Login geleitet wird.

## Logout

### Per YForm-Formbuilder

1. In der Struktur einen Artikel `Logout` erstellen
2. Im Artikel `Logout` den YForm Formbuilder hinzufügen und folgende Formulardefinition eintragen:

```text
ycom_auth_logout|label|
```

```php
<?php
$form = \Yakamara\YForm\YForm::factory();

$form->setObjectparams('form_name', 'logout_form');
$form->setObjectparams('form_action', \Redaxo\Core\Filesystem\Url::article());

$form->setValueField('ycom_auth_logout', ['logout']);

echo $form->getForm();
```

3. Im REDAXO-Backend unter `YCom` > `Einstellungen` den Artikel unter `Logout` verknüpfen.
4. Den Artikel für den Nutzer zugänglich verlinken, z.B. in der Navigation oder im Header.

Nun können sich Nutzer nach einem Login ausloggen und werden dann weitergeleitet.

### Ohne YForm-Formbuilder
Alternativ kann ein Logout mithilfe der Funktion `\Yakamara\YCom\Auth\Auth::clearUserSession()` ausgeführt werden. 

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
showvalue|email|E-Mail-Adresse / Login

text|firstname|Vorname
validate|empty|firstname|Bitte geben Sie Ihren Vornamen ein.
text|name|Nachname
validate|empty|name|Bitte geben Sie Ihren Nachnamen ein.

action|showtext|<div class="alert alert-success">Profildaten wurden aktualisiert</div>|||1
action|ycom_auth_db
```

3. Den Artikel für den Nutzer zugänglich verlinken, z.B. in der Navigation oder im Header.

> Hinweis: Über den Formularcode-Generator in YForm kann man die Pipe-Schreibweise des Formulars auch für andere Felder des Nutzers abrufen.

> Hinweis: Wird als Login nicht das Feld `email` herangezogen, sondern der Nutzername, lautet die 3. Zeile nicht `showvalue|email|E-Mail-Adresse`, sondern `showvalue|login|Login:`.

## Registrierung

> **Hinweis zu Status-Werten:** Die Status-Werte sind als Konstanten in der Klasse `rex_ycom_user` definiert. Während in YForm-Formularen die numerischen Werte verwendet werden (z.B. `hidden|status|0`), kann im PHP-Code auf die entsprechenden Konstanten zurückgegriffen werden:
> - `STATUS_INACTIVE_TERMINATION = -3`
> - `STATUS_INACTIVE_LOGINS = -2`
> - `STATUS_INACTIVE = -1`
> - `STATUS_REQUESTED = 0`
> - `STATUS_CONFIRMED = 1`
> - `STATUS_ACTIVE = 2`
>
> Siehe auch die [Status-Liste](05_passwords.md#status-liste-eines-ycom-accounts) für weitere Details.

### Registrierungs-Formular

Die Registrierung läuft über ein Token: das Formular legt den Zugang mit `status = 0` an und
verschickt einen Bestätigungslink. Erst der Klick darauf setzt den Status auf `1` und loggt ein.

```text
ycom_user_token|token|create|register|

objparams|submit_btn_label|Registrieren

hidden|status|0

fieldset|logindata|Zugangsdaten

text|email|E-Mail*|
validate|empty|email|Bitte geben Sie Ihre E-Mail-Adresse ein.
validate|type|email|email|Bitte geben Sie eine korrekte E-Mail-Adresse ein.
validate|unique|email|Diese E-Mail-Adresse wird bereits verwendet.|rex_ycom_user

text|email_2|E-Mail bestätigen*||no_db
validate|compare|email|email_2||Bitte geben Sie zweimal dieselbe E-Mail-Adresse ein.

text|firstname|Vorname*
validate|empty|firstname|Bitte geben Sie Ihren Vornamen ein.

text|name|Nachname*
validate|empty|name|Bitte geben Sie Ihren Nachnamen ein.

ycom_auth_password|password|Ihr Passwort*|{"length":{"min":10},"letter":{"min":1},"digit":{"min":1}}|Das Passwort muss mindestens 10 Zeichen lang sein, einen Buchstaben und eine Ziffer enthalten.|1
validate|empty|password|Bitte geben Sie ein Passwort ein.
password|password_2|Passwort bestätigen*||no_db|{"autocomplete":"new-password"}
validate|compare|password|password_2||Bitte geben Sie zweimal dasselbe Passwort ein.

checkbox|termsofuse_accepted|Ich habe die Nutzungsbedingungen akzeptiert.|0|0|
validate|empty|termsofuse_accepted|Bitte bestätigen Sie die Nutzungsbedingungen.

html|required|<p class="form-required">* Pflichtfelder</p>

# email als Login verwenden
action|copy_value|email|login
action|db|rex_ycom_user
action|tpl2email|ycom_register|email|
action|showtext|<div class="alert alert-success">Wir haben Ihnen eine E-Mail geschickt. Bitte bestätigen Sie darin Ihre Registrierung.</div>|||1
```

> Hinweis: Sollen keine Nutzungsbedingungen abgefragt werden, `checkbox|termsofuse_accepted|…`
> durch `hidden|termsofuse_accepted|1` ersetzen.

### Registrierungsbestätigung

Ein eigener Artikel, den der Link aus der E-Mail aufruft. Er darf für Nicht-Eingeloggte erreichbar
sein (`ycom_auth_type = 2`).

```text
ycom_user_token|token|validate|register|Der Link ist nicht mehr gültig. Bitte registrieren Sie sich erneut.
hidden|status|1
objparams|submit_btn_label|Registrierung abschließen
```

### E-Mail-Template

Ein YForm-E-Mail-Template mit dem Key `ycom_register` anlegen. `X` durch die Artikel-ID der
Bestätigungsseite ersetzen:

```php
<?= trim(\Redaxo\Core\Core::getServer(), '/') . \Redaxo\Core\Filesystem\Url::article(X, null, ['token' => 'REX_YFORM_DATA[field=token]']) ?>
```

Ausführlich — auch für Direktlogin und Passwort-Reset — steht das in [Tokens](11_tokens.md).
