<?php
class URLrules
{
	public static function get($q='')
	{
		if(!empty($q) OR $q!='')
		{
			return (isset($_GET[$q]) ? $_GET[$q] : '');
		}
	}

	public static function empty_get($q='')
	{
		if(!empty($q) OR $q!='')
		{
			return (empty($_GET[$q]) OR $_GET[$q] == '' ? true : false);
		}
	}

	public static function exist_get($q='')
	{
		if(!empty($q) OR $q!='')
		{
			return (isset($_GET[$q]) ? ( $_GET[$q] != '' ? true : false ) : false);
		}
	}

	public static function exist_get_and_empty($q='')
	{
		if(!empty($q) OR $q!='')
		{
			return (isset($_GET[$q]) AND $_GET[$q] == '' ? true : false);
		}
	}

	public static function is_home($param)
	{
		extract($param);

		if(is_array($param))
		{
			$base = isset($param['base']) ? $base : basename($_SERVER['REQUEST_URI']);
			$dir = isset($param['dir']) ? $dir : dirname($_SERVER['REQUEST_URI']);
		}
		else
		{
			$base = basename($_SERVER['REQUEST_URI']);
			$dir = dirname($_SERVER['REQUEST_URI']);
		}

		if( empty(stripslashes($dir)) OR 
			preg_replace('/\\\\/','x9~D1@',$dir) == 'x9~D1@' OR 
			$dir == $base
		  )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function get_city($city='')
	{
		if(is_string($city) AND $city != '')
		{
			$regex = '/([\w]+\=)(.+)/';

			if(preg_match($regex,$city)){
				preg_match($regex,$city,$return);
				return array('template'=>preg_replace('/\=/','',$return[1]),
							 'city'=>$return[2]
							);
			}
			else
			{
				return null;
			}
		}
		else
		{
			return false;
		}
	}
}
?>