<?php

class rex_ycom
{
    /** @var array<string> */
    public static array $tables = [];

    public static function addTable(string $table_name): void
    {
        self::$tables[] = $table_name;
    }

    /**
     * @return array<string>
     */
    public static function getTables(): array
    {
        return self::$tables;
    }

    public static function parseText(string $text): string
    {
        $text = nl2br(trim($text));
        return '<p>' . $text . '</p>';
    }

    public static function cut(string $text, int $size = 15, string $t = ' (...) '): string
    {
        $s = strlen($text);
        if ($s > $size) {
            $start = (int) ($size / 2);
            return substr($text, 0, $start) . $t . substr($text, -$start);
        }
        return $text;
    }
}
