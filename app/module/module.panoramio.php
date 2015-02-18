<?php if ( !defined('BASEPATH')) header('Location:/404');
class PanoramioModule extends panoramioAPI
{
	public static function retrieve($city=false)
	{
		try
		{
			$ApiParm = array('img_num'=>100,
							 'calc_box'=>true,
							 'cache_dir'=>APPDATAPATH.'/cache/',
							 'type_save'=>'file',
							 'start_img'=>0,
							 'city'=>$city,
							 'typedata'=>'json'
							 );

			return parent::getPanoramioImages($ApiParm);
		}
		catch(PanoramioException $e)
		{
			return $e->getMessage();
		}
	}
	
	public static function get($city=null)
	{
		$city = preg_replace('/([\s]?)((C|c)ity|(K|k)ota)([\s]?)/','', $city);

		$panoramio = self::retrieve($city);

		if(is_object($panoramio))
		{
			$var = array('panoramio'=>$panoramio->photos,'size'=>'small','limit'=>5,'indexFirst'=>0);

			return PanoramioControl::build($var);
		}
		else
		{
			return $panoramio;
		}
	}
}