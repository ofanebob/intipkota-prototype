<?php if ( !defined('BASEPATH')) header('Location:/404');
class Regex
{
	/** @method Menghitung banyaknya koma dalam string */
	public static function GetNumberComma($string) {
	    return preg_match_all(self::get('count_comma'), $string, $m);
	}

	public static function get($type='')
	{
		$bank = self::bank($type);
		if($bank != false)
		{
			return $bank;
		}
	}

	public static function bank($type='')
	{
		switch ($type) {
			case 'first_word_comma':
				//return '/((?<=>)\s*\b\w*\b|^\s*\w*\b)([\,])/';
				return '/^(.+?)([\,])(.+)/';
			break;
			
			case 'first_word_space':
				return '/((?<=>)\s*\b\w*\b|^\s*\w*\b)([\s])/';
			break;
			
			case 'wikitext_move':
				return '/([\#][A-Z]+[\s])([\[]{2}+)([a-zA-Z0-9 _-]+)([\]]{2})+(\n)?(.*)/';
			break;

			case 'wikitag_move':
				return '/(.*)<([\w]{2})>([A-Z]+[\s])<a href\=\\"([\/]+[\w]+[\/]+)(.+)\\"([\s])(.*)?/';
			break;

			case 'wikitag_move_match':
				return '/<([\w]{2})>([A-Z]+[\s])<([\w]{1,2})/';
			break;

			case 'wikitag_move_match_redirect':
				return '/(.*)\.php\?title\=([\w]+)(.*)/';
			break;
			
			case 'trim_city':
				return '/(.*?)([a-zA-Z0-9]+)([\,])(.*)/';
			break;
			
			case 'pathinfo_filename':
				return '/([a-zA-Z0-9_-]+)\?p\=(.*)/';
			break;

			case 'count_comma':
				return '/(?=,|$)/';
			break;

			case 'news_image':
				return '/(.+)(<img[\s]src\=\")(\/\/[a-zA-Z0-9 \/\?\=\:\._-]+)(\")(.+)/';
			break;

			case 'css_file':
				return '/([a-zA-z0-9 \._-]+)\.css/';
			break;

			case 'js_file':
				return '/([a-zA-z0-9 \._-]+)\.js/';
			break;

			case 'panoramio_image':
				return '/(.+)([\/])([0-9]+.+)/';
			break;
			
			default:
				return false;
			break;
		}
	}
}
?>