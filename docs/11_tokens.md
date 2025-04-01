# Tokens

Über Tokens werden E-Mail und Bestätgigungen geprüft, wie z.B. bei der Registrierung oder beim Passwort zurücksetzen. Man kann aber auch bestimmte Userinformationen sich nochmal über eine E-Mail überprüfen lassen und einen Direktlogin erzeugen. Ein Token ist für eine Aktion gültig und wird nach der Aktion gelöscht. 

## A) Beispiel: Direktlogin

Man möchte auf der Webseite ein Login über eine E-Mail-Adresse ermöglichen. Dazu wird ein Token generiert, das an die E-Mail-Adresse gesendet wird. Der Benutzer kann sich dann über den Link direkt einloggen.

### 1. Token mit YForm Pipecode generieren

Einen Artikel (A) erstellen und dort ein YForm Formular einfügen.
Folgenden Pipecode einfügen:

```php
ycom_user_token|token|create|login|email

text|email|E-Mail:|

validate|type|email|email|Bitte geben Sie Ihre E-Mail-Adresse ein.
validate|empty|email|Bitte geben Sie Ihre E-Mail-Adresse ein.
validate|in_table|email|rex_ycom_user|email|Für die angegebene E-Mail-Adresse existiert kein Nutzer.|

action|tpl2email|login_email_template|email|
action|showtext|Sie erhalten eine E-Mail mit einem Link, über den Sie sich einloggen können|<p>|</p>|1

```

### 2. Artikel für die Tokenüberprüfung und das Login erstellen

Einen weiteren Artikel (B) erstellen und dort folgenden Pipecode einfügen:

```php
ycom_user_token|token|validate|login|Token ist falsch
objparams|submit_btn_label|Einloggen

```

### 3. E-Mail Template erstellen

Ein E-Mail Template erstellen, das den Benutzer über den Login informiert. 
In diesem Beispiel hat das YForm Template folgenden key: `login_email_template`.
Bitte das X mit der Artikel ID von Artikel (B) ersetzen.

```php
<?= trim(rex::getServer(),'/') . rex_getUrl(X, null, ['token' => REX_YFORM_DATA[field=token]]); ?>

``` 

## B) Beispiel: Passwort zurücksetzen

In diesem Beispiel geht es darum, dass ein User sein Passwort vergessen hat und dieses nun neu gesetzt werden soll. 

### 1. Token mit YForm Pipecode generieren

Einen Artikel (A) erstellen und dort ein YForm Formular einfügen.

Folgenden Pipecode einfügen:

```php
ycom_user_token|token|create|password_reset|email

text|email|E-Mail:|

validate|type|email|email|Bitte geben Sie Ihre E-Mail-Adresse ein.
validate|empty|email|Bitte geben Sie Ihre E-Mail-Adresse ein.
validate|in_table|email|rex_ycom_user|email|Für die angegebene E-Mail-Adresse existiert kein Nutzer.|

action|tpl2email|resetpassword_de_template|email|
action|showtext|Sie erhalten eine E-Mail mit einem Link, über den Sie das Passwort zurücksetzen können.|<p>|</p>|1
```
### 2. Artikel für die Tokenüberprüfung und das Passwort zurücksetzen erstellen

Einen weiteren Artikel (B) erstellen und dort folgenden Pipecode einfügen:

```php
ycom_user_token|token|validate|password_reset|Token ist falsch
hidden|new_password_required|1
objparams|submit_btn_label|Token bestätigen, einloggen und Passwort neu setzen
```

### 3. E-Mail Template erstellen

Ein E-Mail Template erstellen, das den Benutzer über den Login informiert.
In diesem Beispiel hat das YForm Template folgenden key: `resetpassword_de_template`.
Bitte das X mit der Artikel ID von Artikel (B) ersetzen.

```php
<?= trim(rex::getServer(),'/') . rex_getUrl(X, null, ['token' => REX_YFORM_DATA[field=token]]); ?>
``` 

## C) Beispiel: Registrierung

Hier wird ein Token generiert, der den Benutzer über die Registrierung informiert und den Account bestätigt.

### 1. Token mit YForm Pipecode generieren

Einen Artikel (A) erstellen und dort ein YForm Formular einfügen.

Folgenden Pipecode einfügen:

```php
ycom_user_token|token|create|register|

objparams|submit_btn_label|Registrieren

hidden|status|0

fieldset|label|Login-Daten:

text|email|E-Mail*|
text|email_2|E-Mail bestätigen*||no_db

text|firstname|Vorname*
validate|empty|firstname|Bitte geben Sie Ihren Vornamen ein.

text|name|Nachname*
validate|empty|name|Bitte geben Sie Ihren Nachnamen ein.

ycom_auth_password|password|Ihr Passwort*|{"length":{"min":10},"letter":{"min":1},"lowercase":{"min":0},"uppercase":{"min":0},"digit":{"min":1},"symbol":{"min":0}}|Das Passwort muss mindestens 10 Zeichen lang sein und eine Ziffer enthalten.
password|password_2|Passwort bestätigen*||no_db|{"autocomplete":"new-password"}

checkbox|termsofuse_accepted|Ich habe die Nutzungsbedingungen akzeptiert.|0|0|

html|required|<p class="form-required">* Pflichtfelder</p>

validate|type|email|email|Bitte geben Sie korrekte E-Mail-Adresse ein.
validate|unique|email|Diese E-Mail-Adresse wird bereits verwendet.|rex_ycom_user
validate|empty|password|Bitte geben Sie ein Passwort ein.
validate|compare|password|password_2||Bitte geben Sie zweimal dasselbe Passwort ein.
validate|compare|email|email_2||Bitte geben Sie zweimal dieselbe E-Mail-Adresse ein.

# email als Login verwenden
action|copy_value|email|login
action|db|rex_ycom_user
action|tpl2email|register_de_template|email|
```

### 2. Artikel für die Tokenüberprüfung um die Registrierung abzuschliessen

Einen weiteren Artikel (B) erstellen und dort folgenden Pipecode einfügen:

```php
ycom_user_token|token|validate|register|Token ist falsch
hidden|status|1
objparams|submit_btn_label|Registrierung durchführen und E-Mail bestätigen
```

### 3. E-Mail Template erstellen

Ein E-Mail Template erstellen, das den Benutzer über den Login informiert.
In diesem Beispiel hat das YForm Template folgenden key: `register_de_template`.
Bitte das X mit der Artikel ID von Artikel (B) ersetzen.

```php
<?= trim(rex::getServer(),'/') . rex_getUrl(X, null, ['token' => REX_YFORM_DATA[field=token]]); ?>
``` 
