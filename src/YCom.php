<?php

namespace Yakamara\YCom;

use function in_array;

/**
 * The registry of tables ycom manages.
 *
 * This list is what puts the yform data pages for `rex_ycom_user` and `rex_ycom_group` into ycom's
 * own navigation block instead of yform's — see {@see YComAddon::getPages()}.
 */
final class YCom
{
    /** @var list<string> */
    private static array $tables = [];

    private function __construct() {}

    public static function addTable(string $tableName): void
    {
        if (!in_array($tableName, self::$tables, true)) {
            self::$tables[] = $tableName;
        }
    }

    /** @return list<string> */
    public static function getTables(): array
    {
        return self::$tables;
    }
}
