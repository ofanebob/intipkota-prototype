<?php
class GoogleMap extends Method
{
	function image_handler($args=false){
		if(is_array($args))
		{
			$folder_storing = $args['cache'] ? $args['cache'] : 'media/maps/';
			$file_storing = 'gmap_img_'.($args['name'] ? $args['name'] : date('Ymd')).'.jpg';
			$path_puts = $folder_storing.$file_storing;
			$cachetime = $args['cachetime'] ? $args['cachetime'] : 24;
			$url_api_gmap = 'https://maps.googleapis.com/maps/api/staticmap?center='.$args['ll'].'&zoom='.$args['z'].'&size='.$args['s'].'&markers='.$args['ll'].'&sensor='.$args['sensor'];
			if( file_exists($path_puts) && ((time() - filemtime($path_puts)) > 3600 * $cachetime) ){
				unlink($path_puts);
				return $url_api_gmap;
			}
			else{
				$get_map_image = file_get_contents($url_api_gmap);
				file_put_contents($path_puts,$get_map_image);
				return site_url($path_puts);
			}
		}
	}
}
?>