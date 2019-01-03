# Navigationen 


### Benutzung der REDAXO Navigation

Damit die Userberechtigungen in der Navigation berücksichtigt werden muss die REDAXO Klasse rex_navigation::factory ergänzt werden.

```
$nav = rex_navigation::factory();
$nav->addCallback('rex_ycom_auth::checkPerm');
$nav->show();
```

die Klasse `rex_ycom_navigation::factory();` existiert seit Version 3 nicht mehr.

### Meta-User-Navigation

Beispiel für die Entwicklung einer Meta-User-Navigation:

```
<?php

// please change
$profile_article_id = 1;
$login_article_id = 1;
$register_article_id = 1;

$ycom_user = rex_ycom_auth::getUser();
if ($ycom_user) {
    echo '
		<div class="usernavi">
			<ul>
				<li class="login">
					<a href="'. rex_getUrl($profile_article_id) .'" title="{{ Profile }}" ><span>{{ Profile }}</span></a>
				</li>
		        <li class="registrierung">
					<a href="'. rex_getUrl('', '', ['rex_ycom_auth_logout' => 1]) .'" title="{{ Logout }}"><span>'.$ycom_user->getValue('firstname').' '.$ycom_user->getValue('name').' - {{ Logout }}</span></a>
		        </li>
		    </ul>
		</div>';
} else {
    echo '
		<div class="usernavi">
			<ul>
				<li class="login">
					<a href="'. rex_getUrl($login_article_id) .'" title="{{ Login }}"><span>{{ Login }}</span></a>
				</li>
		        <li class="registrierung">
					<a href="'. rex_getUrl($register_article_id) .'" title="{{ Registrierung }}"><span>{{ Register }}</span></a>
		        </li>
		    </ul>
		</div>';
}
```