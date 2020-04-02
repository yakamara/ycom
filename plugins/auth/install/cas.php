<?php

$settings = [
    'idp' => [
        'protocol' => 'https://',
        'host' => 'xxxxxx.com',
        'uri' => '/cas',
        'port' => 443,
        'CasServerValidation' => false,
        'CasServerCACertPath' => rex_addon::get('ycom')->getDataPath('cas_cert.pem'),
        'ServerVersion' => '2.0', // '2.0' => CAS_VERSION_2_0, '3.0' => CAS_VERSION_3_0, 'S1' => SAML_VERSION_1_1
    ],
    'debug' => false,
    'debugPath' => rex_path::log('ycom_auth_cas.log'),
];
