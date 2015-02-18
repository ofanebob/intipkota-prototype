<?php if ( !defined('BASEPATH')) header('Location:/404');
class WeatherModule extends WuForecast
{
	public static function retrieve($param=null)
	{
		try
		{
			$data = array(	'lang'=>'ID',
							'city'=>strtolower($param),
							'expire_cache'=>strtotime('+1 Hour'),
							'type_save'=>'file',
							'cache_dir'=>APPDATAPATH.'/cache/',
							'forecast'=>true
							);
			
			$weather = parent::retrive_api($data);
			
			return $weather;
		}
		catch(WuForecastException $e)
		{
			//return $e->getMessage();
			return null;
		}
	}

	public static function get($param=null)
	{
		$weather = self::retrieve($param);

		if(is_object($weather))
		{
			self::_Global($weather);
			
			return WeatherControl::build(array('data'=>$weather));
		}
		else
		{
			//return '<div class="inner-side"><div class="alert alert-danger">Weather '.$weather.'</div></div>';
			return $weather;
		}
	}

	public static function _Global($weather)
	{
		$weather_icon = $weather->current_observation->icon;
		$weather_name = $weather->current_observation->weather;
		$weather_city = preg_replace('/((K|k)ota|(C|c)ity| )/','',$weather->current_observation->display_location->city);
		$weather_time = $weather->current_observation->local_epoch;
		$weather_time_convert = date('j F Y',$weather_time);

		$GLOBALS['CURRENTWEATHER'] = htmlentities("Cuaca di $weather_city tanggal $weather_time_convert $weather_name");
	}
}