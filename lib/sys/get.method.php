<?php
class Method
{
	public static function call($method=false,$data=null)
	{
		if(is_array($method))
		{
			if( method_exists($method[0],$method[1]) || 
				is_callable($method) || 
				(is_array($method) AND function_exists($method[1]))
			  )
			{
				if(is_null($data))
				{
					return null;
				}
				else
				{
					return call_user_func_array($method, array($data));
				}
			}
			else
			{
				return null;
			}
		}
		else
		{
			return null;
		}
	}
}
?>