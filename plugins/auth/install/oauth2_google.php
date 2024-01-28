<?php

$settings = [
    'clientId' => 'someJibberish',    // Go to https://console.cloud.google.com/ and setup a project. Setup an OAuth consent screen and choose scopes
    'clientSecret' => 'moreJibbereish',   // In your project at https://console.cloud.google.com/, click on "Credential" and then "Create credentials => OAuth client ID". Fill out, add return url, get ID and secret
    'redirectUri' => 'https://your-url.com/maybe-a-subpage/?rex_ycom_auth_mode=oauth2_google&rex_ycom_auth_func=code', // do not fill out first and wait for the login error message to fill it out
    // 'hostedDomain' => 'your-url.com', // optional; used to restrict access to users on your G Suite/Google Apps for Business accounts
];
