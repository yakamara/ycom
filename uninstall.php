<?php
    /**
     * Let's remove the columns from the database
     */
    $addon_cols = [
        'ycom_auth_type',
        'ycom_group_type',
        'ycom_groups'
    ];

    $table = rex::getTable('article');

    $db_cols = rex_sql::showColumns($table);
    foreach($addon_cols as $addon_col)
    {
        $found = false;
        foreach($db_cols as $db_col)
        {
            if($db_col['name'] === $addon_col)
            {
                $found = true;
                break;
            }
        }

        if($found)
        {
            $sql = rex_sql::factory();
            $sql->setQuery("ALTER TABLE `$table` DROP ".$addon_col, array());
        }
    }

    unset($addon_cols, $addon_col, $db_cols, $db_col, $found, $sql);
