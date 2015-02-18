<?php
class SearchModule extends Method
{
	/*
	 * Sample Use
	 *
	SearchModule::cache( array( 'city'=>$param_theme['param_all'] ) );
	*/

	public static function cache($args=false)
	{
		try
		{
			$parameter = isset($args['param']) ? $args['param'] : null;
			$ip = isset($args['ip']) ? $args['ip'] : Browser::get_ip();

			$param = array(	'method'=>array('SearchModule','process'),
							'data'=>array('city'=>$parameter,
										  'ip'=>$ip
							),
							'expire_cache'=>strtotime('+1 Day'),
							'cache_prefix'=>'search',
							'cache_id'=>$parameter,
							'type_save'=>'database',
							'table_cache'=>'search_cache',
							'serialize'=>true,
							'base64'=>true,
							'retrive'=>false
							);
			
			self::save_city(array('city'=>$parameter));

			return CacheHandler::save($param);
		}
		catch(CacheHandlerException $e)
		{
			//echo $e->getMessage().' From Search Module';
			return null;
		}
	}


	public static function save_city($args=false)
	{
		try
		{
			$city = isset($args['city']) ? $args['city'] : null;

			$param = array(	'method'=>false,
							'data'=>$city,
							'expire_cache'=>strtotime('+1 Month'),
							'cache_prefix'=>'city',
							'cache_id'=>$city,
							'type_save'=>'database',
							'table_cache'=>'city_cache',
							'serialize'=>false,
							'base64'=>false,
							'retrive'=>false
							);

			return CacheHandler::save($param);
		}
		catch(CacheHandlerException $e)
		{
			return null;
		}
	}


	public static function process($args=false)
	{
		$city = isset($args['city']) ? $args['city'] : null;
		$ip = isset($args['ip']) ? $args['ip'] : Browser::get_ip();
		
		$regexNews = Regex::get('news_image');
		//$cityRegex = CityRegex::test($city);

		$news = NewsModule::retrieve($city);
		$venue = FoursquareModule::retrieve($city);

		$geocode = self::geocode($city);

		$reconstruct_data = array(
			'city'=>array(
				'name'=>$city,
				'geocode'=>array(
					'lat'=>$geocode->lat,
					'lng'=>$geocode->lng,
					'll'=>$geocode->lat.','.$geocode->lng
				)
			),
			'items'=>array()
		);

		/** @Loop News Array */
		$numNews = 0;
		foreach ($news->item as $num => $item)
		{
			if(preg_match($regexNews, $item->description))
			{
				$img = preg_replace($regexNews, "$3", $item->description);

				$guid = preg_replace('/(.*)((cluster)?\=(f|ht)t(p|ps)\:)(.*)/', "$6", $item->guid);

				$reconstruct_data['items'][] = 
					array(
						'type'=>'news',
						'content'=>array(
							'title'=>htmlSpecialChars($item->title),
							'image'=>$img,
							'meta'=>array(
								'date'=>(@$item->timestamp ? (int) $item->timestamp : ''),
								'link'=>$guid
							)
						)
					);
			}
			if(++$numNews > 10) break;
		}

		$totalVenue = count($venue->response->groups[0]->items);

		foreach ($venue->response->groups[0]->items as $num => $venues)
		{
			$venues = $venues->venue;

			$photos = @$venues->photos;
			$rating = @$venues->rating ? in_array(intval($venues->rating),range(5,10)) : false;

			if($photos)
			{
				$p = $photos->groups;
				$p = count($p) > 0 ? $p[0]->items[0] : null;

				if($p != null)
				{
					$img = $p->prefix.'80x80'.$p->suffix;

					if($rating == true)
					{
						$reconstruct_data['items'][] = 
							array(
								'type'=>'venue',
								'content'=>array(
									'title'=>$venues->name,
									'image'=>$img,
									'meta'=>array(
										'id'=>$venues->id,
										'address'=>implode(', ',$venues->location->formattedAddress),
										'link'=>'https://foursquare.com/v/'.$venues->id,
										'rating'=>$venues->rating
									)
								)
							);
					}
				}
			}

			if($num > 20) break;
		}

		return $reconstruct_data;
	}

	private static function geocode($param)
	{
		try
		{
			$geo_param = array('city'=>$param,'typedata'=>'json','type_save'=>'file');
			$get_geocode = GoogleGeocode::get_geocode($geo_param);

			return $get_geocode->results[0]->geometry->location;
		}
		catch(GoogleGeocodeException $e)
		{
			return null;
		}
	}

	private static function calculateVenueRating($rating,$totalVenue)
	{
		/*if($totalVenue < 20)
		{
			return $rating >= 6 ? true : false;
		}
		elseif($totalVenue > 20 AND $totalVenue < 40)
		{
			return $rating >= 7 ? true : false;
		}
		elseif($totalVenue > 40 AND $totalVenue < 60)
		{
			return $rating >= 8 ? true : false;
		}
		elseif($totalVenue > 60 AND $totalVenue < 80)
		{
			return $rating >= 9 ? true : false;
		}
		else
		{
			return true;
		}*/
	}
}
?>