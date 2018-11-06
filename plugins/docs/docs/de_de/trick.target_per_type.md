# Unsterschiedliche Zielseiten für verschiedene Nutzer

Wenn nach dem Login Nutzer zu unterschiedlichen Seiten weitergeleitet werden sollen, muss YCom um die entsprechende Funktion erweitert werden.

## Variante A: Unterschiedliche Zielseiten je YCom-Nutzer 

Voraussetzung ist, dass mindestens ein Benutzer vorhanden ist.

**Vorbereitung**

Zunächst muss die Tabelle `User` um ein Feld erweitert werden, das die gewünschte ID zum Zielartikel enthält.

1. im REDAXO-Backend `YCom` -> `User` aufrufen
2. In der Tabelle User auf `Felder editieren` klicken. 
3. Auf das `+` Symbol klicken, um ein neues YForm-Feld hinzuzufügen.
4. Als Feldtyp `be_link` auswählen und z.B. folgende Werte eingeben:
    * Name: `target_id`
    * Bezeichnung: `Ziel-Artikel`
5. Das Feld mit `Speichern` hinzufügen.

**Zuweisung der Zielseiten**

Als nächstes wird jedem Benutzer ein Zielartikel zugewiesen. 

1. Im REDAXO-Backend `YCom` -> `User` aufrufen
2. Benutzer editieren, um im neuen Feld `Ziel-Artikel` einen Artikel aus der Struktur auszuwählen und speichern. Diesen Schritt für jeden Benutzer wiederholen 

**Weiterleitung einrichten**

Zu guter Letzt werden REDAXO und YCom so konfiguriert, dass nach dem Login automatisch auf den richtigen Artikel weitergeleitet wird.

1. Im REDAXO-Backend ein neues Template `Login-Weiterleitung` anlegen und folgenden Code kopieren:

```
<?php

$ycom_user = rex_ycom_auth::getUser();

if($ycom_user) {
    
    $target_id = $ycom_user->getValue("target_id");
    
    if($target_id) {
        rex_redirect($target_id);
    } 
    
} else {
    // System-Start-Artikel
    rex_redirect(rex_article::getSiteStartArticleId()); 
    // Alternativ: YRewrite-Domain-Startartikel
    # rex_redirect(rex_yrewrite::getCurrentDomain()->getStartId()); 
}
?>
```

> Hinweis: Wenn ein Nutzer auf den Artikel zugreift, ohne eingeloggt zu sein, wird er automatisch auf die Startseite weitergeleitet.

2. Unter Struktur einen neuen Artikel `Login-Weiterleitung` anlegen und das Template `Login-Weiterleitung` zuweisen.
3. Im REDAXO-Backend `YCom` -> `Einstellungen` aufrufen und unter "...wenn erfolgreich eingeloggt [target_id_jump_ok]" den Artikel `Login-Weiterleitung` auswählen.

## Variante B: Unterschiedliche Zielseiten je YCom-Gruppe 

Voraussetzung ist, dass mindestens ein Benutzer mindestens einer Gruppe zugewiesen ist.

**Vorbereitung**

Zunächst muss die Tabelle `Gruppen` um ein Feld erweitert werden, das die gewünschte ID zum Zielartikel enthält.

1. im REDAXO-Backend `YCom` -> `Gruppen` aufrufen
2. In der Tabelle Gruppen auf `Felder editieren` klicken. 
3. Auf das `+` Symbol klicken, um ein neues YForm-Feld hinzuzufügen.
4. Als Feldtyp `be_link` auswählen und z.B. folgende Werte eingeben:
    * Name: `target_id`
    * Bezeichnung: `Ziel-Artikel`
5. Das Feld mit `Speichern` hinzufügen.

**Zuweisung der Zielseiten**

Als nächstes wird jeder angelegten Gruppe ein Zielartikel zugewiesen. 

1. Im REDAXO-Backend `YCom` -> `Gruppen` aufrufen
2. Gruppe editieren, um im neuen Feld `Ziel-Artikel` einen Artikel aus der Struktur auszuwählen und speichern. Diesen Schritt für jede Gruppe wiederholen 

**Weiterleitung einrichten**

Zu guter Letzt werden REDAXO und YCom so konfiguriert, dass nach dem Login automatisch auf den richtigen Artikel weitergeleitet wird.

1. Im REDAXO-Backend ein neues Template `Login-Weiterleitung` anlegen und folgenden Code kopieren:

```
<?php

$ycom_user = rex_ycom_auth::getUser();

if($ycom_user) {
    
    $target_id = $ycom_user->getRelatedDataset('ycom_groups')->getValue("target_id");
    
    if($target_id) {
        rex_redirect($target_id);
    } 

} else {
    // System-Start-Artikel
    rex_redirect(rex_article::getSiteStartArticleId()); 
    // Alternativ: YRewrite-Domain-Startartikel
    # rex_redirect(rex_yrewrite::getCurrentDomain()->getStartId()); 
}
?>
```

> Hinweis: Wenn ein Nutzer auf den Artikel zugreift, ohne eingeloggt zu sein, wird er automatisch auf die Startseite weitergeleitet.

2. Unter Struktur einen neuen Artikel `Login-Weiterleitung` anlegen und das Template `Login-Weiterleitung` zuweisen.
3. Im REDAXO-Backend `YCom` -> `Einstellungen` aufrufen und unter "...wenn erfolgreich eingeloggt [target_id_jump_ok]" den Artikel `Login-Weiterleitung` auswählen.
