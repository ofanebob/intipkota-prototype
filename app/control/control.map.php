<?php if ( !defined('BASEPATH')) header('Location:/404');
class MapControl extends GenTag
{
	public static function build($var=false)
	{
		if(is_array($var))
		{
			extract($var);

			return json_encode($foursquare->response->groups[0]->items);
		}
	}


	public static function variable($city=false)
	{
		if(is_string($city))
		{	
			$foursquare = null;

			$foursquareCache = APPDATAPATH.'/cache/foursquare-explore-'.md5(strtolower($city)).'.json';

			if(file_exists($foursquareCache))
			{
				$foursquare = file_get_contents($foursquareCache);
			}

			return $foursquare;
		}
	}	
}
?>