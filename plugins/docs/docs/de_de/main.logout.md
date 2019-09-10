# Logout 

## Per YForm-Formbuilder

1. In der Struktur einen Artikel `Logout` erstellen
2. Im Artikel `Logout` den YForm Formbuilder hinzufügen und folgende Formulardefinition eintragen:

```
ycom_auth_form_logout|label|
```

3. Im REDAXO-Backend unter `YCom` > `Einstellungen` den Artikel unter `Logout` verknüpfen.
4. Den Artikel für den Nutzer zugänglich verlinken, z.B. in der Navigation oder im Header.

Nun können sich Nutzer nach einem Login ausloggen.

## Per Redirect

```rex_redirect(rex_plugin::get('ycom', 'auth')->getConfig('article_id_jump_logout'), 'REX_CLANG_ID', array("rex_ycom_auth_logout" => 1)); ```

Hierbei wird der User abgemeldet und zur "Ausgeloggt-Seite" weitergeleitet. 

