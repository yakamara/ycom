# YCom User Status - Beispiele

## Status-Konstanten verwenden

### Im PHP-Code

```php
<?php

// Neuen User erstellen mit bestätigtem Status
$user = \Yakamara\YCom\User::create();
$user->setValue('email', 'test@example.com');
$user->setValue('login', 'test@example.com');
$user->setValue('status', \Yakamara\YCom\User::STATUS_CONFIRMED);
$user->save();

// Status prüfen
$currentUser = \Yakamara\YCom\User::getMe();
if ($currentUser && $currentUser->getValue('status') >= \Yakamara\YCom\User::STATUS_CONFIRMED) {
    // User ist eingeloggt und bestätigt
}

// Status auf inaktiv setzen
$user->setValue('status', \Yakamara\YCom\User::STATUS_INACTIVE);
$user->save();
```

## Status-Optionen erweitern via Extension Point

### Beispiel 1: Eigenen Status hinzufügen

```php
<?php

\Redaxo\Core\ExtensionPoint\Extension::register('YCOM_USER_STATUS_OPTIONS', function (rex_extension_point $ep) {
    /** @var array<int,string> $statusOptions */
    $statusOptions = $ep->getSubject();
    
    // Premium-Status hinzufügen (Wert 3)
    $statusOptions[3] = 'translate:ycom_account_premium';
    
    // VIP-Status hinzufügen (Wert 4)
    $statusOptions[4] = 'translate:ycom_account_vip';
    
    return $statusOptions;
});
```

### Beispiel 2: Status-Optionen anpassen und sortieren

```php
<?php

\Redaxo\Core\ExtensionPoint\Extension::register('YCOM_USER_STATUS_OPTIONS', function (rex_extension_point $ep) {
    /** @var array<int,string> $statusOptions */
    $statusOptions = $ep->getSubject();
    
    // Bestimmte Stati entfernen (optional)
    unset($statusOptions[\Yakamara\YCom\User::STATUS_INACTIVE_TERMINATION]);
    
    // Eigene Stati hinzufügen
    $statusOptions[10] = 'translate:ycom_account_special';
    
    // Nach Schlüssel sortieren
    ksort($statusOptions);
    
    return $statusOptions;
});
```

### Beispiel 3: Status-Labels projektspezifisch anpassen

```php
<?php

\Redaxo\Core\ExtensionPoint\Extension::register('YCOM_USER_STATUS_OPTIONS', function (rex_extension_point $ep) {
    /** @var array<int,string> $statusOptions */
    $statusOptions = $ep->getSubject();
    
    // Standard-Labels durch eigene ersetzen
    $statusOptions[\Yakamara\YCom\User::STATUS_ACTIVE] = 'Aktives Mitglied';
    $statusOptions[\Yakamara\YCom\User::STATUS_CONFIRMED] = 'Bestätigtes Mitglied';
    $statusOptions[\Yakamara\YCom\User::STATUS_REQUESTED] = 'Registrierung ausstehend';
    
    return $statusOptions;
});
```

## Verfügbare Status-Konstanten

| Konstante | Wert | Standard-Label |
|-----------|------|----------------|
| `\Yakamara\YCom\User::STATUS_INACTIVE_TERMINATION` | -3 | Zugang wurde gekündigt |
| `\Yakamara\YCom\User::STATUS_INACTIVE_LOGINS` | -2 | Zugang wurde deaktiviert [Loginfehlversuche] |
| `\Yakamara\YCom\User::STATUS_INACTIVE` | -1 | Zugang ist inaktiv |
| `\Yakamara\YCom\User::STATUS_REQUESTED` | 0 | Zugang wurde angefragt |
| `\Yakamara\YCom\User::STATUS_CONFIRMED` | 1 | Zugang wurde bestätigt und ist aktiv |
| `\Yakamara\YCom\User::STATUS_ACTIVE` | 2 | Zugang ist aktiv |

## Status-Logik

- **Login erlaubt**: Status >= 1 (STATUS_CONFIRMED oder höher)
- **Login nicht erlaubt**: Status <= -1 (STATUS_INACTIVE oder niedriger)
- **Registriert, aber Nutzungsbedingungen fehlen**: Status = 0 (STATUS_REQUESTED)
