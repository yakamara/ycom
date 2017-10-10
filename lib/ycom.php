<?php

class rex_ycom
{
    public static $tables = [];

    public static function addTable($table_name)
    {
        self::$tables[] = $table_name;
    }

    public static function getTables()
    {
        return self::$tables;
    }

    public static function parseText($text)
    {
        $text = nl2br(trim($text));
        return '<p>' . $text . '</p>';
    }

    public static function cut($text, $size = 15, $t = ' (...) ')
    {
        $s = strlen($text);
        if ($s > $size) {
            $start = (int) ($size / 2);
            return substr($text, 0, $start) . $t . substr($text, -$start);
        }
        return $text;
    }
}
