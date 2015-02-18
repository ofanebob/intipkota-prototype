<?php
/**
 * Load default file config.php
 */
require_once(dirname(__FILE__).'/config.php');

/**
 * Load systems library
 */
Libs::load($GLOBALS['REQUIRELIB'],'sys');

/**
 * Autoload system
 */
function __autoload($autoloadClassName)
{
	$getMethod = preg_replace('/([\w]+)((M|m)odule|(C|c)ontrol)/','$2',$autoloadClassName);

	$getClassName = preg_replace('/([\w]+)((M|m)odule|(C|c)ontrol)/','$1',$autoloadClassName);

	$lowerCaseMethod = strtolower($getMethod);

	$lowerClassName = strtolower($getClassName);

	if($lowerCaseMethod == 'module')
	{
		$absolutePath = MODULEPATH;
	}
	elseif($lowerCaseMethod == 'control')
	{
		$absolutePath = CONTROLPATH;
	}
	else
	{
		$absolutePath = null;
	}

	if($absolutePath !== null)
	{
		$path = "$absolutePath/{$lowerCaseMethod}.{$lowerClassName}.php";
		if(file_exists($path))
		{
			require_once($path);
		}
	}
}
?>