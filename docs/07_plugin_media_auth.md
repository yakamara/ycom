# MediaAuth-Plugin

Mit dem MediaAuth-Plugin werden Medien des Medienpools geschützt, sodass diese im Frontend nur unter bestimmten Vorraussetzungen aufgerufen werden können, bspw. nur im eingeloggten Zustand und nur für bestimmte YCom-Gruppen.

Dazu ist das YRrewrite-Plugin `media_auth` nötig, das alle Medien auf ihre Berechtigung überprüft. 

> **Technische Erläuterung:** Anstelle eines direkten Aufrufs, bspw. `/media/meine_-_datei.pdf` werden die Aufrufe durch eine `.htaccess`-Regel über den **Media Manager** geleitet. Dort sorgt ein Authentifzierungs-Effekt dafür, dass die Berechtigungen des Besuchers überprüft werden und ggf die Auslieferung verweigert wird.

> **Achtung:** Sofern man als Redakteur/Administrator im REDAXO-Backend eingeloggt ist, hat man immer Zugriff zu allen Medien. Daher empfehlen deswegen bei Tests immer darauf achten, dass man im Backend ausgeloggt ist.

## Einrichtung

1. Das Addon `YRewrite` installieren, aktivieren und das Setup ausführen
2. Das YCom-Plugin `media_auth` aktivieren
3. Unter YCom / Einstellungen unter dem Tab "Medien" Authentifizierung aktivieren
4. Im Medienpool den Dateien entsprechende Rechte geben.

## Fehlerbehebung

* **Das Schützen der Datei funktioniert nicht**: Sicherstellen, dass YRewrite installiert ist, aktiviert ist und die `.htaccess` im Hauptverzeichnis folgende Zeile enthält: 
`RewriteRule ^media/(.*) %{ENV:BASE}/index.php?rex_media_type=default&rex_media_file=$1&%{QUERY_STRING} [B]`

* **Bestimmte Dateitypen werden im Frontend nicht mehr korrekt dargestellt / heruntergeladen**: Bitte hier melden: [YRewrite GitHub](https://github.com/yakamara/redaxo_yrewrite/issues/235)
