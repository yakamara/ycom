# Medienschutz

Der Medienschutz schützt Medien des Medienpools, sodass diese im Frontend nur unter bestimmten Vorraussetzungen aufgerufen werden können, bspw. nur im eingeloggten Zustand und nur für bestimmte YCom-Gruppen.

ycom prüft dazu bei jeder Auslieferung über den Media Manager die Rechte der Datei. 

> **Technische Erläuterung:** Anstelle eines direkten Aufrufs, bspw. `/media/meine_-_datei.pdf` werden die Aufrufe durch eine `.htaccess`-Regel über den **Media Manager** geleitet. Dort sorgt ein Authentifzierungs-Effekt dafür, dass die Berechtigungen des Besuchers überprüft werden und ggf die Auslieferung verweigert wird.

> **Achtung:** Sofern man als Redakteur/Administrator im REDAXO-Backend eingeloggt ist, hat man immer Zugriff zu allen Medien. Daher empfehlen deswegen bei Tests immer darauf achten, dass man im Backend ausgeloggt ist.

## Einrichtung

1. Das Addon `YRewrite` installieren, aktivieren und das Setup ausführen
2. Unter `Community` &rsaquo; `Medienschutz` den Medienschutz auf „aktiv" stellen
3. Im Medienpool den Dateien entsprechende Rechte geben.

## optional: Mediendateien nach Upload schützen

Nutze den EP `MEDIA_ADDED`, um Medienuploads in eine bestimmte Kategorie standardmäßig zu schützen. Anbei ein Beispiel, das man an seine eigenen Anforderungen anpassen kann.

In die `boot.php` deines Addons oder des Project-Addons notieren:

```php
if (\Redaxo\Core\Core::isBackend() && \Redaxo\Core\Core::getUser()) {
    \Redaxo\Core\ExtensionPoint\Extension::register('MEDIA_ADDED', ['project','media_added'], \Redaxo\Core\ExtensionPoint\Extension::LATE);
}
```

In deinem Addon-Code oder Project-Addon notieren, hier: `lib/project.php`:

```php
<?php
class project
{
    public static function media_added($ep)
    {
        $mediafile = &$ep->getParams();
        $media = \Redaxo\Core\MediaPool\Media::get($mediafile['filename']);

        if ($media->getCategory()->getId() == 1 || $media->getCategory()->getParent()->getId() == 1) {
            $sql = \Redaxo\Core\Database\Sql::factory();
            $sql->setWhere('filename="'.$mediafile['filename'].'"');
            $sql->setTable(\Redaxo\Core\Core::getTablePrefix() . 'media');
            $sql->setValue('ycom_auth_type', 1);
            $sql->update();
        }
        $media = \Redaxo\Core\MediaPool\Media::get($mediafile['filename']);
    }
}
```

## Fehlerbehebung

* **Das Schützen der Datei funktioniert nicht**: Sicherstellen, dass YRewrite installiert ist, aktiviert ist und die `.htaccess` im Hauptverzeichnis folgende Zeile enthält: 
`RewriteRule ^media/(.*) %{ENV:BASE}/index.php?rex_media_type=default&rex_media_file=$1&%{QUERY_STRING} [B]`

* **Bestimmte Dateitypen werden im Frontend nicht mehr korrekt dargestellt / heruntergeladen**: Bitte hier melden: [YRewrite GitHub](https://github.com/yakamara/redaxo_yrewrite/issues/235)


## Überprüfung von Medien, welche im MediaManager einen eigene Pfad haben

```
\Redaxo\Core\ExtensionPoint\Extension::register(['MEDIA_MANAGER_BEFORE_SEND'], function (rex_extension_point $ep) {
    /** @var rex_media_manager $mm */
    $mm = $ep->getSubject();
    $originalMediaPath = dirname($mm->getMedia()->getSourcePath());
    $deinPfad = '.../testbilder/';
    if ($originalMediaPath == $deinPfad) {
        // eigene überprüfung
    }
}, \Redaxo\Core\ExtensionPoint\Extension::EARLY);
```
