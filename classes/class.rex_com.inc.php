<?php

class rex_com {

	function cut($text,$size = 15,$t=" (...) ")
	{
		// 123456789 -> 10, 5, 
		$s = strlen($text);
		if($s>$size)
		{
			$start = (int) ($size/2);
			return substr($text,0,$start).$t.substr($text,-$start);
		}else {
			return $text;
		}
	}

}