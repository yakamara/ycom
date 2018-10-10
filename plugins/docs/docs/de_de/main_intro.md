# YCom: Einführung


<h3>Allgemeines</h3>

Am Anfang des Templates sollte stehen:

<pre>$ycom_user = rex_ycom_auth::getUser()</pre>

Somit ist es möglich wie folgt die Benutzerberechtigung zu prüfen:

<pre>if ($ycom_user) {
	// Benutzter besitzt die nötige Berechtigung
} else {
	// Benutzter besitzt die nötige Berechtigung nicht
}</pre>
In der TableManager Tabelle 'rex_ycom_user' können die Benutzerfelder erweitert werden.
Bitte die bestehenden Felder nicht ändern oder löschen.

Die Benutzerfelder können wie folgt aus der Tabelle 'rex_ycom_user' ausgelesen werden:

<pre>rex_ycom_auth::getUser()->getValue('name');</pre>


