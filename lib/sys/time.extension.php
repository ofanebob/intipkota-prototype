<?php
class TimeExtension
{
	public static function pubDate2Epoch($data)
	{
		$d = explode("-", $date); 
		$t = explode(":", $time);
		$pubdate = date("r", mktime($t[0], $t[1], $t[2], $d[1], $d[2], $d[0]));

		return strtotime($pubdate);
	}
}