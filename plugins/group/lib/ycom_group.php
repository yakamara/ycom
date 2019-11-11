<?php

class rex_ycom_group extends \rex_yform_manager_dataset
{
    public static $perms = [
        '0' => 'translate:ycom_group_forallgroups',
        '1' => 'translate:ycom_group_inallgroups',
        '2' => 'translate:ycom_group_inonegroup',
        '3' => 'translate:ycom_group_nogroups',
    ];

    public static function getGroups()
    {
        $groups = [];
        foreach (self::query()->find() as $group) {
            $groups[$group->id] = $group->name;
        }
        return $groups;
    }

    public static function hasGroupPerm($groupType, $groups = [], $userGroups = [])
    {
        $groupType = (int) $groupType;

        if ($groupType < 1) {
            return true;
        }

        switch ($groupType) {
            // user in every group
            case 1:
                foreach ($groups as $group) {
                    if ('' != $group && !in_array($group, $userGroups)) {
                        return false;
                    }
                }
                return true;

            // user in at least one group
            case 2:
                foreach ($groups as $group) {
                    if ('' != $group && in_array($group, $userGroups)) {
                        return true;
                    }
                }
                return false;

            // user has no groups
            case 3:
                if (0 == count($userGroups)) {
                    return true;
                }
                return false;

            default:
                return false;
        }
    }
}
