<?php
    /**
     * Let's remove the columns from the database
     */
    $addon_cols = [
        'ycom_auth',
        'ycom_groups',
        'ycom_users'
    ];

    $db_cols = rex_sql::showColumns(rex::getTable('media'));
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
            $sql->setQuery("ALTER TABLE `".rex::getTable('media')."` DROP ".$addon_col, array());
        }
    }

    unset($addon_cols, $addon_col, $db_cols, $db_col, $found, $sql);
