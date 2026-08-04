<?php

namespace Yakamara\YCom\Group;

use Yakamara\YForm\Manager\Dataset;

use function count;
use function in_array;

/**
 * A user group — the model class behind the yform table `rex_ycom_group`.
 *
 * In REDAXO 5 this was the `group` system plugin. REDAXO 6 has no plugin concept, so it is part of
 * the addon; the group columns on articles and media are always present.
 */
class Group extends Dataset
{
    /** @var array<string, string> */
    public const array PERMS = [
        '0' => 'translate:ycom_group_forallgroups',
        '1' => 'translate:ycom_group_inallgroups',
        '2' => 'translate:ycom_group_inonegroup',
        '3' => 'translate:ycom_group_nogroups',
    ];

    /**
     * @return array<int, string>
     */
    public static function getGroups(): array
    {
        $groups = [];

        foreach (self::query()->find() as $group) {
            /** @var self $group */
            $groups[$group->getId()] = $group->getName();
        }

        return $groups;
    }

    /**
     * Whether a user's groups satisfy the group requirement of an article or media file.
     *
     * @param array<string|int> $groups the groups the article/media requires
     * @param array<string|int> $userGroups the groups the user is in
     */
    public static function hasGroupPerm(string|int $groupType, array $groups = [], array $userGroups = []): bool
    {
        $groupType = (int) $groupType;

        if (1 > $groupType) {
            return true;
        }

        return match ($groupType) {
            // user has to be in every listed group
            1 => self::isInAllGroups($groups, $userGroups),
            // user has to be in at least one listed group
            2 => self::isInAnyGroup($groups, $userGroups),
            // user must not be in any group at all
            3 => 1 > count($userGroups),
            default => false,
        };
    }

    /**
     * @param array<string|int> $groups
     * @param array<string|int> $userGroups
     */
    private static function isInAllGroups(array $groups, array $userGroups): bool
    {
        foreach ($groups as $group) {
            if ('' !== (string) $group && !in_array($group, $userGroups)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array<string|int> $groups
     * @param array<string|int> $userGroups
     */
    private static function isInAnyGroup(array $groups, array $userGroups): bool
    {
        foreach ($groups as $group) {
            if ('' !== (string) $group && in_array($group, $userGroups)) {
                return true;
            }
        }

        return false;
    }

    public function getName(): string
    {
        return (string) $this->getValue('name');
    }
}
