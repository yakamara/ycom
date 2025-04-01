<?php

/**
 * @var rex_addon $this
 * @psalm-scope-this rex_addon
 */

// Update from Version < 4
$articleAuthTypeWasEnum = false;
$articleTable = rex_sql_table::get(rex::getTable('article'));
if ($articleTable->hasColumn('ycom_auth_type')) {
    $Column = $articleTable->getColumn('ycom_auth_type');
    if (null !== $Column && 'enum' === substr($Column->getType(), 0, 4)) {
        $articleAuthTypeWasEnum = true;
    }
}

rex_sql_table::get(rex::getTable('article'))
    ->ensureColumn(new rex_sql_column('ycom_auth_type', 'int', false, '0'))
    ->alter();

rex_sql_table::get(rex::getTable('ycom_user_token'))
    ->ensureColumn(new rex_sql_column('hash', 'varchar(255)'))
    ->ensureColumn(new rex_sql_column('user_id', 'int(10) unsigned', true))
    ->ensureColumn(new rex_sql_column('email', 'varchar(255)'))
    ->ensureColumn(new rex_sql_column('type', 'varchar(255)'))
    ->ensureColumn(new rex_sql_column('selector', 'varchar(255)'))
    ->ensureColumn(new rex_sql_column('createdate', 'datetime'))
    ->ensureColumn(new rex_sql_column('expiredate', 'datetime'))
    ->setPrimaryKey('hash')
    ->ensureIndex(new rex_sql_index('token', ['selector'], rex_sql_index::UNIQUE))
    ->ensureForeignKey(
        new rex_sql_foreign_key(
            'ycom_user_token_id',
            rex::getTable('ycom_user'),
            ['user_id' => 'id'],
            rex_sql_foreign_key::CASCADE,
            rex_sql_foreign_key::CASCADE,
        ),
    )
    ->ensure();

rex_sql_table::get(rex::getTable('ycom_user_session'))
    ->ensureColumn(new rex_sql_column('session_id', 'varchar(255)'))
    ->ensureColumn(new rex_sql_column('user_id', 'int(10) unsigned'))
    ->ensureColumn(new rex_sql_column('ip', 'varchar(39)')) // max for ipv6
    ->ensureColumn(new rex_sql_column('useragent', 'varchar(255)'))
    ->ensureColumn(new rex_sql_column('starttime', 'datetime'))
    ->ensureColumn(new rex_sql_column('last_activity', 'datetime'))
    ->ensureColumn(new rex_sql_column('last_activity', 'datetime'))
    ->ensureColumn(new rex_sql_column('otp_verified', 'tinyint(1)', false, '0'))
    ->ensureColumn(new rex_sql_column('cookie_key', 'varchar(255)', true))
    ->ensureIndex(new rex_sql_index('cookie_key', ['cookie_key'], rex_sql_index::UNIQUE))
    ->setPrimaryKey('session_id')
    ->ensureForeignKey(
        new rex_sql_foreign_key(
            'ycom_user_session_id',
            rex::getTable('ycom_user'),
            ['user_id' => 'id'],
            rex_sql_foreign_key::CASCADE,
            rex_sql_foreign_key::CASCADE,
        ),
    )
    ->ensure();

// Update from Version < 4
if ($articleAuthTypeWasEnum) {
    rex_sql::factory()->setQuery('UPDATE rex_article SET `ycom_auth_type` = `ycom_auth_type` -1');
}

foreach (['saml', 'oauth2', 'cas'] as $settingType) {
    $pathFrom = __DIR__ . '/install/' . $settingType . '.php';
    $pathTo = rex_addon::get('ycom')->getDataPath($settingType . '.php');
    if (!file_exists($pathTo)) {
        rex_file::copy($pathFrom, $pathTo);
    }
}

// termofuse -> termsofuse. Version < 3.0
try {
    rex_sql_table::get(rex::getTablePrefix() . 'ycom_user')
        ->ensureColumn(new rex_sql_column('termsofuse_accepted', 'tinyint(1)', false, '0'))
        ->removeColumn('session_key')
        ->alter();

    rex_sql::factory()
//        ->setQuery('alter table `' . rex::getTablePrefix().'ycom_user' . '` drop if exists `termofuse_accepted`', [])
        ->setQuery('delete from `' . rex_yform_manager_field::table() . '` where `table_name`="rex_ycom_user" and `type_id`="value" and `type_name`="checkbox" and `name`="termofuse_accepted"', [])
        ->setQuery('update `rex_config` set `key`="article_id_jump_termsofuse" where `key`="article_id_jump_termofuse" and `namespace`="ycom/auth"', [])
        ->setQuery('delete from `' . rex_yform_manager_field::table() . '` where `table_name`="rex_ycom_user" and `type_id`="value" and `type_name`="generate_key" and `name`="session_key"');
} catch (rex_sql_exception $e) {
    dump($e);
    exit;
}
