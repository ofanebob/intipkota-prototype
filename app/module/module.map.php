<?php if ( !defined('BASEPATH')) header('Location:/404');
class MapModule extends GoogleGeocode
{
	public static function get($param=null)
	{
		$foursquare = FoursquareModule::retrieve($param);

		if(is_object($foursquare))
		{
			FoursquareModule::_Global($foursquare);
			self::_Global($param);

			$var = array('foursquare'=>$foursquare, 'icon_suffix'=>'bg_32.png');
			
			return MapControl::build($var);
		}
		else
		{
			return $foursquare;
		}
	}

	private static function geocode($param)
	{
		try
		{
			$geo_param = array('city'=>$param,'typedata'=>'json','type_save'=>'file');
			$get_geocode = parent::get_geocode($geo_param);

			return json_encode($get_geocode->results[0]->geometry->location);
		}
		catch(GoogleGeocodeException $e)
		{
			return null;
		}
	}

	public static function _Global($param)
	{
		$GeoCode = self::geocode($param);
		
		$GLOBALS['CURRENTVENUE']['LATLNG'] = $GeoCode;
	}
}