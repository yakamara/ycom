<?php

rex_sql_table::get(rex::getTable('media'))
    ->removeColumn('ycom_auth_type')
    ->removeColumn('ycom_group_type')
    ->removeColumn('ycom_groups')
    ->alter();
