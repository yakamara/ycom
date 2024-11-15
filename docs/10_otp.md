# OTP (OneTimePassword) Authentifizierung (2FA)

## Allgemeine Konfiguration

Zunächst muss die Konfiguration in den YCom Einstellungen vorgenommen werden. Die entsprechenden Artikel für das OTP Setup und die OTP Überprüfung müssen angelegt werden und über die YCom Permission nur für eingeloggte User verfügbar gemacht werden.

## Benötigte Artikel

Es wird 1 REDAXO Artikel benötigt. Dieser führt das initale SetUp und auch die Verifizierung durch. Dieser Artikel muss in der Einstellungseite verlinkt sein

### Artikel für das OTP Setup

Im OTP Artikel das YForm Builder Modul verwenden und diesen YFormCode einsetzen. Rechte müssen auf "Zugriff für eingeloggte User" gesetzt sein.

```
ycom_auth_otp|setup
```

## Einleitung

Die 2-Faktor-Authentifizierung (2FA) ist eine zusätzliche Sicherheitsebene für Ihr Konto. Sie schützt Ihr Konto vor unbefugtem Zugriff, selbst wenn Ihr Passwort kompromittiert wurde.

Die 2FA ist eine Authentifizierungsmethode, bei der zwei verschiedene Faktoren verwendet werden, um die Identität einer Person zu bestätigen. 

Hier werden 2 Möglichkeiten angeboten.

* One Time Passwort über E-Mail.
* One Time Passwort über (Google) Authenticator.

Die 2FA ist eine der besten Möglichkeiten, um Ihr Konto zu schützen, da sie sicherstellt, dass nur Sie auf Ihr Konto zugreifen können, selbst wenn jemand Ihr Passwort kennt.

## Einrichtung

Die 2FA kann in Ihrem Konto aktiviert werden. Dazu müssen Sie sich zunächst anmelden und die 2FA in den Einstellungen aktivieren. Anschließend müssen Sie einen Sicherheitsschlüssel hinzufügen, der zur Authentifizierung verwendet wird.

Die 2FA kann auf verschiedene Arten aktiviert werden, z.B. durch die Eingabe eines Codes, der per SMS oder E-Mail gesendet wird, oder durch die Verwendung einer Authentifizierungs-App wie Google Authenticator oder Authy.

## Verwendung

Nachdem die 2FA aktiviert wurde, müssen Sie sich bei jedem Anmeldeversuch zusätzlich zur Eingabe Ihres Passworts auch mit einem Sicherheitsschlüssel authentifizieren. Dies kann z.B. durch die Eingabe eines Codes aus der Authentifizierungs-App erfolgen.

Die 2FA bietet eine zusätzliche Sicherheitsebene für Ihr Konto und schützt es vor unbefugtem Zugriff. Wir empfehlen daher, die 2FA zu aktivieren, um Ihr Konto zu schützen.

## Umsetzung in REDAXO

In REDAXO kann die 2FA z.B. mit dem AddOn YCom umgesetzt werden. Dazu müssen Sie zunächst die 2FA in den Einstellungen von YCom aktivieren und einen Sicherheitsschlüssel hinzufügen. Anschließend müssen Sie sich bei jedem Anmeldeversuch zusätzlich zur Eingabe Ihres Passworts auch mit einem Sicherheitsschlüssel authentifizieren.

Die 2FA bietet eine zusätzliche Sicherheitsebene für Ihr REDAXO-Konto und schützt es vor unbefugtem Zugriff. Wir empfehlen daher, die 2FA zu aktivieren, um Ihr Konto zu schützen.

## E-Mail Template

Standardmäßig wird eine vorbereitete E-Mail mit festem Text verschickt. 

Es kann aber ein eigenes YForm-Template für die 2FA verwendet. Mit dem Key ```ycom_otp_code_template``` kann ein eigenes Template gesetzt wird. Folgende Werte sind verfügbar: ```name, email, firstname, code```.
