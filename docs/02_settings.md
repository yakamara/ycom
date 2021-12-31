# Einstellungen

Die Einstellungsseite befindet sich im REDAXO-Backend unter `YCom` > `Einstellungen`.

> Hinweis: Nicht alle hier getätigen Einstellungen funktionieren von Haus aus und erfordern ggf. noch weitere Einstellungen in der Struktur, Tempaltes, Modulen oder E-Mail-Tempaltes.

Weiteleitungen          | Erläuterung
----------------------- | ------------
article_id_jump_ok      | Ziel-Artikel, zu dem weitergeleitet wird, wenn der Login erfolgreich war.
article_id_jump_not_ok  | Ziel-Artikel, zu dem weitergeleitet wird, wenn der Login fehlgeschlagen ist. Dies kann auch der Login-Artikel sein. (nur für SAML-Authentifizierung)
article_id_jump_logout  | Ziel-Artikel, zu dem weitergeleitet wird, nachdem Logout erfolgt ist. Dies kann auch der Login-Artikel sein.
article_id_jump_denied  | Ziel-Artikel, zu dem weitergeleitet wird, wenn der Besucher nicht die passende Gruppen-Berechtigung hat. Z.B., wenn der Benutzer nicht eingeloggt ist oder keine passende Gruppenberechtigung hat. Dies kann auch der Login-Artikel sein.
article_id_jump_password| (optional) Artikel, in dem das Passwort geändert wird.
article_id_jump_termsofuse| (optional) Artikel, zu dem der Nutzer weitergeleitet wird, wenn der Besucher die Nutzungsbedingungen akzeptiert hat.

Allgemeine Seiten       | Erläuterung
----------------------- | ------------
Login-Seite             | Kategorie / Artikel, der das Login-Formular enthält.
Register-Seite          | (optional) Kategorie / Artikel, der das Registrier-Formular enthält.
Passwort zurücksetzen   | (optional) Kategorie / Artikel, der das Passwort-ändern-Formular enthält.

Login                   | Erläuterung
----------------------- | ------------
Login-Feld              | Feld, das für den Login berücksichtigt werden soll, z.B. `email` (E-Mail-Adresse des Nutzers) oder `login` (Pseudonym des Nutzers).

Sicherheit              | Erläuterung
----------------------- | ------------
Authentifizierungsregel | Regel, mit der Bruteforce-Attacken unterbunden werden, z.B. um nach 5 fehlgeschlagenen Logins den entsprechenden Nutzer für 15 Minuten zu sperren.

