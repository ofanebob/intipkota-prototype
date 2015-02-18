<?php if ( !defined('BASEPATH')) header('Location:/404');
/**
 *
 */
class Wikipedia extends Method
{
	/** @var array */
	protected static $_args;

	/** @var string */
	protected static $_cache_dir;

	/** @var basepath */
	protected static $_basepath;

	const CACHE_PATH = '/app/data/cache';
	const BASE_STATE = 'id';
	const BASE_ACTION = 'query';
	const BASE_FORMAT = 'json';
	const RVPROP = 'timestamp|user|comment|content';
	const BASE_API = 'wikipedia.org/w/api.php';

	public static function retrive_wiki($args=false)
	{
		if(is_array($args))
		{
			/** @var $args params */
			self::$_args = is_array($args) ? $args : null;

			/** @var path direktori default */
			self::$_basepath = (defined('BASEPATH') ? BASEPATH : dirname(__FILE__).'/../../');

			/** @var direktori untuk file cache */
			self::$_cache_dir = self::$_basepath.self::CACHE_PATH;

			try
			{
				$action = isset($args['action']) ? $args['action'] : self::BASE_ACTION;
				
				$format = isset($args['format']) ? $args['format'] : self::BASE_FORMAT;
				
				$state = isset($args['state']) ? $args['state'] : self::BASE_STATE;

				$rvprop = isset($args['rvprop']) ? $args['rvprop'] : self::RVPROP;

				$cache_dir = isset($args['cache_dir']) ? $args['cache_dir'] : self::$_cache_dir;
				
				$type_save = isset($args['type_save']) ? $args['type_save'] : 'database';

				$serialize = isset($args['serialize']) ? $args['serialize'] : true;

				$table_cache = isset($args['table_cache']) ? $args['table_cache'] : 'app_cache';

				$expire_cache = isset($args['expire_cache']) ? $args['expire_cache'] : strtotime('+1 Hour');

				$parameter = isset($args['parameter']) ? $args['parameter'] : 'Kabupaten_Kuningan';
				$parameter = preg_replace('/([a-zA-Z0-9 _-]+)([\,]+)(.*)/',"$1", urldecode($parameter));
				$parameter = preg_replace('/ /',"_", $parameter);

				$param = array(	'method'=>array('Wikipedia','_ServiceWiki'),
								'data'=>array('parameter'=>$parameter,
											  'action'=>$action,
											  'state'=>$state,
											  'format'=>$format,
											  'rvprop'=>$rvprop
								),
								'cache_expire'=>$expire_cache,
								'cache_prefix'=>'wiki',
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
				throw new WikipediaException($e->getMessage().' From Wikipedia');
			}
		}
		else
		{
			throw new WikipediaException('Error Parameter Function Wiki');
		}
	}


	/**
	 * _ServiceFoursquare()
	 * Fungsi untuk meminta data dari API endpoint Foursquare
 	 * @return cURLs() retrive data
	 */
	public static function _ServiceWiki($args=false)
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
				$action = isset($data['action']) ? $data['action'] : self::BASE_ACTION;
				$format = isset($data['format']) ? $data['format'] : self::BASE_FORMAT;
				$state = isset($data['state']) ? $data['state'] : self::BASE_STATE;
				$rvprop = isset($data['rvprop']) ? $data['rvprop'] : self::RVPROP;
				$parameter = isset($data['parameter']) ? $data['parameter'] : null;

				$API_ENDPOINT = 'http://'.$state.'.'.$server.'?rvparse&action='.$action.'&indexpageids&prop=revisions&titles='.$parameter.'&rvprop='.$rvprop.'&format='.$format;

				$cURL = new cURLs(array('url'=>$API_ENDPOINT,'type'=>'data'));
				$get = $cURL->access_curl();

				$decode = json_decode($get,true);

				if( isset($decode['error']) || 
					$decode['query']['pageids'][0] == -1)
				{
					return null;
				}
				else
				{
					$content = "*";
					$indexpageids = $decode['query']['pageids'][0];
					$content = $decode['query']['pages'][$indexpageids]['revisions'][0][$content];

					if(preg_match('/#(alih|move)/',strtolower($content)))
					{
						$parameter = CityRegex::wikitext($content);
						
						$param = array( 'parameter'=>$parameter,
										'action'=>$action,
										'state'=>$state,
										'format'=>$format,
										'rvprop'=>$rvprop
									);

						return (self::_ServiceWiki($param));
					}
					else
					{
						preg_match('/=([\w]+[\_]+[\w]+)/', $content, $match);
						if(count($match) > 0)
						{
							//$parameter = CityRegex::wikitag($content);
							$parameter = preg_replace('/=/','',$match[1]);

							var_dump($parameter);

							$param = array( 'parameter'=>$parameter,
											'action'=>$action,
											'state'=>$state,
											'format'=>$format,
											'rvprop'=>$rvprop
										);

							//return (self::_ServiceWiki($param));						
						}
						else
						{
							return $get;
						}
					}
				}
			}
			else
			{
				return null;
			}
		}
	}	
}

/** WikipediaException extends */
class WikipediaException extends Exception{}
?>