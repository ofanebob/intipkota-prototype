<?php if ( !defined('BASEPATH')) header('Location:/404');
/**
 * Foursquare Class
 * Foursquare API Request
 * @author Ofan Ebob
 * @since 2014 (v.2)
 * @copyright GNU & GPL license
 */
class Foursquare extends Method
{
	/** @var array */
	protected static $_args;

	/** @var string */
	protected static $_api_key;

	/** @var string */
	protected static $_cache_dir;

	/** @var basepath */
	protected static $_basepath;

	const ERROR_MSG = 'Something Wrong';
	const BASE_LOCALE = 'id';
	const BASE_API = 'https://api.foursquare.com';
	const API_VERSION = 2;
	const API_ENDPOINT = 'venues/search?near=';
	const CACHE_PATH = '/app/test/venue/json';
	

	/**
	 * retrieve_api()
	 * Fungsi akses publik setelah semua proses permintaan data
 	 * @throws FoursquareException on retrieve_api()
 	 * @return stdObject convertion from json_decode()
	 */
	public static function retrieve_api($args=false)
	{
		/** @var $args params */
		self::$_args = is_array($args) ? $args : null;

		/** @var path direktori default */
		self::$_basepath = (defined('BASEPATH') ? BASEPATH : dirname(__FILE__).'/../../');

		/** @var direktori untuk file cache */
		self::$_cache_dir = self::$_basepath.self::CACHE_PATH;

		self::selfAPIkey();

		try
		{
			$cache_dir = isset($args['cache_dir']) ? $args['cache_dir'] : self::$_cache_dir;
			
			$type_save = isset($args['type_save']) ? $args['type_save'] : 'database';

			$serialize = isset($args['serialize']) ? $args['serialize'] : true;

			$table_cache = isset($args['table_cache']) ? $args['table_cache'] : 'app_cache';

			$endpoint_type = isset($args['endpoint_type']) ? self::endpoint_type($args['endpoint_type']) : self::endpoint_type('explore') ;

			$expire_cache = isset($args['expire_cache']) ? $args['expire_cache'] : strtotime('+1 Hour');

			$api_key = isset($args['key']) ? $args['key'] : self::$_api_key;

			$parameter = isset($args['parameter']) ? $args['parameter'] : 'Kuningan, Jawa Barat';

			$radius = isset($args['radius']) ? $args['radius'] : '';

			$langlatacc = isset($args['langlatacc']) ? $args['langlatacc'] : '';

			$final_parameter = self::_geocode($args['endpoint_type'],$parameter,$type_save);

			$param = array(	'method'=>array('Foursquare','_ServiceFoursquare'),
							'data'=>array('key'=>$api_key,
										  'locale'=>self::BASE_LOCALE,
										  'parameter'=>$final_parameter,
										  'endpoint_type'=>$endpoint_type,
										  'radius'=>$radius,
										  'langlatacc'=>$langlatacc
							),
							'cache_expire'=>$expire_cache,
							'cache_prefix'=>'foursquare-'.$args['endpoint_type'],
							'cache_id'=>$parameter,
							'cache_dir'=>$cache_dir,
							'type_save'=>$type_save,
							'table_cache'=>$table_cache,
							'serialize'=>$serialize
							);

			$cache = CacheHandler::save($param);

			$data = is_object($cache) ? $cache : json_decode($cache);

			return $data;
		}
		catch(CacheHandlerException $e)
		{
			throw new FoursquareException($e->getMessage().' From Foursquare');
		}
	}


	/**
	 * _api_key()
	 * Regenerate API Key dari JSON file
 	 * @return self::$_api_key
	 */
	public static function _api_key()
	{
		$param = array('key_file'=>'foursquare.api.json','random'=>true);
		$key = JsonKeyExtractor::get($param);

		/** @var API Key di acak */
		return $key;
	}

	public static function selfAPIkey()
	{
		self::$_api_key = self::_api_key();
	}


	/**
	 * _geocode()
	 * Merubah Nama Kota menjadi Geocode menggunakan Google Geocode API
	 * @param ($endpoint, $parameter, $type_save
 	 * @return strings
	 */
	protected static function _geocode($endpoint='',$parameter='',$type_save='')
	{
		if( empty($endpoint) AND empty($parameter) AND empty($type_save) )
		{
			return null;
		}
		else
		{
			if(in_array($endpoint,array('trending','explore')))
			{
				try
				{
					$geo_param = array('city'=>$parameter,'typedata'=>'json','type_save'=>$type_save);
					$get_geocode = GoogleGeocode::get_geocode($geo_param);

					$lat = floatval($get_geocode->results[0]->geometry->location->lat);
					$lng = floatval($get_geocode->results[0]->geometry->location->lng);

					return $lat.','.$lng;
				}
				catch(GoogleGeocodeException $e)
				{
					throw new FoursquareException($e->getMessage().' Geo/Foursquare');
				}
			}
			else
			{
				return urlencode($parameter);
			}
		}
	}

	/**
	 * endpoint_type()
	 * Menetapkan jenis endpoint yg tersedia di API Foursquare
	 * @param $type
 	 * @return strings
	 */
	public static function endpoint_type($type='search')
	{
		switch ($type) {
			case 'search':
				return 'venues/search?near=';
				break;
			
			case 'explore':
				return 'venues/explore?venuePhotos=1&ll=';
				break;

			case 'trending':
				return 'venues/categories?venuePhotos=1&ll=';
				break;

			case 'user':
				return 'user/self';
				break;

			case 'venue':
				return 'venues/';
				break;

			case 'categories':
				return 'venues/categories?';
				break;

			case 'tips':
				return 'tips/';
				break;

			default:
				return self::API_ENDPOINT;
				break;
		}
	}


	/**
	 * _ServiceFoursquare()
	 * Fungsi untuk meminta data dari API endpoint Foursquare
 	 * @return cURLs() retrive data
	 */
	public static function _ServiceFoursquare($args=false)
	{
		$data = $args == false ? self::$_args : $args;

		if($data == null)
		{
			return false;
		}
		else
		{
		 	if(method_exists('cURLs','access_curl'))
		 	{
				$server = self::BASE_API;
				$api_version = self::API_VERSION;
				$key = isset($data['key']) ? $data['key'] : self::$_api_key;
				$locale = isset($data['locale']) ? $data['locale'] : self::BASE_LOCALE;
				$endpoint_type = isset($data['endpoint_type']) ? $data['endpoint_type'] : self::API_ENDPOINT;
				$parameter = isset($data['parameter']) ? $data['parameter'] : null;
				$radius = isset($data['radius']) ? (empty($data['radius']) ? '' :'radius='.$data['radius'].'&') : '';
				$langlatacc = isset($data['langlatacc']) ?  (empty($data['langlatacc']) ? '' : 'llAcc='.$data['langlatacc'].'&') : '';

				$API_ENDPOINT = $server.'/v'.$api_version.'/'.$endpoint_type.$parameter.'&limit=100&'.$langlatacc.$radius.'client_id='.$key['api_id'].'&client_secret='.$key['api_secret'].'&locale='.$locale.'&v='.date('Ymd', strtotime('-1 Week'));

				$cURL = new cURLs(array('url'=>$API_ENDPOINT,'type'=>'data'));
				$get = $cURL->access_curl();

				$decode = json_decode($get,true);

				if( $decode['meta']['code'] == 200 || 
					count($decode['response']['venues']) > 0 || 
					count($decode['response']['groups']) > 0 || 
					count($decode['response']['groups'][0]['items']) > 0)
				{
					return $get;
				}
				else
				{
					return null;
				}
			}
			else
			{
				return null;
			}
		}
	}
}


/** Define Exception Extends Class */
class FoursquareException extends Exception{}
?>