# Logout 

## Per YForm-Formbuilder

1. In der Struktur einen Artikel `Logout` erstellen
2. Im Artikel `Logout` den YForm Formbuilder hinzufügen und folgende Formulardefinition eintragen:

```
ycom_auth_logout|label|
```

3. Im REDAXO-Backend unter `YCom` > `Einstellungen` den Artikel unter `Logout` verknüpfen.
4. Den Artikel für den Nutzer zugänglich verlinken, z.B. in der Navigation oder im Header.

Nun können sich Nutzer nach einem Login ausloggen und werden dann weitergeleitet.

### Ohne YForm-Formbuilder
Alternativ kann ein Logout mithilfe der Funktion `rex_ycom_auth::clearUserSession()` ausgeführt werden. 
