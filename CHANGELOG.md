Changelog
=========


Version 4.0.3 – 25.08.2020
--------------------------

#### Bei Update: Bitte unbedingt beachten:

Version 3.0beta unterscheidet sich von Version 4.x. Da dies ein paar elementare Änderungen mit sich gebracht hatte wurde Version 3.0 nicht released. Bitte in der Doku nachlesen. Man kann sich nun nicht mehr auf jeder Seite einloggen, sondern nur auf den spezifischen Loginseiten.

* termofuse wurde zu termsofuse
* cookie und session werden nun mit redaxo session geteilt.
* wenn "eingeloggt bleiben" genutzt wird, in der redaxo_config path für frontend und backend auch "/" setzen
* 4.0.2: kleinere Korrekturen zu termsofuse_accepted, wie auch beim Aufruf von createUserByEmail
* 4.0.2: SAML Auth Korrekturen
* 4.0.3: OAUTH2 ergänzt
* 4.0.3: Problem MariaDB vs MySQL behoben
* 4.0.3: OnUpdate Fehler bei Import der YForm Tabellen behoben


#### Neu

* PlugIns auth und media_auth sind aktiv, sobald die PlugIns installiert sind.
* SAML Authentifizierung ergänzt
* media_auth-Rule ergänzt um Weiterleitung auf ErrorSeite wenn User eingeloggt und Zugriff verboten

### Änderungen / Korrekturen

* EP REX_YCOM_YFORM_SAVED -> YCOM_YFORM_SAVED
* Umbau der action ycom_auth_db.php. rex_manager_dataset wird nun verwendet. Dadurch einheitliche EPs und Prozesse
* Prüfung auf Perm nun über Core Methode rex_article->isPermitted(), so dass diese auch bei Sitemaps (YRewrite) richtig geht
* returnTo Feld ergänzt, um erweiterte Weiterleitung mit Prüfung zu ermöglichen
* Cache von YForm wird nun bei install und uninstall geleert.

Version 3.0 – xx.xx.2018
--------------------------

#### Bei Update: Bitte unbedingt beachten:

* termofuse wurde zu termsofuse
* cookie und session werden nun mit redaxo session geteile.
* wenn "eingeloggt bleiben" genutzt wird, in der redaxo_config path für frontend und backend auch "/" setzen


#### Neu

* media_auth Plugin ergänzt. Man kann einzelnen Medien YCom Zugriffsrechte geben. braucht rex 5.7
* Doku als Plugin für bessere Übersicht und Inhalte ergänzt


### Änderungen / Korrekturen

* Passwort-Rules um "generate" ergänzt für die Passwortgenerierung
* Diverse Verbesserungen. Default Werte, Initiale Werte etc., diverse Notices entfernt
* YForm ab Version 3 nötig
* Doppelten "Passwort anzeigen" Button entfernt
* Status bei zu vielen Fehllogins ist nun richtig,
* uninstall verbessert
* referer to logout bug behoben
* Doku aktualisiert
* auth: Wenn nicht eingeloggt und gesperrte Seite -> Loginseite
* Danke an Pixeldaniel, Alexander Walther, Jürgen Weiss, Yves Torres, christophboecker für diverse Korrekturen, Doku, Sprachdateien ..
* placeholer passwort meldungen fixed


Version 2.1 – 05.05.2018
--------------------------

#### Achtung

wenn als Update muss folgendes manuell eingetragen werden.
in das Feld rex_ycom_user:status
bei der selectdefiniton: translate:ycom_account_inactive_termination=-3,translate:ycom_account_inactive_logins=-2,translate:ycom_account_inactive=-1,translate:ycom_account_requested=0,translate:ycom_account_confirm=1,translate:ycom_account_active=2

Achtung: Demo geht nicht mehr. Status für angefragt ist nun 0.

#### Änderungen / Korrekturen

* Cookie/Sessionmanagement verbessert. (Sessionfixation, Eingloggt bleiben Cookie)
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
* Beschreibungen verbessert


Version 2.0 – 21.04.2018
--------------------------

* Basiert nun auf YFORM (ORM von YForm)
* Fehlende Übersetzungen ergänzt
* Maximale Fehllogins werden beachtet
* Userfelder ergänzt. Letzte Aktion, Letzter Login, Login Fehlversuche
* Rechte können ergänzt werden. In der Permissionfestlegung im Artikel wie auch im Frontend bei der Abfrage.
* PHP 7 fixes
* Texte in de/en ergänzt
* debugmeldung entfernt


Version 1.0 – 01.06.2018
--------------------------

_ Community User und Gruppentabellen
_ Authentifizierung durch das Auth-Plugin
_ Gruppenverwaltung und Zuweisung durch das Group-Plugin
_ Zuweisung der Rechte über die Artikel
