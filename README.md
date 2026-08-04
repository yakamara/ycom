# ycom für REDAXO 6

Frontend-Benutzerverwaltung: Login, Registrierung, Profile, Tokens, Zwei-Faktor-Authentifizierung,
Artikel- und Medienrechte.

Das ist ycom für REDAXO 6 (Composer-Paket `yakamara/ycom`, Namespace `Yakamara\YCom`,
Branch `5.x`), portiert aus dem REDAXO-5-AddOn `ycom` 4.4.4. Die REDAXO-5-Version liegt auf `main`
und wird nicht an Ort und Stelle aktualisiert – siehe
[Was sich gegenüber REDAXO 5 geändert hat](#was-sich-gegenüber-redaxo-5-geändert-hat).

## Funktionen

- Login und Logout im Frontend, mit Rücksprung auf die ursprünglich angeforderte Seite
- Registrierung mit Bestätigung per E-Mail-Token
- Profilverwaltung und Passwortänderung, Passwort-vergessen per Token
- Benutzergruppen mit eigener Verwaltung
- **Artikelrechte**: je Artikel und Sprache festlegen, ob er für alle, nur für angemeldete, nur für
  abgemeldete Besucher oder nur für bestimmte Gruppen sichtbar ist – vererbbar über die Struktur
- **Medienschutz**: Medienkategorien für Gruppen sperren, optional zuschaltbar
- Zwei-Faktor-Authentifizierung per TOTP (Authenticator-App)
- Externe Authentifizierung über SAML 2.0 und OAuth2
- Session-Übersicht, Token-Verwaltung, Login-Log und „als dieser Benutzer anmelden" im Backend
- Alle Formulare als YForm-Felder, das Markup über Fragmente pro Projekt überschreibbar

## Voraussetzungen

- REDAXO 6
- PHP 8.5 oder neuer
- YForm und YRewrite (werden als Abhängigkeit mitinstalliert)

## Installation

```bash
composer require yakamara/ycom
php bin/console addon:install ycom
```

Die Installation legt die Tabellen `rex_ycom_user`, `rex_ycom_group`, `rex_ycom_user_session` und
`rex_ycom_user_token` an, ergänzt die Rechtespalten in `rex_article` und `rex_media_category` und
richtet die YForm-Tabellenverwaltung für Benutzer und Gruppen ein. `uninstall()` nimmt all das
zurück.

Nach der Installation unter **ycom → Einstellungen** die Artikel für Login, Logout, Profil,
Registrierung und Passwort hinterlegen. Eine fertige Seitenstruktur bringt ycom nicht mit; die
Doku-Kapitel *Login, Logout, Profil, Registrierung* und *Statusbeispiele* zeigen die
Formulardefinitionen zum Kopieren.

## Rechte auf Artikel

Die Rechte hängen an zwei Spalten in `rex_article` und wirken pro Sprache:

| `ycom_auth_type` | Bedeutung |
|---|---|
| `0` | erbt von der übergeordneten Kategorie, sonst offen |
| `1` | nur angemeldete Besucher |
| `2` | nur abgemeldete Besucher – für Login- und Registrierungsseiten |
| `3` | ausdrücklich für alle |

Mit `ycom_group_type = 2` lässt sich zusätzlich auf die in `ycom_groups` genannten Gruppen
einschränken. Gesetzt werden die Werte im Backend beim Bearbeiten eines Artikels im Seitenpanel
*Community Rechte*. Ob eine Seite
für den aktuellen Besucher sichtbar ist, beantwortet im Code
`Yakamara\YCom\Auth\Auth::articleIsPermitted($article)`. Die Core-Navigation lässt sich damit direkt
filtern:

```php
$nav = Redaxo\Core\View\Navigation::factory();
$nav->addCallback('\Yakamara\YCom\Auth\Auth::articleIsPermitted');
```

> Eine frische Installation setzt nichts davon automatisch: alle Artikel stehen auf `0`, sind also
> für jeden sichtbar.

## Zugriff im Code

`Auth::getUser()` liefert den angemeldeten Benutzer oder `null`:

```php
use Yakamara\YCom\Auth\Auth;

$user = Auth::getUser();

if (null !== $user) {
    echo $user->getValue('name');
    echo $user->getValue('email');
}
```

## Dokumentation

Die vollständige Dokumentation liegt im AddOn unter **ycom → Dokumentation** und als Markdown in
`docs/`.

## Was sich gegenüber REDAXO 5 geändert hat

### Keine Plugins mehr

REDAXO 6 kennt kein Plugin-Konzept. Die drei R5-Plugins sind Teil des AddOns:

| REDAXO 5 | REDAXO 6 |
|---|---|
| System-Plugin `auth` | `src/Auth/` — immer aktiv |
| System-Plugin `group` | `src/Group/` — immer aktiv, die Gruppenspalten existieren immer |
| Plugin `media_auth` | `src/MediaAuth/` — über die Config `media_auth` schaltbar (Standard: aus) |

Damit wandert auch die **Konfiguration**: R5 legte die Auth-Einstellungen im Config-Namespace des
Plugins ab (`ycom/auth`), die Medien-Einstellungen unter `ycom/media_auth`. Alles liegt jetzt unter
`ycom`.

### Klassennamen

`rex_ycom_*` → `Yakamara\YCom\*`:

| REDAXO 5 | REDAXO 6 |
|---|---|
| `rex_ycom` | `Yakamara\YCom\YCom` |
| `rex_ycom_config` | `Yakamara\YCom\Config` |
| `rex_ycom_user` | `Yakamara\YCom\User` |
| `rex_ycom_log` | `Yakamara\YCom\Log` |
| `rex_ycom_auth` | `Yakamara\YCom\Auth\Auth` |
| `rex_ycom_auth_rules` | `Yakamara\YCom\Auth\AuthRules` |
| `rex_ycom_user_session` | `Yakamara\YCom\Auth\UserSession` |
| `rex_ycom_user_token` | `Yakamara\YCom\Auth\UserToken` |
| `rex_ycom_injection_*` | `Yakamara\YCom\Auth\Injection\*` |
| `rex_ycom_otp_*` | `Yakamara\YCom\Auth\Otp\*` |
| `rex_ycom_group` | `Yakamara\YCom\Group\Group` |
| `rex_ycom_media_auth` | `Yakamara\YCom\MediaAuth\MediaAuth` |

Die **YForm-Feldnamen bleiben unverändert** (`ycom_auth_password`, `ycom_auth_returnto`,
`ycom_user_token`, …) — sie stehen in `rex_yform_field.type_name` und in handgeschriebenen
Formulardefinitionen. Registriert werden sie über `#[AsValue]` / `#[AsValidate]` / `#[AsAction]`.

Ein aus R5 übernommenes Tableset muss allerdings angepasst werden, wenn es PHP-Callables enthält:
das `status`-Feld holt seine Auswahl aus `Yakamara\YCom\User::getStatusOptions` statt aus
`rex_ycom_user::getStatusOptions`. Das mitgelieferte Tableset ist entsprechend gesetzt.

### Feld-Templates sind Fragments

REDAXO 6 hat kein `ytemplates`-Verzeichnis mehr — YForm rendert über Core-Fragments. Die R5-Templates
liegen jetzt unter `fragments/yform/value/{backend,frontend}/`:

| REDAXO 5 | REDAXO 6 |
|---|---|
| `ytemplates/bootstrap/value.ycom_password.tpl.php` | `fragments/yform/value/backend/ycom_password.php` |
| `…value.ycom_auth_otp_setup.tpl.php` | `…/ycom_auth_otp_setup.php` |
| `…value.ycom_auth_otp_verify.tpl.php` | `…/ycom_auth_otp_verify.php` |

Damit YForm die Templates eines fremden AddOns findet, meldet `boot()` das Fragment-Verzeichnis an:

```php
Yakamara\YForm\View\Fragment::addSearchPath($this->getPath('fragments/'));
```

### Externe Authentifizierung

SAML und OAuth2 sind portiert, CAS nicht (siehe „Offene Punkte").

| REDAXO 5 | REDAXO 6 |
|---|---|
| `rex_yform_trait_value_auth_extern` (Trait) | `YForm\Value\AbstractExternalAuth` (abstrakte Klasse) |
| `rex_yform_value_ycom_auth_saml` | `YForm\Value\AuthSaml` — Feldname `ycom_auth_saml` |
| `rex_yform_value_ycom_auth_oauth2` | `YForm\Value\AuthOAuth2` — Feldname `ycom_auth_oauth2` |

Die Abhängigkeit wandert von `onelogin/php-saml ^3` (abgekündigt) auf **`^4.3`**. Die von ycom
genutzte API ist unverändert; v4 bringt PHP-8.x-Kompatibilität und Sicherheitsfixes.
OAuth2 nutzt weiterhin `league/oauth2-client ^2`.

Konfiguration wie in R5 in `var/data/addons/ycom/{saml,oauth2}.php`; `install/` enthält die
kommentierten Vorlagen. Ein Formularfeld mit unvollständiger Konfiguration zeigt mit aktivierter
yform-Debug-Ausgabe die Daten an, die beim Identity Provider hinterlegt werden müssen.

Drei Härtungen gegenüber R5:

- **RelayState wird geprüft.** R5 gab `$_POST['RelayState']` direkt an `Auth::redirectTo()` — ein
  Open Redirect, da der Wert über den Identity Provider vom Browser zurückkommt. Er läuft jetzt durch
  `Auth::getReturnTo()`, also dieselbe Domain-Allowlist wie ein `returnTo`.
- **Die IdP-Metadaten werden über eine verifizierte TLS-Verbindung geholt.** `parseRemoteXML()`
  validiert das Peer-Zertifikat standardmäßig nicht; die Metadaten enthalten aber das
  Signaturzertifikat des IdP, mit dem sich Assertions fälschen ließen. Abschaltbar über
  `idp.metadataValidatePeer`.
- **Der OAuth2-State wird mit `hash_equals()` verglichen und nur einmal akzeptiert.** R5 verglich mit
  `!=` und ließ den State in der Session stehen.

Weiterhin: URLs für Identity Provider werden absolut gebildet.
`YRewrite::getFullUrlByArticleId()` liefert für die Default-Domain einen *relativen* Pfad — ein alter
yrewrite-Eigenheit, den R5 genauso hatte. SAML-Endpunkte, RelayState und die OAuth2-Redirect-URI
müssen absolut sein, daher `AbstractExternalAuth::toAbsoluteUrl()`.

### Weitere Verhaltensänderungen

- **„Als dieser User anmelden"** braucht `Auth::createSessionForUser()`. REDAXO 6 trennt Backend- und
  Frontend-Session in getrennte Namespaces derselben HTTP-Session; R5 hatte einen gemeinsamen.
- **Fehlgeschlagene Logins** loggen nicht mehr das komplette `$_SERVER` und `$_REQUEST` in eine
  Klartextdatei, sondern nur IP, User-Agent, Request-URI und Referer.
- **Der Medienschutz** hängt sich auf `PACKAGES_INCLUDED` mit `ExtensionLevel::Early` ein, weil
  REDAXO 6 den `MEDIA_MANAGER_BEFORE_SEND`-Extension-Point nicht mehr hat. Das funktioniert, weil
  Core seine eigenen `#[AsExtension]`-Hooks erst *nach* allen `boot()`-Aufrufen registriert.
- **`ycom_auth_returnto`** leitet über `Response::sendRedirect()` weiter. Ein per
  `Response::setHeader()` vorgemerkter Header ginge verloren, weil YForms `form_exit` mit
  `cleanOutputBuffers(); exit;` abbricht, ohne die Header-Queue zu leeren.
- **Die OTP-Seiten sind übersetzt.** In R5 gaben sie wörtlich `{ ycom_otp_setup_title }` aus, weil die
  Keys in keiner Lang-Datei definiert waren.

### Kein Migrationspfad aus REDAXO 5

Das AddOn ist auf einen frischen REDAXO 6 ausgelegt und trägt keine R5-Rückwärtskompatibilität mit.
Bewusst nicht enthalten:

- Übernahme der Konfiguration aus den Plugin-Namespaces `ycom/auth` / `ycom/media_auth`
- die Spaltenumbenennung `termofuse_accepted` → `termsofuse_accepted` (ycom < 3)
- die Umrechnung des `ycom_auth_type`-ENUM auf `int` (ycom < 4)
- das Umschreiben der in `rex_yform_field.choices` gespeicherten R5-Callables
- `activation_key` samt Unique-Validierung: laut ycoms eigener Doku durch Tokens ersetzt
- `Auth::getRequestKey()` / `DEFAULT_REQUEST_KEYS` — die Config-Keys wurden nie gesetzt, R5s eigene
  Nutzung war auskommentiert
- `YCom::parseText()` / `YCom::cut()` — ungenutzte Texthelfer; `parseText()` gab Eingaben zudem
  unescaped als HTML aus
- CAS in allen Spielarten (siehe unten)

Die mitgelieferte Dokumentation ist auf die REDAXO-6-API umgestellt — sie zeigte durchgehend
`rex_ycom_auth::`, `rex_getUrl()` und Ähnliches, was in REDAXO 6 nicht mehr existiert.

Wer aus REDAXO 5 migriert, braucht diese Schritte einmalig als eigene Migration.

## Offene Punkte

- **CAS ist nicht portiert.** `dergel/phpcas ^1` ist unter PHP 8.5 ungeprüft, und der R5-Code für CAS
  nutzt den gemeinsamen Trait gar nicht, sondern dupliziert dessen Logik. Der Extension Point
  `YCOM_AUTH_CAS_MATCHING` wird weiterhin ausgelöst, die Vorlage `install/cas.php` ist entfernt.
- **SAML und OAuth2 sind nicht gegen einen echten Identity Provider getestet.** Geprüft ist alles bis
  zur Grenze zum Provider: Feldregistrierung, Konfigurationsfehler-Pfade, der erzeugte AuthnRequest
  (dekodiert: Destination, ACS-URL, Issuer, RelayState), der Redirect zum Authorize-Endpunkt, die
  State-Prüfung in beiden Richtungen samt Replay-Schutz. Ungetestet bleiben `acs` / `sls` und der
  Token-Tausch, weil dafür ein IdP nötig ist.
- **Kein Test-Suite-Port.** Das R5-AddOn hat `tests/`; hier gibt es noch keine.
