<?php if(!defined('BASEPATH')) header('Location:/404');
/**
 * Libs Class
 * Library function, class & method loader
 * @author Ofan Ebob
 * @since 2014 (v.1)
 */
class Libs
{
	/** @method Load 3rd library */
	public static function load3rd($data)
	{
		return self::load($data,'3rd');
	}

	/** @method Default load library */
	public static function load($data,$vendor='sys')
	{
		if(is_array($data))
		{
			$prefix = $vendor == '3rd' ? 'lib.' : '';

			foreach($data as $lib)
			{
				$lib_data = LIBPATH.'/'.$vendor.'/'.$prefix.$lib.'.php';
				if(file_exists($lib_data)) require_once($lib_data);
			}
		}
	}

	/** @method Generate file inc (lists of library on text) */
	public static function inc($file='')
	{
		if(empty($file))
		{
			exit('File Unaviable');
		}
		else
		{
			if(file_exists($file))
			{
				$grab = file_get_contents($file);

				if(Regex::GetNumberComma($grab) > 1)
				{
					$remove_newline = preg_replace('/(\n|\r|\')/','',$grab);
					$explode_comma = explode(',',$remove_newline);
					return $explode_comma;
				}
			}
			else
			{
				exit('Undifined File Library');
			}
		}
	}
}
?>