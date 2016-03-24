<?php

rex_config::set('ycom', 'auth_request_name', 'rex_ycom_auth_name');
rex_config::set('ycom', 'auth_request_ref', 'rex_ycom_auth_ref');
rex_config::set('ycom', 'auth_request_logout', 'rex_ycom_auth_logout');
rex_config::set('ycom', 'auth_request_psw', 'rex_ycom_auth_psw');
rex_config::set('ycom', 'auth_request_id', 'rex_ycom_auth_id');
rex_config::set('ycom', 'auth_request_stay', 'rex_ycom_auth_stay');


rex_config::set('ycom', 'auth_cookie_ttl', '14');

rex_sql_table::get(rex::getTable('article'))
    ->ensureColumn(new rex_sql_column('ycom_auth_type', "ENUM('0','1','2','3')", false, '0'))
    ->alter();

rex_delete_cache();

