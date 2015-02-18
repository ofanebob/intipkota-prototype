<?php
class SuggestControl extends GenTag
{
	public static function build($param=false)
	{
		$Render = '<div id="loopSuggest" class="clearfix">';

			$Render .= '<div id="innerloopSuggest" style="min-height:410px">';

				if(empty($_COOKIE[md5('loopSuggest')]))
				{
					$Render .= self::loopSuggest($param);
				}
				else
				{
					$Render .= '<h2 class="inner-separator text-center">
								<i class="glyphicon glyphicon-time v-align-top"></i> Loading...</h2>';
				}
			
			$Render .= '</div>';

			$Render .= self::pagination(array('table'=>'search_cache'));

		$Render .= '</div>';

		return $Render;
	}

	public static function pagination($param=false)
	{
		$arr = array( 'row'=>'*', 'tbl'=>$param['table'], 'prm'=>"LIMIT 36" );

		$fetch_db = AccessCRUD($arr,'read');

		$get_total_rows = $fetch_db->num_rows;
		$item_per_page = LIMITSUGGEST;

		$pages = ceil($get_total_rows/$item_per_page)+1;

		if($get_total_rows > 0)
		{
			if($pages > 1)
			{
				$pagination = '<div class="container-fluid clearfix nopadding text-center">';

			    $pagination .= '<ul class="pagination">';
			    
			    $pagination .= '<li><a href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';

			    for($i = 1; $i<$pages; $i++)
			    {
			        $pagination .= '<li><a href="javascript:void(0)" class="paginationNumber" id="'.$i.'-page">'.$i.'</a></li>';
			    }
			    
			    $pagination .= '<li><a href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';

			    $pagination .= '</ul>';
				
				$pagination .= '</div>';
			}
			
			return $pagination;
		}
	}

	public static function loopSuggest($param=false)
	{
		if($param !== false)
		{
			$pages = is_array($param) ? isset($param['pages']) ? $param['pages'] : $param : $param;

			$limit = LIMITSUGGEST;

			$position = ($pages * $limit);

			$arr = array(
					'row'=>'*',
					'tbl'=>'search_cache',
					'prm'=>"ORDER BY cache_expire DESC LIMIT $position, $limit"
				);

			$search_cache = AccessCRUD($arr,'read');

			$Render = '<!-- Suggest Lists -->';

			if($search_cache->num_rows > 0)
			{
				while($suggest = $search_cache->fetch_assoc())
				{
					$unpackCache = unserialize(base64_decode($suggest['cache_data']));

					/** @match Menghapus text lain selain nama kota */
					$city_split = CityRegex::test($unpackCache['city']['name']);
					//$city_split = CityRegex::trimcity($param_city);
					$upperTextCity = strtoupper($city_split);

					$popover = 'data-container="body" data-toggle="popover" data-placement="top"';

					$Render .= '<div class="d-inline-block w-100cent separator-bottom noborder-last-bottom border-bottom border-solid border-smoke border-1px">';
						
						$Render .= '<div class="row separator-bottom clearfix">';

						$Render .= '<h4 class="col-lg-10 nomargin nopadding">
									<i class="glyphicon glyphicon-map-marker v-align-middle"></i> '.$upperTextCity;

						//$Render .= ' &middot; <small><i class="glyphicon v-align-middle glyphicon-calendar"></i> '.date('j F, Y (H:i A)', $suggest['cache_expire']).'</small>';

						$Render .= '</h4>';
						
						$Render .= '<div class="col-lg-2 nomargin nopadding"><div class="text-right">';
						$Render .= '<a href="'.DOMAINFIX.'/?cari='.urlencode($unpackCache['city']['name']).'" '.$popover.' data-content="Intip kota &quot;'.$upperTextCity.'&quot; di halaman panel (cuaca, berita, foto & tempat)" class="popovers btn btn-primary btn-small">
									<i class="glyphicon glyphicon-search"></i></a>';
						
						$Render .= ' <a href="'.DOMAINFIX.'/map='.urlencode($unpackCache['city']['name']).'" '.$popover.' data-content="Intip kota &quot;'.$upperTextCity.'&quot; lengkap dengan tampilan peta & filter tempat" class="popovers btn btn-primary btn-small" data-no-turbolink>
									<i class="glyphicon v-align-middle glyphicon-globe"></i></a>';
						$Render .= '</div></div>';
				
						$Render .= '</div>';

						$Render .= '<div class="marginspace-bottom">';

							$totalCacheItems = count($unpackCache['items']);

							$Render .= '<div class="well '.($totalCacheItems < 16 ? 'text-left' : 'text-center').'">';

								$Render .= '<div class="text-center w-100cent no-overflow " style="height:99px">';

								foreach ($unpackCache['items'] as $a => $b) 
								{
									if($b['type'] == 'news')
									{
										$itemsTitle = preg_replace('/[^\p{L}\p{N}\s]/u','',$b['content']['title']);
									}
									elseif($b['type'] == 'venue')
									{
										$itemsTitle = '<h5><i class="glyphicon v-align-middle glyphicon-map-marker"></i> '.TrimString::truncate($b['content']['title'],200,15).'</h5>';
										$itemsTitle .= '<div class="inner-separator">'.$b['content']['meta']['address'].'</div>';
									}

									$id = isset($b['content']['meta']['id']) ? 'id="'.$b['content']['meta']['id'].'"' : '';


									$Render .='<span class="border-all border-1px border-solid border-white cursor-pointer d-inline-block popovers" '.$id.' '.$popover.' data-content="'.htmlentities($itemsTitle).'">';
									$Render .= '<img src="'.$b['content']['image'].'" width="97" height="97" />';
									$Render .= '</span>';

									if($a+2 > 8) break;
								}
								
								$Render .= '</div>';

							$Render .= '</div>';

						$Render .= '</div>';

					$Render .= '</div>';
				}
			}
			else
			{
				$Render .= parent::alertTag(array('message'=>'Tidak ada rekomendasi kota','type'=>'warning'));
			}

			return $Render;
		}
		else
		{
			return 'Not Define';
		}
	}

	public static function labelSuggest($param=false)
	{
		if($param !== false)
		{
			$limit = isset($param['limit']) ? $param['limit'] : 500;

			$orderby = isset($param['orderby']) ? $param['orderby'] : 'RAND()';

			$arr = array(
					'row'=>'*',
					'tbl'=>'city_cache',
					'prm'=>"ORDER BY $orderby ASC LIMIT $limit"
				);

			$search_cache = AccessCRUD($arr,'read');

			$Render = '<!-- Label Suggest -->';

			while($suggest = $search_cache->fetch_assoc())
			{
				/** @match Menghapus text lain selain nama kota */
				$city_split = CityRegex::test($suggest['cache_data']);

				$upperTextCity = strtoupper($city_split);

				$Render .= '<span class="pull-left" style="margin:5px">';
				$Render .= '<a title="'.preg_replace('/([\w]+[\-]+)([\w])/','$2',$suggest['cache_name']).'" href="'.DOMAINFIX.'/?cari='.urlencode($suggest['cache_data']).'" class="label label-default">';
				$Render .= '<i class="glyphicon v-align-middle glyphicon-map-marker"></i> '.$upperTextCity;
				$Render .= '</a>';
				$Render .= '</span>';
			}
			
			return $Render;
		}
		else
		{
			return 'Not Define';
		}
	}
}

//echo strtotime('-1 Week');
?>