# Nutzer und Profile

Jeder YCom-Nutzer hat ein Profil, das unter `YCom` > `User` eingesehen werden kann. Dort können Profilfelder beliebig erweitert werden.

> **Achtung:** Bitte bestehende Felder nicht ändern oder löschen.

## Profil-Formular

Eingeloggte Nutzer können auf Wunsch ihr Profil bearbeiten.

# Logout 

1. In der Struktur eine Kategorie / einen Artikel `Profil` erstellen.
2. Im Artikel `Profil` den YForm Formbuilder hinzufügen und folgende Formulardefinition eintragen:

```
ycom_auth_load_user|userinfo|email,firstname,name
objparams|form_showformafterupdate|1
showvalue|email|E-Mail / Login:

text|firstname|Vorname:
validate|empty|firstname|Bitte geben Sie Ihren Vornamen ein.
text|name|Nachname:
validate|empty|name|Bitte geben Sie Ihren Namen ein.

action|showtext|<div class="alert alert-success">Profildaten wurden aktualisiert</div>|||1
action|ycom_auth_db
```

3. Im REDAXO-Backend unter `YCom` > `Einstellungen` den Artikel unter `Profil ändern` verknüpfen.
4. Den Artikel für den Nutzer zugänglich verlinken, z.B. in der Navigation oder im Header.

> Hinweis: Über den Formularcode-Generator in YForm kann man die Pipe-Schreibweise des Formulars auch für andere Felder des Nutzers abrufen.

> Hinweis: Wird als Login nicht die E-Mail-Adresse herangezogen, sondern der Nutzername, lautet die 3. Zeile nicht `showvalue|email|E-Mail / Login:`, sondern `showvalue|login|E-Mail / Login:`.