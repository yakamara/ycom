<?php

$settings = [
    'clientId' => 'someJibberish',    // Go to https://github.com/settings/apps and setup a Github app. Fill out, add return url, set scopes in Account permissions: Email addresses => Read only, Profile => Read and write
    'clientSecret' => 'moreJibbereish',   // In your project at https://github.com/settings/apps, click on your created project and then "Generate a new client secret".
    'redirectUri' => 'https://your-url.com/maybe-a-subpage/?rex_ycom_auth_mode=oauth2_google&rex_ycom_auth_func=code', // do not fill out first and wait for the login error message to fill it out
];
