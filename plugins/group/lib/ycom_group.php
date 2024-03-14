<?php

declare(strict_types=1);

class rex_ycom_group extends rex_yform_manager_dataset
{
    /** @var array<string> */
    public static array $perms = [
        '0' => 'translate:ycom_group_forallgroups',
        '1' => 'translate:ycom_group_inallgroups',
        '2' => 'translate:ycom_group_inonegroup',
        '3' => 'translate:ycom_group_nogroups',
    ];

    /**
     * @throws rex_exception
     * @return array<string>
     */
    public static function getGroups(): array
    {
        $groups = [];
        foreach (self::query()->find() as $group) {
            /** @var rex_ycom_group $group */
            $groups[$group->getId()] = $group->getName();
        }
        return $groups;
    }

    /**
     * @param string|int $groupType
     * @param array<string|int> $groups
     * @param array<string|int> $userGroups
     */
    public static function hasGroupPerm($groupType, array $groups = [], array $userGroups = []): bool
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

    public function getName(): string
    {
        return $this->getValue('name');
    }
}
