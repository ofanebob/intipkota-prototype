<?php
class TrimString
{
	public static function simpleTruncate($string=null, $pattern=false, $replace=false, $count=1)
	{
		if(is_string($string))
		{
			$pattern1 = $pattern == false ? ' ' : $pattern[0];
			$pattern2 = $pattern == false ? '/^(.+?)([\s])(.+)/' : $pattern[1];
			$replace = $replace == false ? '' : $replace;

			if(strlen($string) > 15)
			{
				if(substr_count($string, $pattern1) > $count)
				{
					return preg_replace($pattern2, '$1', $string);
				}
				else
				{
					return $string;
				}
			}
			else
			{
				return $string;
			}
		}
	}


    /**
     * @since v.1.0
     * Fungsi truncate
     * Membuat pembatasan karakter teks dalam bentuk string
     */
    public static function truncate($input, $maxWords, $maxChars){
        $words = preg_split('/\s+/', $input);
        $words = array_slice($words, 0, $maxWords);
        $words = array_reverse($words);

        $chars = 0;
        $truncated = array();

        while(count($words) > 0)
        {
            $fragment = trim(array_pop($words));
            $chars += strlen($fragment);

            if($chars > $maxChars) break;

            $truncated[] = $fragment;
        }

        $result = implode($truncated, ' ');
        return $result . ($input == $result ? '' : '...');
    }


    public static function slug($string)
    {
    	$string = preg_replace('/([^\w])/', '-', $string);
        $string = strtolower($string);
        return $string;
    }
}
?>