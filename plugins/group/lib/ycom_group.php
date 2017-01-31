<?php

class rex_ycom_group extends \rex_yform_manager_dataset
{

    static $perms = [
        '0' => 'translate:ycom_group_forallgroups',
        '1' => 'translate:ycom_group_inallgroups',
        '2' => 'translate:ycom_group_inonegroup',
        '3' => 'translate:ycom_group_nogroups'
    ];

    static function getGroups()
    {
        $groups = [];
        foreach (self::query()->find() as $group) {
            $groups[$group->id] = $group->name;

        }
        return $groups;
    }

}
