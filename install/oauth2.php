<?php

/**
 * OAuth 2.0 provider settings for ycom's `ycom_auth_oauth2` field.
 *
 * Copied to `var/data/addons/ycom/oauth2.php` on install; edit that copy, not this template.
 * Every key is passed straight to League\OAuth2\Client\Provider\GenericProvider.
 *
 * Leave `redirectUri` empty at first: rendering the field with yform's debug output on then prints
 * the URL to register with the provider.
 */

$settings = [
    // Issued to you by the provider.
    'clientId' => '',
    'clientSecret' => '',

    // Must match exactly what is registered with the provider. Ends with
    // `?rex_ycom_auth_mode=oauth2&rex_ycom_auth_func=code`.
    'redirectUri' => '',

    // The provider's endpoints.
    'urlAuthorize' => '',
    'urlAccessToken' => '',
    'urlResourceOwnerDetails' => '',

    // Optional: the scopes to request, and the separator the provider expects between them.
    // 'scopes' => ['openid', 'profile', 'email'],
    // 'scopeSeparator' => ' ',
];
