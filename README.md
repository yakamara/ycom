YCom für REDAXO 5.x
=============

YCom ist ein Addon, das REDAXO um eine Frontend-Authentifizierung erweitert. Dadurch lässt sich bspw. ein einfaches Login im Frontend umsetzen - ebenso wie eine komplexe Community-Verwaltung.

Features
--------

* Benutzerverwaltung: Erstellung und Verwaltung von Frontend-Benutzern
* Selbständige Registierung: Nutzer können sich selbständig für einen geschützten Bereich registrieren
* Passwort zurücksetzen: Frontend-Nutzer können selbständig ihr Passwort zurücksetzen 
* Gruppen-Berechtigungen: Frontend-Nutzer können bestimmten Gruppen zugeordnet und verwaltet werden 
* Sichtbarkeit: Die Sichtbarkeit von Artikeln und Kategorien kann für bestimmte Frontend-Nutzer aktiviert oder deaktiviert werden
* Einstellungen: Je nach in den Einstellungen gewählten Artikel wird man beim Login/Logout auf einen bestimmten Artikel weitergeleitet.

> Hinweis: Die einzelnen Features können losgelöst voneinander verwendet werden.

![Screenshot](https://raw.githubusercontent.com/yakamara/redaxo_ycom/assets/ycom_01.png)

Installation
------------

* Ins Backend einloggen und mit dem Installer installieren.

> Hinweis: Benötigt wird das YForm-Addon, empfohlen in der aktuellsten Version.

Last Changes
-----------

### Version 2.1 // xx.xx.2017


### Änderungen / Korrekturen

Achtung: Demo geht nicht mehr. Status für angefragt ist nun 0.

* Cookie/Sessionmanagement verbesser. (Sessionfixation, Eingloggt bleiben Cookie)
* AuthRules um Varianten ergänzt: nach 5/10/20 Login-Fehlversuchen 5/15 Minuten gesperrt
* Passwortregeln ergänzt. Passwort Policy Rules + automatisch passende Passworterstellung
* BUG: Statusüberprüfung ging nicht
* Auslesen der Konfig korrigiert.
* YRewrite Sitemap beachtet nun die YCom Rechte
* UserGroup Un/Install korrigiert
* Diverse Textkorrekturen
* Beispielcode vereinfacht
* Authentifizierung lief nicht.
* Class View: Login Info bei korrigiert, Login und Passwort Felder korrigiert
* Referer vs Login führte zu Fehlern
* Gruppenpage entfernt da unnötig
* Anzahl der Loginversuche kann nun eingestellt werden
* rex_ycom_user::isInGroup(id) ergänzt
* Artikelrechte können nun einem User zugewiesen werden.
* schwedisch ergänzt
* Authplugin wird nun immer mitinstalliert
* Weiterleitung beim Login können nun nur noch auf die aktuelle Seite geleitet werden.
* EP "REX_YCOM_YFORM_SAVED" ergänzt. in YForm action auth_db.
* Kündigungsstatus ergänzt, damit ein User sein Konto als gekündigt markieren kann.
* Seitenrechte wurden nur dem Admin angezeigt.



### Version 2.0 // 21.04.2017

* Basiert nun auf YFORM (ORM von YForm)
* Fehlende Übersetzungen ergänzt
* Maximale Fehllogins werden beachtet
* Userfelder ergänzt. Letzte Aktion, Letzter Login, Login Fehlversuche
* Rechte können ergänzt werden. In der Permissionfestlegung im Artikel wie auch im Frontend bei der Abfrage.
* PHP 7 fixes
* Texte in de/en ergänzt
* debugmeldung entfernt

### Version 1.0 // 01.06.2016

_ Community User und Gruppentabellen
_ Authentifizierung durch das Auth-Plugin
_ Gruppenverwaltung und Zuweisung durch das Group-Plugin
_ Zuweisung der Rechte über die Artikel
