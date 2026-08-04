# Navigationen

## Benutzung der REDAXO Navigation

Damit die Userberechtigungen in der Navigation berücksichtigt werden muss die REDAXO Klasse \Redaxo\Core\View\Navigation::factory ergänzt werden.

```php
$nav = \Redaxo\Core\View\Navigation::factory();
$nav->addCallback('\Yakamara\YCom\Auth\Auth::articleIsPermitted');
$nav->show(0, 1, TRUE, TRUE);
```

> **Hinweis:** Die Ausgabe der Navigation kann angepasst und um Parameter erweitert werden. Details siehe in der Doku: <https://redaxo.org/doku/master/navigationen.>

## Meta-User-Navigation

Beispiel für die Entwicklung einer Meta-User-Navigation:

```php
$login_article_id = \Yakamara\YCom\Config::get('article_id_login');
$logout_article_id = \Yakamara\YCom\Config::get('article_id_logout');

// TODO: please change
$profile_article_id = 1;
$register_article_id = 1;

$ycom_user = \Yakamara\YCom\Auth\Auth::getUser();
if ($ycom_user) {
    echo '
  <div class="usernavi">
   <ul>
    <li class="login">
     <a href="'. \Redaxo\Core\Filesystem\Url::article($profile_article_id) .'" title="{{ Profile }}" ><span>{{ Profile }}</span></a>
    </li>
          <li class="registrierung">
     <a href="'. \Redaxo\Core\Filesystem\Url::article($logout_article_id) .'" title="{{ Logout }}"><span>'.$ycom_user->getValue('firstname').' '.$ycom_user->getValue('name').' - {{ Logout }}</span></a>
          </li>
      </ul>
  </div>';
} else {
    echo '
  <div class="usernavi">
   <ul>
    <li class="login">
     <a href="'. \Redaxo\Core\Filesystem\Url::article($login_article_id) .'" title="{{ Login }}"><span>{{ Login }}</span></a>
    </li>
          <li class="registrierung">
     <a href="'. \Redaxo\Core\Filesystem\Url::article($register_article_id) .'" title="{{ Registrierung }}"><span>{{ Register }}</span></a>
          </li>
      </ul>
  </div>';
}
```
