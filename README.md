

ycom


braucht redaxo 5.1


Übersetzung allgemein noch machen

neues passwort setzen
    - mit authkey link zum neuen linkprozess

passwort kodieren nach redaxo5 art .. noch einbauen
    passwort wird im moment dopplt kodiert
    rex_yform_value_ycom_auth_password_hash ist noch kaputt

deleteuser noch an den Yform  EP dranhängen

rex_ycom:_navigation noch bauen

infotext noch rein..

weiterleitungen raus

Infocode ersetzen

gruppenverwaltung noch reinnehmen

tabellen unterhalb von ycom anzeigen lassen

rex_yform_action_ycom_auth_db anschauen

rex_yform_validate_ycom_auths anschauen

// TODO:
// rex_ycom_user table ergaenzen
// activasio_key etc..

// TODO:
// Feld für User noch anlegen: wird bereits in der basis installiert









### Community-AddOn für REDAXO 5 ###

### To Do ###

* Mehrsprachigkeit der Community User Tabelle wieder herstellen
* install & update.sql berücksichtigen
* im Auth Plugin bei der Installation prüfen ob das Feld <i>com_auth_password_hash</i> schon existiert
* im Auth Plugin bei der Installation prüfen ob die E-Mail Templates schon installiert sind (nicht jedesmal installieren)
* im Auth Plugin bei der Deinstallation Tabellen bereinigen
* kann <i>$com_user = rex_ycom_auth::getUser();</i> im Template global definiert werden? Aktuell muss ich das in Modulen neu zuweisen...
* Installation / Deinstalltion prüfen
* Bei der Registirerung funktioniert das Password compare nicht richtig. Fehlermeldung wird zwei mal ausgegeben
* Feld <i>art_ycom_permtype</i> in die Seitenleiste plazieren und aus den Artikelmetadaten entfernen
* Unbedingt den Installtionsprozess prüfen
* Zwei kam es vor, dass das Addon und/oder das Plugin plötzlich den Status install=false hatte (in der rex_config )