<?php if ( !defined('BASEPATH')) header('Location:/404');
class FoursquareModule extends Foursquare
{
	public static function retrieve($param=null)
	{
		try
		{
			$data = array('type_save'=>'file',
						  'cache_dir'=>APPDATAPATH.'/cache/',
						  'endpoint_type'=>'explore',
						  'parameter'=>strtolower($param),
						  'expire_cache'=>strtotime('+1 Week'),
						  'radius'=>'15000',
						  'langlatacc'=>'250'
						  );

			$foursquare = parent::retrieve_api($data);
			
			return $foursquare;
		}
		catch(FoursquareException $e)
		{
			//echo $e->getMessage();
			return null;
		}
	}
	
	public static function get($param=null,$page='search',$indexFirst=0,$limit=5)
	{
		$foursquare = self::retrieve($param);

		if(is_object($foursquare))
		{
			self::_Global($foursquare);

			$var = array('foursquare'=>$foursquare,
						 'icon_suffix'=>'32.png',
						 'page'=>$page,
						 'indexFirst'=>$indexFirst,
						 'limit'=>$limit
						);
			
			return FoursquareControl::build($var);
		}
		else
		{
			//return '<div class="alert alert-danger">Foursquare '.$foursquare.'</div>';
			return $foursquare;
		}
	}

	public static function _Global($foursquare)
	{
		$response = $foursquare->response;
		
		foreach($response->groups[0]->items as $f)
		{
			$rating = @$f->venue->rating ? $f->venue->rating : 0;
			
			/* @cond Filter venue berdasarkan nilai rating dari 7 - 10 */
			if(in_array(intval($rating),range(7,10)))
			{
				$foursquare_current_venue[] = preg_replace('/\"/','\'',$f->venue->name);
			}
		}

		$GLOBALS['CURRENTVENUE']['LISTS'] = implode(', ', $foursquare_current_venue);
		$GLOBALS['CURRENTVENUE']['TOTAL'] = count($response->groups[0]->items);

		$foursquare_current_venue = array_slice($foursquare_current_venue,0,15);
		$fix_total_venue = count($foursquare_current_venue).' Tempat terpopuler di '.$response->headerFullLocation.': '.implode(', ', $foursquare_current_venue).' & lainnya';
		$GLOBALS['CURRENTVENUE']['DESCRIPTION'] = $fix_total_venue;
	}
}