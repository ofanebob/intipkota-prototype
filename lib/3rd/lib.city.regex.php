<?php if ( !defined('BASEPATH')) header('Location:/404');
class CityRegex extends Regex
{
	/** @method Mengambil Nama Kota & Provinsi/Pulau/Negara Bagian dari string input kompleks */
	public static function test($param)
	{
		if(str_word_count($param) > 2)
		{
			if(parent::GetNumberComma($param) > 1)
			{
				$regex_param = parent::get('first_word_comma');
				$replacer = '$1';
			}
			else
			{
				$regex_param = parent::get('first_word_space');
				$replacer = '';
			}
			
			$param_all = preg_replace($regex_param,$replacer,$param);
			return $param_all;
		}
		else
		{
			if(parent::GetNumberComma($param) > 1)
			{
				$regex_param = parent::get('first_word_comma');
				$param_all = preg_replace($regex_param,'$1',$param);
				return $param_all;
			}
			else
			{
				return $param;
			}
		}
	}


	/** @method Mendapatkan nilai #ALIH di Wiki Search API */
	public static function wikitext($param)
	{
		$param = preg_replace(parent::get('wikitext_move'),"$3",$param);
		$param = preg_replace('/ /',"_", $param);
		return $param;
	}


	public static function wikitag($param)
	{
		$param = preg_replace(parent::get('wikitag_move'),"$5",$param);
		$param = preg_replace('/ /',"_", $param);
		return $param;
	}

	public static function wikitaghref($param)
	{
		$param = preg_replace(parent::get('wikitag_move_match_redirect'),"$2",$param);
		$param = preg_replace('/ /',"_", $param);
		return $param;
	}


	/** @method Mengambil nama kota dalam string kompleks */
	public static function trimcity($param)
	{
		return preg_replace(parent::get('first_word_comma'),"$1",urldecode($param));
	}


	/** @method Merubah nama kota dari string untuk Wiki Search API */
	public static function wikiQuery($s)
	{
		if(str_word_count($s) > 1)
		{
			if(parent::GetNumberComma($s) > 2)
			{
				$ex = explode(',', $s);
				$word = $ex[0].$ex[1];
				$word = preg_replace('/(\s)/','_',$word);
			}
			elseif(parent::GetNumberComma($s) > 1)
			{
				$ex = explode(',', $s);
				$word = preg_replace('/(\s)/','_',$ex[0]);
			}
			else
			{
				$ex = explode(' ', $s);
				$word = $ex[0].'_'.$ex[1];
			}
		}
		else
		{
			$word = $s;
		}

		return preg_replace('/(\,)/','',$word);
	}
}
?>