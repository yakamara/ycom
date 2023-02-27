<?php

/**
 * @var rex_addon $this
 * @psalm-scope-this rex_addon
 */

rex_sql_table::get(rex::getTable('article'))
    ->ensureColumn(new rex_sql_column('ycom_group_type', 'int', false, '0'))
    ->ensureColumn(new rex_sql_column('ycom_groups', 'text'))
    ->alter()
;
