### Allgemeines

Am Anfang des Templates sollte stehen:

<pre>
<code>
$com_user = rex_ycom_auth::getUser();
</code>
</pre>

Somit ist es möglich wie folgt die Benutzerberechtigung zu prüfen:

<pre>
<code>
if ($com_user) {
	// Benutzter besitzt die nötige Berechtigung
} else {
	// Benutzter besitzt die nötige Berechtigung nicht
}
</code>
</pre>
In der TableManager Tabelle 'rex_ycom_user' können die Benutzerfelder erweitert werden.
Bitte die bestehenden Felder nicht ändern oder löschen.

Die Benutzerfelder können wie folgt aus der Tabelle 'rex_ycom_user' ausgelesen werden:

<pre>
<code>
rex_ycom_auth::getUser()->getValue('FELDNAME');
</code>
</pre>
