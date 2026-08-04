<?php

/**
 * The "sign in with …" link of an external identity provider (`ycom_auth_saml`, `ycom_auth_oauth2`).
 *
 * Ship `ycom_auth_saml.php` / `ycom_auth_oauth2.php` next to this file to give one provider its own
 * markup — the value classes look for the provider-specific key first.
 *
 * @var \Yakamara\YForm\View\Fragment $this
 * @var \Yakamara\YForm\Value\AbstractValue $yfield
 * @var string $url the URL that starts the provider's flow
 * @var string $name the link label
 */

use function Redaxo\Core\View\escape;

extract($this->getVariables());

$url ??= '';
$name ??= '';

if ('' === $url) {
    return;
}

// Both values arrive unescaped — the label comes from I18n::rawMsg(), the URL from Url::article()
// with a returnTo that Auth::getReturnTo() has already restricted to an allowed domain. Escaping
// belongs here, in the template that emits the markup.
echo '<p class="yform-element ycom-auth-extern">'
    . '<a class="btn btn-default" href="' . escape($url) . '">' . escape($name) . '</a>'
    . '</p>';
