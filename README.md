Community for REDAXO Version 4.6
=============

AddOn to manage tables and forms for REDAXO CMS Version 4.6


Installation
-------

* Download and unzip
* Rename the unzipped folder from redaxo_community to community
* Move the folder to your REDAXO 4.6 System /redaxo/include/addons/
* Install and activate the addon xform and the plugins setup, manager, email in the REDAXO 4.6 backend

OR

* User Installer in REDAXO 4.6 Backend


Last Changes
-------

### Version 4.7.2 // 9. Februar 2014

### Info

* Braucht nun mindestens die XForm Version 4.6.10

### Neu

* Filebrowser und Verwaltung für geschlossene Bereiche als PlugIn ergänzt. Durch die Installtion des Plugins
wird das entsprechende Modul installiert.


### Bugs

* Debugmeldung bei Facebookauthentifizierung entfernt
* Durch die XForm 4.6.10 ist die Methode "getXFormFieldsByType" verschwunden
* Newsletter: XForm Validate Klasse wurde falsch eingebunden


### Version 4.7.1 // 29. Juli 2014

#### Info

* Wenn man ein Update über den Installer macht, werden die Konfigurationen überschrieben.

#### Bugs

* Usertabelle wurde bei der Installation nicht automatisch aktiviert.
* Kommentarplugin installierte den Namen nicht richtig. Deswegen konnten keine Infos gespeichert werden.
* Installationprozess warf Fehler.



### Version 4.7 // 25. Juli 2014

#### Info

* Läuft nur mit XForm ab Version 4.7

#### Neu

* XForm Manager Api eingesetzt, so dass sich die Felder richtig erstellen und nur KOrrekturen übernommen werden.
* Facebookt Auth: Sessionstarted geprüft
* Community Demo aktualisiert. Profil hatte nicht funktioniert.
