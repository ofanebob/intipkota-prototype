<?php if ( !defined('BASEPATH')) header('Location:/404');
class NewsModule extends Method
{
	public static function retrieve($param=null)
	{
		try
		{
			$data =  array(	'query'=>$param,
							/*'prefix_query'=>array('matches'=>'/(ind(onesia|o)|mala(ysia|y)|singa(pore|pura)|brunei)/',
												  'prefix1'=>'Wisata ',
												  'prefix2'=>' Travel',
												  'query'=>$param
										),*/
							'type_save'=>'file',
							'expire_cache'=>strtotime('+1 Day'),
							'cache_dir'=>APPDATAPATH.'/cache/',
							'serialize'=>true
							);

			$rss = NewsGoogleFeed::loadRss($data);
			
			return $rss;
		}
		catch(NewsGoogleFeedException $e)
		{
			//return $e->getMessage();
			return null;
		}
	}

	public static function get($param=null)
	{
		$news = self::retrieve($param);

		if(is_object($news))
		{
			$var = array('rss'=>$news);

			return NewsControl::build($var);
		}
		else
		{
			return null;
		}
	}
}