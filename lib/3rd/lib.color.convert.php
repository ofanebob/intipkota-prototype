<?php if ( !defined('BASEPATH')) header('Location:/404');

function convert_color_temp($temp)
{
	if(empty($temp))
	{
		return null;
	}
	else
	{
		if($temp <= 11)
		{
			$r = "35BFFF";
		}
		elseif($temp >= 12 AND $temp <= 31)
		{
			$r = "00C609";
		}
		elseif($temp >= 32 AND $temp <= 61)
		{
			$r = "F58839";
		}
		elseif($temp >= 62 AND $temp <= 91)
		{
			$r = "FB006A";
		}
		elseif($temp >= 92)
		{
			$r = "DE0303";
		}
		else
		{
			$r = "555";
		}
		return $r;
	}
}


function convert_color_rating($v)
{
	if(empty($v))
	{
		return null;
	}
	else
	{
		if($v <= 4.3)
		{
			$r = array('hexa'=>"888",'class'=>'default');
		}
		elseif($v >= 4.5 AND $v <= 7)
		{
			$r = array('hexa'=>"FFC800",'class'=>'warning');
		}
		elseif($v >= 7.1)
		{
			$r = array('hexa'=>"00B551",'class'=>'success');
		}
		else
		{
			$r = array('hexa'=>"888",'class'=>'default');
		}
		return $r;
	}
}
?>