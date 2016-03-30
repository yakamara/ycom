<?php

class rex_ycom {

	static $tables = [];

	static public function addTable($table_name){
		self::$tables[] = $table_name;
	}

	static public function getTables(){
		return self::$tables;
	}

	static public function parseText($text) {
		$text = nl2br(trim($text));
		return '<p>'.$text.'</p>';

	}






	static public function cut($text,$size = 15,$t=" (...) ")
	{
		// 123456789 -> 10, 5, 
		$s = strlen($text);
		if($s > $size) {
			$start = (int) ($size/2);
			return substr($text, 0, $start) . $t . substr($text, -$start);
		} else {
			return $text;
		}
	}

}