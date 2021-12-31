<?php

/**
 * @var rex_addon $this
 * @psalm-scope-this rex_addon
 */

rex_sql_table::get(rex::getTable('media'))
    ->ensureColumn(new rex_sql_column('ycom_auth_type', 'int(11)', false, '0'))
    ->ensureColumn(new rex_sql_column('ycom_group_type', 'int(11)', false, '0'))
    ->ensureColumn(new rex_sql_column('ycom_groups', 'text'))
    ->alter();
