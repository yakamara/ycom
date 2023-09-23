<?php

$settings = [
    'clientId' => 'someJibberish',    // Go to https://dev.twitch.tv/console/apps and create an app. The client ID from your app
    'clientSecret' => 'moreJibbereish',   // In your app at https://dev.twitch.tv/console/apps, click on "Manage" and then "New Secret". The client password from your app
    'redirectUri' => 'https://your-url.com/maybe-a-subpage/?rex_ycom_auth_mode=oauth2_twitch&rex_ycom_auth_func=code' // do not fill out first and wait for the login error message to fill it out - add it to your allowed domains in your app at https://dev.twitch.tv/console/apps
    //'scope' => 'user:read:email,user:read:follows',
];