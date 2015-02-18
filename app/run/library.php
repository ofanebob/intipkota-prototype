<?php if ( !defined('BASEPATH')) header('Location:/404');
//require_once(BASEPATH.'/loader.php');

$param_library = Libs::inc(APPRUNPATH.'/library.inc');
Libs::load3rd($param_library);

$route = new Route();
?>