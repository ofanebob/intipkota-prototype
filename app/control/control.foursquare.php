<?php if ( !defined('BASEPATH')) header('Location:/404');
class FoursquareControl extends GenTag
{
	public static function build($var=false)
	{
		if(is_array($var))
		{
			extract($var);

			$return = '<div id="foursquareVenues" class="noborder-last-bottom clearfix">';

			$items = $foursquare->response->groups[0]->items;

			//shuffle($items);
			$items = $limit == false ? $items : array_slice($items,$indexFirst,$limit);

			/** @Loop obj $items Foursquare */
			foreach($items as $k=>$f):

				if($page=='search')
				{
					$return .= self::search(array('foursquare'=>$f,'index'=>(++$indexFirst)));
				}
				else
				{
					$return .= self::maps(array('foursquare'=>$f,'icon_suffix'=>$icon_suffix));
				}

			endforeach;

			$return .= '</div>';

			$return .= $page == 'search' ? '<span class="load_more_foursquare">Memuat Loka Lainnya...</span>' : '';

			return $return;
		}
	}

	public static function maps($param=false)
	{
		if(is_array($param))
		{
			extract($param);
				
			$f = $foursquare->venue;

			$return = '<!-- Start Venue Lists -->';

			/** @var Category */
			$categories = @$f->categories;
			if($categories)
			{
				$icon = $f->categories[0]->icon->prefix.$icon_suffix;
			}
			else
			{
				$icon = 'https://ss3.4sqi.net/img/categories_v2/none_'.$icon_suffix;
			}


			/** @var Canonical URL */
			$fixCanonical = 'https://id.foursquare.com/v/'.str_replace(' ','-',preg_replace('/([^\w+^\s])/','',strtolower($f->name))).'/'.$f->id;

			/** @Condition Hitung Total huruf di judul */
			$nomargin = strlen($f->name) >= 27 ? 'class="nomargin"' : '';

			$return .= '<div class="media foursquare border-smoke border-bottom border-1px border-solid" data-id="'.$f->id.'">
							<a data-geocode="'.$f->location->lat.','.$f->location->lng.'" class="pull-left thumbnail quick-venue-map" id="'.$f->id.'" title="'.htmlentities($f->name).'" href="'.$fixCanonical.'" target="_blank">
								<img src="'.$icon.'" class="waitForImages bg-primary" data-holder-rendered="true" style="width: 32px; height: 32px;" alt="32x32" />
							</a>';

			$return .= '<div class="media-body">
					<div class="media-heading">
							<h5 '.$nomargin.'>
								<a data-geocode="'.$f->location->lat.','.$f->location->lng.'" id="'.$f->id.'" class="quick-venue-map d-inline-block w-100cent" title="'.htmlentities($f->name).'" href="'.$fixCanonical.'" target="_blank">
									'.$f->name.'
								</a>
							</h5>
					</div>
				</div>
			</div>
			';

			return $return;
		}
	}

	public static function search($param=false)
	{
		if(is_array($param))
		{
			extract($param);
				
			$f = $foursquare->venue;

			$return = '<!-- Start Venue Lists -->';

			/** @var Category */
			$categories = @$f->categories;
			if($categories)
			{
				$categories_name = $f->categories[0]->name;
				$categories_id = $f->categories[0]->id;
				$category = sprintf('<span class="label label-primary">%s</span>&nbsp;',$categories_name);
			}
			else
			{
				$category = null;
			}

			/** @var Specials */
			$specials = @$f->specials;
			$special = $specials->count > 0 ? '<span class="specials-mini glyphicon glyphicon-certificate text-warning v-align-top"></span>' : '';

			/** @var Rating */
			$rating = @$f->rating ? 
			$rating = sprintf("<span class=\"label label-%s\">Rating: %s</span>",convert_color_rating($f->rating)['class'],$f->rating) : '';

			/** @var Photos */
			$photos = @$f->photos;
			$return .= '<div id="fsq-'.$index.'" class="media foursquare border-smoke border-bottom border-1px border-solid" data-id="'.$f->id.'">';
			
			$img = DOMAINFIX.'/public/images/no-image-80x100.jpg';
			if($photos)
			{
				$p = $photos->groups;
				$p = count($p) > 0 ? $p[0]->items[0] : null;
				if($p != null)
				{
					$img = $p->prefix.'80x100'.$p->suffix;
				}
			}

			/** @var Canonical URL */
			$fixCanonical = 'https://id.foursquare.com/v/'.str_replace(' ','-',preg_replace('/([^\w+^\s])/','',strtolower($f->name))).'/'.$f->id;

			$return .= '<a class="pull-left quick-view-map" id="'.$f->id.'" title="'.htmlentities($f->name).'" href="'.$fixCanonical.'" target="_blank">
					<img src="'.$img.'" class="thumbnail waitForImages" data-holder-rendered="true" style="width: 80px; height: 100px;" alt="80x100" />
					</a>';

			$return .= '<div class="media-body">
						<div class="media-heading">
						'.$special.'
						'.$category.'
						'.$rating.'
						</div>
						<div class="break-word-all">
							<h5>
								<a id="'.$f->id.'" class="quick-view-map d-inline-block w-100cent" title="'.htmlentities($f->name).'" href="'.$fixCanonical.'" target="_blank">
									'.$f->name.'
								</a>
							</h5>

							<address>
							'.implode(', ',$f->location->formattedAddress).'
							</address>
						</div>
					</div>
				</div>
			';
			return $return;
		}
	}
}
?>