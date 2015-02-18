<?php if ( !defined('BASEPATH')) header('Location:/404');
class PanoramioControl extends GenTag
{
	public static function build($args=false)
	{
		if(is_array($args))
		{
			extract($args);

			$Render = '<ul id="panoramioImages" class="noborder-last-bottom nomargin-last-bottom taglist-flat">';
			
			$Render .= self::loop(array('photo'=>$panoramio,'size'=>$size,'limit'=>$limit,'indexFirst'=>$indexFirst));

			$Render .= '</ul>';

			$Render .= '<span class="load_more_panoramio">Memuat Foto Lainnya...</span>';

			return $Render;
		}
	}

	public static function variable($city=false)
	{
		if(is_string($city))
		{
			$city = preg_replace('/([\s]?)((C|c)ity|(K|k)ota)([\s]?)/','', $city);
			
			$panoramio = null;

			$PanoramioCache = APPDATAPATH.'/cache/panoramio-'.md5(strtolower($city)).'.json';

			if(file_exists($PanoramioCache))
			{
				$panoramio = file_get_contents($PanoramioCache);
			}

			return $panoramio;
		}
	}

	public static function loop($args=false)
	{
		if(is_array($args))
		{
			extract($args);

			$photo = $limit == false ? $photo : array_slice($photo,$indexFirst,$limit);

			$size = is_null($size) ? 'small' : $size;

			//$replacer = 'http://panoramio.com/photos/'.$size.'/$3';

			$Render = '';

			foreach($photo as $id => $localPhotos)
			{
				//$imageFix = preg_replace(Regex::get('panoramio_image'),$replacer,$localPhotos->photo_file_url);

				$Render .= '<li id="panoramio-'.(++$indexFirst).'" data-id="'.$localPhotos->photo_id.'" class="clearfix border-bottom border-smoke border-1px border-solid marginspace-bottom">';
				$Render .= '<h5 class="nomargin" style="margin-bottom:10px">
							<i class="glyphicon glyphicon-paperclip"></i> 
							<span>'.TrimString::truncate(ucwords($localPhotos->photo_title),4,30).'</span>
							</h5>';
				$Render .= '<a href="'.$localPhotos->photo_url.'" target="_blank" class="thumbnail shaping d-inline-block w-100cent">';
				$Render .= '<img src="'.$localPhotos->photo_file_url.'" class="d-inline-block w-100cent" alt="'.TrimString::slug($localPhotos->photo_title).'" />';
				$Render .= '</a>';
				$Render .= '</li>';
			}

			//$Render .= '<li class="load_more_panoramio">Load More</li>';

			return $Render;
		}
	}
}
?>