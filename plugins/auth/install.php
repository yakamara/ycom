<?php

rex_config::set('ycom', 'auth_request_ref', 'returnTo');
rex_config::set('ycom', 'auth_cookie_ttl', '14');
rex_config::set('ycom/auth', 'auth_rule', 'login_try_5_pause');
rex_config::set('ycom', 'login_field', 'email');

rex_sql_table::get(rex::getTable('article'))
    ->ensureColumn(new rex_sql_column('ycom_auth_type', 'int', false, '0'))
    ->alter();

// saml dummy file
rex_file::copy(__DIR__.'/install/saml.php', rex_addon::get('ycom')->getDataPath('saml.php'));
// cas dummy file
rex_file::copy(__DIR__.'/install/cas.php', rex_addon::get('ycom')->getDataPath('cas.php'));

// termofuse -> termsofuse. Version < 3.0
try {

    rex_sql_table::get(rex::getTablePrefix().'ycom_user')
        ->ensureColumn(new rex_sql_column('termsofuse_accepted', 'tinyint(1)', false, '0'))
        ->alter();

    rex_sql::factory()
//        ->setQuery('alter table `' . rex::getTablePrefix().'ycom_user' . '` drop if exists `termofuse_accepted`', [])
        ->setQuery('delete from `' . rex_yform_manager_field::table() . '` where `table_name`="rex_ycom_user" and `type_id`="value" and `type_name`="checkbox" and `name`="termofuse_accepted"', [])
        ->setQuery('update `rex_config` set `key`="article_id_jump_termsofuse" where `key`="article_id_jump_termofuse" and `namespace`="ycom/auth"', [])
    ;

} catch (rex_sql_exception $e) {
    dump($e);
    exit;
}

rex_delete_cache();
rex_yform_manager_table::deleteCache();
