# Einrichtung

Nach der Installation von YCom werden für die Inbetriebnahme einige Schritte benötigt.

## 1. Formbuilder installieren

YForm Formbuilder installieren unter `YForm` > `Übersicht` > `Setup` > `YForm Formbuilder installieren`

## 2. Struktur vorbereiten

Folgende Kategorien in der Struktur in oberster Ebene anlegen:

* **Login**: Menüpunkt soll angezeigt werden, wenn der Besucher nicht eingeloggt ist.

* **Logout**: Menüpunkt soll angezeigt werden, wenn der Besucher eingeloggt ist.

* Unter `Logout` optional folgende Kategorien anlegen:

  * **Passwort ändern**: Dieser Artikel soll ein Formular zum Ändern des Passworts beinhalten.

> Hinweis: Die hier vorgeschlagene ist nicht verpflichtend, wird jedoch für Anfänger empfohlen.

## Navigation vorbereiten

Die Navigation muss angepasst werden, sodass Kategorien und Artikel, für die der Nutzer keine Berechtigung hat, nicht dargestellt werden. Sofern die Core-Funktion `rex_navigation::factory()` verwendet wird, folgende Zeile hinzufügen: `$nav->addCallback('rex_ycom_auth::articleIsPermitted');`

> Hinweis: Wenn die Navigation nicht via `rex_navigation::factory()` 
erfolgt, werden ggf. alle Menüpunkte angezeigt. Die Navigation muss dann so erweitert werden, dass Nutzer ohne Berechtigungen die entsprechenden Menüpunkte nicht sehen kann.

## YCom konfigurieren

YCom Einstellungsseite im REDAXO-Backend aufrufen unter `YCom` > `Einstellungen` und konfigurieren:

* **Weiterleitungen**: 
  * als `article_id_jump_ok` bspw. die Startseite verwenden. 
  * als `article_id_jump_not_ok` (nur SAML-Authentifizierung) bspw. den Login-Artikel verwenden. 
  * als `article_id_jump_logout` bspw. den Login-Artikel oder die Startseite verwenden. 
  * als `article_id_jump_denied` bspw. den Login-Artikel verwenden oder einen eigenen Artikel anlegen.
* **Allgemeine Seiten**
  * als Login-Seite den Login-Artikel auswählen.
* **Login-Feld**
  * als Login-Feld die Option E-Mail-Adresse (`email`) auswählen. Alternativ `login`, wenn bspw. ein eigenes Pseudonym als Nutzername verwendet werden soll. 

> Hinweis: Dies sind die empfohlenen Minimal-Einstellungen für einen reibungslosen Betrieb.

## Weitere Schritte

Anschließend können Module und Templates in Abhängigkeit des Login-Status (eingeloggt/ausgeloggt) dargestellt werden. 

```
$ycom_user = rex_ycom_auth::getUser() // zu Beginn eines Moduls oder Templates

if ($ycom_user) {
	// Benutzter besitzt die nötige Berechtigung
} else {
	// Benutzter besitzt die nötige Berechtigung nicht
}
```
> Siehe auch: [User und Gruppen auslesen](user.details.md)
