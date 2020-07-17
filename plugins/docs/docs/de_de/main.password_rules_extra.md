# Erweiterte Passwortregeln einbauen

wenn man bereits verwendete Passwörter verbieten möchte.


## Erstellung der Passworttabelle

Dazu ist es zunächst nötig die Passworteingaben des Users zu erfassen. Eine entsprechende Tabelle muss zunächst über YForm angelegt werden. Wir nennen diese mal `rex_ycom_user_password`, stellen diese Tabelle auf versteckt und aktiv und erstellen folgende Felder:

`datestamp_created` als datetime-Feld welches immer automatisch befüllt wird, wenn ein Datensatz erstellt wird.
`user_id` als be_relation zur ycom_user Tabelle als PopUp-Single-Feld.
`password` als text-Feld, in welchem dann die alten Passwörter verschlüsselt gespeichert werden.

Sobald diese Tabelle existiert, müssen wir dafür sorgen, dass die Passwörter des Users bei jeder neuen Eingabe in unserer neuen Tabelle erfasst werden.

Das passiert im Normalfall unter "Passwort ändern". Dort gibt man sein altes Passwort ein und erstellt ein Neues.

Über einen ExtensionPoint (EP) können wir uns nun an dieses Formular hängen, und die Werte entsprechend abziehen und bei uns speichern.
Dieser EP könnt im eigenen `project`-AddOn liegen, dort z.B. in der boot.php

für das **xxxxxx** bitte den Feldnamen des neuen Passwortes eintragen.

```
rex_extension::register('YCOM_YFORM_SAVED', function ($params) {

    /* @var $form rex_yform */
    $form = $params->getParams()['form'];
    // email means here: email pool of values, which normally is for sending emails.
    $values = $form->params['value_pool']['email'];
    $nameOfFieldInForm = 'xxxxxx';

    // wir gehen davon aus, dass das -password-Feld nur im Passwort ändern
    // Formular verwendet wird.

    if (isset($values[$nameOfFieldInForm])) {
        $newPassword = $values[$nameOfFieldInForm];
        $newPassword = rex_login::passwordHash($newPassword);
        $me = rex_ycom_user::getMe();
        if ($me) {
            $instance = rex_yform_manager_dataset::create('rex_ycom_user_password');
            $instance->setValue('user_id', $me->getId());
            $instance->setValue('password', $newPassword);
            $instance->save();
        }
    }
});

```

**Erklärung zu diesem EP**: Beim Aufruf eines Formular welches Userdaten speichert, wird dieser EP aufgerufen. Es werden die Daten des Formulars übernommen und geprüft, ob das Passwort-Feld gesetzt ist, damit wir wissen, dass wir dieses zu Speichern nutzen können. Dieses Passwort wird gehasht, also verschlüsselt. Die ID des in dem Moment eingeloggten Users wird mit dem gehashten Passwort gespeichert. Ein Zeitstempel wird automatisch durch die vorher definierte Tabelle gesetzt.

## Validierung der Passworteingaben

Zunächst eine entsprechende Funktion erstellt werden, welches das aktuelle Passwort ausliest und diese mit den vorhandenen alten Passwörtern vergleicht.

Wir erstellen hier eine vereinfachte Variante über eine einfache Funktion. Diese Funktion legen wir, genauso wie den obigen Aufruf des EPs in die project/boot.php.

```
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
```

**Erklärung zu dieser Funktion**: Der Name des Feldes aus dem Formular, wie auch der Wert, also das Passwort selbst, wird übergeben. Mit der ID des Users werden alle Passwörter aus der Vergangenheit geladen und jedes einzelne mit dem neuen Passwort verglichen. Ist eines damit identisch wird true zurückgegeben, so dass das Formular eine Fehlermeldung ausgeben kann.


Im Passwort vergessen Formular muss folgende Zeile ergänzt werden, damit die Funktion überhaupt mit den richtigen Werten aufgerufen wird.
für das **xxxxxx** bitte den Feldnamen des neuen Passwortes eintragen.

```
validate|customfunction|xxxxxx|validatePassword||Dieses Passwort haben sie bereits genutzt
```

## Nur die letzten 6 Passwörter / die Passwörter der letzten 3 Monate überprüfen.

Über einen **Cronjob** in REDAXO werden immer die ältesten Passwörter gelöscht. Hier ein Beispiel der letzten 6 Passwörter.
Dieser Cronjob muss regelmäßig ausgeführt werden.

```

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

```

Genauso, und sogar einfacher, geht es auch mit Passwörtern die älter als 6 Monate sind.

```
$Date = new DateTime();
$Date->modify("-6 months");
rex_sql::factory()->setQuery('DELETE FROM rex_ycom_user_password where created_datetime < :deletedate', ['deletedate' => $Date->format('Y-m-d H:i:s')]);
```


## User welche seit 6 Monaten nicht ihr Passwort geändert haben

könnte man auf `new_password_required` = 1 setzen. So dass diese User zu einem neuen Passwort gezwungen werden. (Sofern es entsprechend eingerichtet ist).
Als Cronjob einrichten.


```
$Date = new DateTime();
$Date->modify("-6 months");
$Users = rex_ycom_user::query()->where('status', 0, '>')->find();
foreach($Users as $User) {
    $result = rex_sql::factory()->getArray('select * from rex_ycom_user_password where user_id = :uid and created_datetime > :dt LIMIT 1', ['uid' => $User->getId(), 'dt' => $Date->format('Y-m-d H:i:s')]);
    if (0 == count($result)) {
        $User->setValue('new_password_required', 1)->save();
    }
}
```

## User welche seit 9 Monaten nicht ihr Passwort geändert haben

könnte man auf inactive setzen.
Als Cronjob einrichten.


```
$Date = new DateTime();
$Date->modify("-9 months");
$Users = rex_ycom_user::query()->where('status', 0, '>')->find();
foreach($Users as $User) {
    $result = rex_sql::factory()->getArray('select * from rex_ycom_user_password where user_id = :uid and created_datetime > :dt LIMIT 1', ['uid' => $User->getId(), 'dt' => $Date->format('Y-m-d H:i:s')]);
    if (0 == count($result)) {
        $User->setValue('status', -1)->save();
    }
}
```
