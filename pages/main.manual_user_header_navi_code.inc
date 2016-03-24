<?php
/*

	Text innerhalb von {{ }} ist als Platzhalter zu verstehen.
	Die ID's 72 und 73 in diesem Beispiel sind mit den richtigen Artikel IDs zu ersetzen
	
	Im Template muss (vorher) irgendwo $com_user = rex_ycom_auth::getUser(); vorhanden sein.
	
*/
	
if ($ycom_user) {
	echo '
		<div class="usernavi">
			<ul>
				<li class="login">
					<a href="'. rex_getUrl(rex_addon::get("ycom")->getConfig("article_login_ok")) .'" title="{{ Persönlicher Bereich }}" ><span>{{ Persönlicher Bereich }}</span></a>
				</li>
		        <li class="registrierung">
					<a href="'. rex_getUrl(rex_addon::get("ycom")->getConfig("article_logout")) .'?rex_ycom_auth_logout=1" title="{{ Logout }}"><span>'.rex_ycom_auth::getUser()->getValue('firstname').' '.rex_ycom_auth::getUser()->getValue('name').' - {{ Logout }}</span></a>
		        </li>
		    </ul>
		</div>';		
} else {
	echo '
		<div class="usernavi">
			<ul>
				<li class="login">
					<a href="'. rex_getUrl(72) .'" title="{{ Login }}"><span>{{ Login }}</span></a> 
				</li>
		        <li class="registrierung">
					<a href="'. rex_getUrl(73) .'" title="{{ Registrierung }}"><span>{{ Registrierung }}</span></a>
		        </li>
		    </ul>
		</div>';
}
}?>