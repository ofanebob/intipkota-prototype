<?php
/** JSON API KEY extractor
 *
 */
class JsonKeyExtractor
{
	protected static $_key_files = null;
	protected static $_key_path = null;

	const KEY_PATH = 'app/data/apikey';

	public static function get($args=false)
	{
		if( is_array($args) )
		{
			$basepath = defined('BASEPATH') ? BASEPATH : dirname(__FILE__).'/../../';
			$key_path = $basepath.'/'.self::KEY_PATH.'/';

			$key_file = isset($args['key_file']) ? $args['key_file'] : null;

			self::$_key_path = $key_path;
			self::$_key_files = $key_file;

			$GetKey = self::_GrabFiles()['data'];

			if(isset($args['random']) AND $args['random'] == true)
			{
				$key = self::_Randomize($GetKey);
			}
			else
			{
				$key = $GetKey[0];
			}

			return $key['api_key'];
		}
		else
		{
			throw new Exception('Error Retrive Key');
		}
	}

	protected static function _Randomize($key)
	{
		return $key[array_rand($key)];
	}

	private static function _GrabFiles($files=false)
	{
		$files = $files == false ? (self::$_key_files == null ? null : self::$_key_files) : $files;
		$path = self::$_key_path;
		$get_files = file_get_contents($path.$files);
		$decode = json_decode($get_files,true);
		return $decode;
	}
}
?>