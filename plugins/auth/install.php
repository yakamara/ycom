<?php

rex_config::set('ycom', 'auth_request_name', 'rex_ycom_auth_name');
rex_config::set('ycom', 'auth_request_psw', 'rex_ycom_auth_psw');
rex_config::set('ycom', 'auth_request_stay', 'rex_ycom_auth_stay');
rex_config::set('ycom', 'auth_request_ref', 'rex_ycom_auth_ref');
rex_config::set('ycom', 'auth_request_logout', 'rex_ycom_auth_logout');
rex_config::set('ycom', 'auth_request_id', 'rex_ycom_auth_id');

rex_config::set('ycom', 'auth_cookie_ttl', '14');
rex_config::set('ycom/auth', 'auth_rule', 'login_try_5_pause');

rex_config::set('ycom', 'login_field', 'email');

rex_sql_table::get(rex::getTable('article'))
    ->ensureColumn(new rex_sql_column('ycom_auth_type', 'int', false, '0'))
    ->alter();

// termofuse -> termsofuse. Version < 3.0
try {
    rex_sql::factory()
    ->setDebug()
    ->setQuery('delete from `' . rex_yform_manager_field::table() . '` where `table_name`="rex_ycom_user" and `type_id`="value" and `type_name`="checkbox" and `name`="termofuse_accepted"', [])
    ->setQuery('update `rex_config` set `key`="article_id_jump_termsofuse" where `key`="article_id_jump_termofuse" and `namespace`="ycom/auth"', [])
    ->setQuery('alter table `rex_ycom_user` drop if exists `termofuse_accepted`', [])
    ;
} catch (rex_sql_exception $e) {
   // dump($e);
}

rex_delete_cache();
