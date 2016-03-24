<?php

class rex_ycom_group {

    static $perms = [
        '0' => 'translate:ycom_group_forallgroups',
        '1' => 'translate:ycom_group_inallgroups',
        '2' => 'translate:ycom_group_inonegroup',
        '3' => 'translate:ycom_group_nogroups'
    ];

    static function getGroups()
    {
        $groups = [];
        foreach(rex_sql::factory()->getArray('select id,name from '.self::getTable()) as $group) {
            $groups[$group["id"]] = $group["name"];

        }

        return $groups;
    }

    static function getTable()
    {
        return 'rex_ycom_group';

    }

}