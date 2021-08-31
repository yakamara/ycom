## Passwort ändern

### Im Backend

Nutzern können vom Administrator jederzeit ein neues Passwort vergeben werden. 

Es wird jedoch empfohlen, Passwörter vom Nutzer selbst festlegen zu lassen, um Datenpannen aus dem Weg zu gehen.

### Im Frontend

Nutzer können selbständig das Passwort ändern.

1. In der Struktur einen Artikel anlegen, z.B. `Passwort ändern`
2. Im Editiermodus des Artikels auf der rechten Seite die passenden Berechtigungen einstellen (Sichtbar nur für eingeloggte Nutzer)
3. Das Modul `YForm Formbuilder` hinzufügen und folgendes Formular eintragen

```php
ycom_auth_password|password|Ihr Passwort:*|{"length":{"min":6},"letter":{"min":1},"lowercase":{"min":0},"uppercase":{"min":0},"digit":{"min":1},"symbol":{"min":0}}|Das Passwort muss mindestens 6 Zeichen lang sein und mindestens eine Ziffer enthalten
password|password_2|Passwort wiederholen:||no_db
validate|empty|password|Bitte geben Sie ein Passwort ein.
validate|compare|password|password_2|!=|Bitte geben Sie zweimal das gleiche Passwort ein
action|showtext|Ihre Daten wurden aktualisiert. Das neue Passwort ist ab sofort aktiv.|||1
action|ycom_auth_db
hidden|new_password_required|0
```

4. Den Artikel verlinken, damit die Nutzer die Seite zum Ändern des Passworts aufrufen können.

Nun können Nutzer ihr Passwort selbständig ändern.

> Tipp: Dieses Formular kann auch dazu genutzt werden, um Nutzer ein neues Passwort festlegen zu lassen, wenn sie ihr Passwort vergessen haben. Weitere Infos in der Doku unter `Passwort vergessen`

### Zusätzliche Prüfung auf das alte Passwort

1. Feld für altes Passwort und Validierung hinzufügen

```php
password|old_password|Altes Passwort||no_db
validate|empty|old_password|Bitte altes Passwort angeben.
validate|ycom_auth_password|old_password|Das alte Passwort ist fehlerhaft!
```

2. Neues Passwort darf nicht dem alten Passwort entsprechen

Dafür noch folgende Validierung hinzufügen

```php
validate|compare|password|old_password|==|Das neue Passwort darf nicht dem alten Passwort entsprechen.
```

Das ganze Formular sieht dann wie folgt aus:
```php
password|old_password|Altes Passwort||no_db
ycom_auth_password|password|Ihr Passwort:*|{"length":{"min":6},"letter":{"min":1},"lowercase":{"min":0},"uppercase":{"min":0},"digit":{"min":1},"symbol":{"min":0}}|Das Passwort muss mindestens 6 Zeichen lang sein und mindestens eine Ziffer enthalten
password|password_2|Passwort wiederholen:||no_db

validate|empty|old_password|Bitte altes Passwort angeben.
validate|ycom_auth_password|old_password|Das alte Passwort ist fehlerhaft!
validate|compare|password|old_password|==|Das neue Passwort darf nicht dem alten Passwort entsprechen.
validate|empty|password|Bitte geben Sie ein Passwort ein.
validate|compare|password|password_2|!=|Bitte geben Sie zweimal das gleiche Passwort ein
action|showtext|Ihre Daten wurden aktualisiert. Das neue Passwort ist ab sofort aktiv.|||1
action|ycom_auth_db
hidden|new_password_required|0
```

## Passwort zurücksetzen

### Passwort-Zurücksetzen-Formular

Da man auswählen kann, ob man ein Login verwenden möchte, oder die E-Mail als Login genutzt wird, muss man das untenstehende Formular entsprechend anpassen. Im Beispiel geht es von der E-Mail als Authentifizierungsfeld aus. Die Seite muss öffentlich erreichbar sein und kann z.B. im Login-Formular verlinkt werden.

```php
generate_key|activation_key

text|email|E-Mail:|

captcha|Bitte geben Sie den entsprechenden Sicherheitscode ein. Sollten Sie den Code nicht lesen können klicken Sie bitte auf die Grafik, um einen neuen Code zu generieren.|Sie haben den Sicherheitscode falsch eingegeben.

validate|email|email|Bitte geben Sie die E-Mail ein.
validate|empty|email|Bitte geben Sie Ihre E-Mail ein.
validate|in_table|email|rex_ycom_user|email|Für die angegebene E-Mail-Adresse existiert kein Nutzer.|

action|db_query|update rex_ycom_user set activation_key = ? where email = ?|activation_key,email
action|tpl2email|resetpassword_de|email|

action|showtext|Sie erhalten eine E-Mail mit einem Link, über den Sie das Passwort neu setzen können.|<p>|</p>|1
```

### E-Mail-Template `resetpassword_de` für „Passwort zurücksetzen” anlegen

```php
<!--
Bitte bei (888) die ID des Artikels für die "E-Mail-Bestätigungsseite" eintragen
-->
<p>Bitte klicken Sie diesen Link, um das Passwort zurück zu setzen:</p>
<p><a href="<?= trim(rex::getServer(),'/') . rex_getUrl(888) ?>?rex_ycom_activation_key=REX_YFORM_DATA[field=activation_key]&rex_ycom_id=REX_YFORM_DATA[field=email]"><?= trim(rex::getServer(),'/') . rex_getUrl(888) ?>?rex_ycom_activation_key=REX_YFORM_DATA[field=activation_key]&rex_ycom_id=REX_YFORM_DATA[field=email]</a></p>

```

Die Seite zur Bestätigung der `resetpassword_de` E-Mail muss öffentlich erreichbar sein, damit der User geprüft und eingeloggt werden kann. Hier bietet es sich an in der `action|html` noch einen Link zur **Passwort ändern** Seite zu setzen.

### E-Mail-Bestätigungsseite erstellen

```php
objparams|submit_btn_show|0
objparams|send|1
objparams|csrf_protection|0

validate|ycom_auth_login|activation_key=rex_ycom_activation_key,email=rex_ycom_id|status=1|Zugang wurde bereits bestätigt oder ist schon fehlgeschlagen|status

action|ycom_auth_db|update
action|html|<b>Sie sind eingeloggt. Das Passwort kann nun geändert werden.</b>
```

Arbeitet man nur mit *bestätigten Accounts*, muss der `status=1` sein.

Der Status kann hier auch 2 sein (`status=2`), wenn man mit selbst angelegten Accounts ohne Bestätigung arbeitet. Wenn man also im Backend einen Account anlegt und diesen auf **Zugang ist aktiv** stellt. Siehe dazu auch folgende Liste.


#### Status Liste eines YCom-Accounts

Interner Name | Übersetzung | Wert
------ | ------ | ------
ycom_account_inactive_termination | Zugang wurde gekündigt | -3
ycom_account_inactive_logins | Zugang wurde deaktiviert [Loginfehlversuche] | -2
ycom_account_inactive | Zugang ist inaktiv | -1
ycom_account_requested | Zugang wurde angefragt | 0
ycom_account_confirm | Zugang wurde bestätigt und ist aktiv | 1
ycom_account_active | Zugang ist aktiv | 2

#### Weitere Hinweise

Um zu verhindern, dass ein Passwort-zurücksetzen-Link mehrfach verwendet werden kann, kann mit folgenden Zeilen der activation_key neu gesetzt werden:

```php
generate_key|activation_key
action|db_query|update rex_ycom_user set activation_key = ? where email = ?|activation_key,rex_ycom_id
```

Da man durch das Modul gleich eingeloggt wird, kann es sinnvoll sein im Anschluss gleich einen Redirect auszuführen, damit ggf. der Login Status im Menü aktualisiert wird:

```php
// bei pw_change_article_id die Id der Passwort-ändern-Seite eintragen
action|redirect|pw_change_article_id
```

## Passwortregeln

Immer dann, wenn ein Passwort eingegeben werden soll, müssen Passwortregeln verwendet werden. z.B.

* Beim ändern eines Passworts,
* bei der Registrierung, oder
* beim Anlegen des Nutzers über das REDAXO-Backend unter `YCom` > `User`.

Wird keine Passwortregel hinterlegt, werden die Passwortregeln des REDAXO-Backends aus der `config.yml`-Datei verwendet.

### Definition

Bei der Installation von YCom erhält das Feld `passwort` der YCom-User-Tabelle folgened Einstellung:

```php
{"length":{"min":6},"letter":{"min":1,"generate":10},"lowercase":{"min":0},"uppercase":{"min":0},"digit":{"min":1},"symbol":{"min":0}}
```

Wie man sieht, können verschiedene Regeln vergeben werden:
* Länge des Passworts (min/max)
* Anzahl der Buchstaben (min/max), davon Kleinbuchstaben (lowercase) und Großbuchstaben (uppercase)
* Anzahl der Zahlen (min/max)
* Anzahl der Symbole (min/max)

> **Tipp:** Mit dem Parameter `generate` wird beim Anlegen des Nutzers über das Backend angegeben, mit wie viel entsprechenden Zeichen das Passwort generiert werden soll.

### Beispiel

**Passwort ändern Formular:**

```php
ycom_auth_password|password|Ihr Passwort:*|{"length":{"min":6},"letter":{"min":1},"lowercase":{"min":0},"uppercase":{"min":0},"digit":{"min":1},"symbol":{"min":0}}|Das Passwort muss mindestens 6 Zeichen lang sein und mindestens eine Ziffer enthalten
```

### Weitere Infos zu sicheren Passwörtern

* [SZ: Das sind die fünf größten Passwort-Mythen](https://www.sueddeutsche.de/digital/it-sicherheit-das-sind-die-fuenf-groessten-passwort-mythen-1.3489400)
* [SZ: So gehen sichere Passwörter!](https://www.sueddeutsche.de/digital/it-sicherheit-so-gehen-sichere-passwoerter-1.3587661)
* [XKCD-Comic zu sicheren Passwörtern](https://xkcd.com/936/)


## Erweiterte Passwortregeln einbauen

wenn man bereits verwendete Passwörter verbieten möchte.

### Erstellung der Passworttabelle

Dazu ist es zunächst nötig die Passworteingaben des Users zu erfassen. Eine entsprechende Tabelle muss zunächst über YForm angelegt werden. Wir nennen diese mal `rex_ycom_user_password`, stellen diese Tabelle auf versteckt und aktiv und erstellen folgende Felder:

`datestamp_created` als datetime-Feld welches immer automatisch befüllt wird, wenn ein Datensatz erstellt wird.
`user_id` als be_relation zur ycom_user Tabelle als PopUp-Single-Feld.
`password` als text-Feld, in welchem dann die alten Passwörter verschlüsselt gespeichert werden.

Sobald diese Tabelle existiert, müssen wir dafür sorgen, dass die Passwörter des Users bei jeder neuen Eingabe in unserer neuen Tabelle erfasst werden.

Das passiert im Normalfall unter "Passwort ändern". Dort gibt man sein altes Passwort ein und erstellt ein Neues.

Über einen ExtensionPoint (EP) können wir uns nun an dieses Formular hängen, und die Werte entsprechend abziehen und bei uns speichern.
Dieser EP könnt im eigenen `project`-AddOn liegen, dort z.B. in der boot.php

für das **xxxxxx** bitte den Feldnamen des neuen Passwortes eintragen.

```php
<?php

if (\rex::isBackend() || \rex::isFrontend()) {
    rex_extension::register('REX_YFORM_SAVED', function ($ep): void {
        $params = $ep->getParams();
        if ('rex_ycom_user' == $params['table'] && isset($params['id']) && 0 < $params['id']) {
            $password = $params['form']->getParam('value_pool')['email']['password'];
            $password_hashed = $params['form']->getParam('value_pool')['sql']['password'];
            if ($password != $password_hashed && '' != $password) {
                $instance = rex_yform_manager_dataset::create('rex_ycom_user_password');
                $instance->setValue('user_id', $params['id']);
                $instance->setValue('password', $password_hashed);
                $instance->save();
            }
        }
    });
}

?>
```

**Erklärung zu diesem EP**: Beim Aufruf eines Formular welches Userdaten speichert, wird dieser EP aufgerufen. Es werden die Daten des Formulars übernommen und geprüft, ob das Passwort-Feld gesetzt ist, damit wir wissen, dass wir dieses zu Speichern nutzen können. Dieses Passwort wird gehasht, also verschlüsselt. Die ID des in dem Moment eingeloggten Users wird mit dem gehashten Passwort gespeichert. Ein Zeitstempel wird automatisch durch die vorher definierte Tabelle gesetzt.

### Validierung der Passworteingaben

Zunächst eine entsprechende Funktion erstellt werden, welches das aktuelle Passwort ausliest und diese mit den vorhandenen alten Passwörtern vergleicht.

Wir erstellen hier eine vereinfachte Variante über eine einfache Funktion. Diese Funktion legen wir, genauso wie den obigen Aufruf des EPs in die project/boot.php.

```php
<?php
function validatePassword($nameOfFieldInForm, $PasswordUnhashed, $unusedExtra = '') {
    $me = rex_ycom_user::getMe();
    if ($me) {
        $foundPasswords = rex_sql::factory()->getArray('select * from rex_ycom_user_password where user_id = ?', [$me->getId()]);
        foreach($foundPasswords as $foundPassword) {
            if (rex_login::passwordVerify($PasswordUnhashed, $foundPassword[$nameOfFieldInForm])) {
                return true;
            }
        }
    }
    return false;
}
?>
```

**Erklärung zu dieser Funktion**: Der Name des Feldes aus dem Formular, wie auch der Wert, also das Passwort selbst, wird übergeben. Mit der ID des Users werden alle Passwörter aus der Vergangenheit geladen und jedes einzelne mit dem neuen Passwort verglichen. Ist eines damit identisch wird true zurückgegeben, so dass das Formular eine Fehlermeldung ausgeben kann.


Im Passwort vergessen Formular muss folgende Zeile ergänzt werden, damit die Funktion überhaupt mit den richtigen Werten aufgerufen wird.
für das **xxxxxx** bitte den Feldnamen des neuen Passwortes eintragen.

```php
validate|customfunction|xxxxxx|validatePassword||Dieses Passwort haben sie bereits genutzt
```

### Nur die letzten 6 Passwörter / die Passwörter der letzten 3 Monate überprüfen.

Über einen **Cronjob** in REDAXO werden immer die ältesten Passwörter gelöscht. Hier ein Beispiel der letzten 6 Passwörter.
Dieser Cronjob muss regelmäßig ausgeführt werden.

```php
<?php

$limit = 6;
$Users = rex_sql::factory()->getArray('select user_id from( select user_id, count(id) as id_count from rex_ycom_user_password GROUP BY user_id ) as my_table WHERE id_count >= :limit', ['limit' => $limit]);
foreach ($Users as $User) {
    $s = rex_sql::factory()->setQuery('
        DELETE otbl
        FROM rex_ycom_user_password as otbl
        JOIN (
                SELECT id
                FROM rex_ycom_user_password
                WHERE user_id = :uid
                ORDER BY id desc
                LIMIT 1 OFFSET ' . $limit . '
        ) as itbl
        ON otbl.id <= itbl.id
        WHERE otbl.user_id = :uid', ['uid' => $User['user_id']]
    );
}

?>
```

Genauso, und sogar einfacher, geht es auch mit Passwörtern die älter als 6 Monate sind.

```php
<?php

$Date = new DateTime();
$Date->modify("-6 months");
rex_sql::factory()->setQuery('DELETE FROM rex_ycom_user_password where created_datetime < :deletedate', ['deletedate' => $Date->format('Y-m-d H:i:s')]);

?>
```


### User welche seit 6 Monaten nicht ihr Passwort geändert haben

könnte man auf `new_password_required` = 1 setzen. So dass diese User zu einem neuen Passwort gezwungen werden. (Sofern es entsprechend eingerichtet ist).
Als Cronjob einrichten.


```php
<?php

$Date = new DateTime();
$Date->modify("-6 months");
$Users = rex_ycom_user::query()->where('status', 0, '>')->find();
foreach($Users as $User) {
    $result = rex_sql::factory()->getArray('select * from rex_ycom_user_password where user_id = :uid and created_datetime > :dt LIMIT 1', ['uid' => $User->getId(), 'dt' => $Date->format('Y-m-d H:i:s')]);
    if (0 == count($result)) {
        $User->setValue('new_password_required', 1)->save();
    }
}

?>
```

### User welche seit 9 Monaten nicht ihr Passwort geändert haben

könnte man auf inactive setzen.
Als Cronjob einrichten.


```php
<?php

$Date = new DateTime();
$Date->modify("-9 months");
$Users = rex_ycom_user::query()->where('status', 0, '>')->find();
foreach($Users as $User) {
    $result = rex_sql::factory()->getArray('select * from rex_ycom_user_password where user_id = :uid and created_datetime > :dt LIMIT 1', ['uid' => $User->getId(), 'dt' => $Date->format('Y-m-d H:i:s')]);
    if (0 == count($result)) {
        $User->setValue('status', -1)->save();
    }
}

?>
```

