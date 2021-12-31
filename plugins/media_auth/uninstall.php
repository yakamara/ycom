<?php

/**
 * @var rex_addon $this
 * @psalm-scope-this rex_addon
 */

rex_sql_table::get(rex::getTable('media'))
    ->removeColumn('ycom_auth_type')
    ->removeColumn('ycom_group_type')
    ->removeColumn('ycom_groups')
    ->alter();
