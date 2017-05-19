<?php
rex_sql_table::get(rex::getTable('media'))
    ->ensureColumn(new rex_sql_column('ycom_auth', "ENUM('0','1')", false, '0'))
    ->ensureColumn(new rex_sql_column('ycom_groups', 'varchar(255)'))
    ->ensureColumn(new rex_sql_column('ycom_users', 'varchar(255)'))
    ->alter();

$content = rex_file::get(rex_path::plugin('ycom', 'media', 'install/tablesets/yform_media.json'));
rex_yform_manager_table_api::importTablesets($content);

rex_delete_cache();
