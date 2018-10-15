# MediaAuth-Plugin

Mit dem MediaAuth-Plugin kann man einzelne Medien des Medienpools schützen, so dass diese im Frontend nicht mehr, oder nur unter bestimmten Vorraussetzungen aufgerufen werden können.

Dazu ist das YRrewrite-Plugin nötig, welches dafür sorgt, dass alle Medien entsprechend durch die MediaAuth Überprüfung läuft. Technisch, wird der (direkte) Aufruf einer Medienpooldatei an den MediaManager weitergeleitet, eventuelle Effekte werden ausgeführt und gecacht und vor der Auslieferung überprüft MediaAuth die Rechte.

Sobald man im Backend eingeloggt ist hat direkt Zugriff zu allen Medien, deswegen bei Tests immer darauf achten, dass man im Backend ausgeloggt ist.

* Aktiviertes YRewrite
* MediaAuth aktivieren
* Den Dateien entsprechende Rechte im Medienpool vergeben.


