<?php if ( !defined('BASEPATH')) header('Location:/404');

/** @template HTML tag dasar untuk memuat halaman pencarian */
$domainfix = $param_theme['domainfix'];
$webname = $param_theme['webname'];
$navbar = $param_theme['navbar'];

$current_city = isset($param_theme['currentcity']) ? $param_theme['currentcity'] : $GLOBALS['CURRENTCITY'];
$current_city = urldecode($current_city);

$city_wiki_query = CityRegex::wikiQuery($current_city);

$WikiModule = (new WikyInc)->parse(WikiModule::html($city_wiki_query));

if( $WikiModule == null )
{
	$header = header("Location: $domainfix/404");
}
else
{
$header = header("HTTP/1.0 200 Success");

$navbar = NavbarControl::html(
				array('domainfix'=>$domainfix,
					  'navbar'=>$param_theme['navbar'],
					  'webname'=>$webname
					)
				);

$content = <<<HTML
<div id="wrapper">
	{$navbar}

	<div class="container-fluid clearfix">
		{$WikiModule}
	</div>

</div>
HTML;
}
?>