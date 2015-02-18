<?php if ( !defined('BASEPATH')) header('Location:/404');
global $param;

/** @template HTML tag dasar untuk memuat halaman pencarian */
$domainfix = $param_theme['domainfix'];

$param_all = $param_theme['param_all'];

/* @var Foursquare Module */
$foursquare = FoursquareModule::get($param);

/* @var Weather Module */
$weather = WeatherModule::get($param_all);

$prefixNews = preg_match('/(indonesia|malaysia|singa(pore|pura)|brunei)/', strtolower($param)) ? 
				'Wisata '.$param : $param.' Travel';

/* @var News Module */
$news = NewsModule::get( ucwords($param) );

/* @var News Module */
$panoramio = PanoramioModule::get($param);

//var_dump($panoramio);
if( $weather !== null && $foursquare !== null && $news !== null)
{
	/* @Module Save Searching Value for suggest City */
	SearchModule::cache( array( 'param'=>$param, 'ip'=>Browser::get_ip() ) );
}

if( $foursquare == null )
{
	$header = header("Location: $domainfix/failed=$param_all");
}
else
{
$weather = is_null($weather) ? 
			GenTag::panelTag(array('message'=>"Maaf! Tidak ada prakiraan cuaca untuk $param")) 
			: $weather;

$news = is_null($news) ? 
			GenTag::panelTag(array('message'=>"Maaf! Tidak ada berita untuk $param",'class'=>'')) 
			: $news;

/* @var Set Header if Success */
$header = header("HTTP/1.0 200 Success");

$webname = $param_theme['webname'];

$navbar = NavbarControl::html(
				array('domainfix'=>$domainfix,
					  'navbar'=>$param_theme['navbar'],
					  'webname'=>$webname
					)
				);

$description = array($GLOBALS['CURRENTWEATHER'],
				 	 $GLOBALS['CURRENTVENUE']['DESCRIPTION']
				);

$meta = GenTag::meta(
			array(
				'keyword'=>array('content'=>$GLOBALS['CURRENTVENUE']['LISTS']),
				'description'=>array('content'=>implode('. ', $description)),
			)
		);

$total_venue = isset($GLOBALS['CURRENTVENUE']['TOTAL']) ? 
				'<span class="label label-info">Total '.$GLOBALS['CURRENTVENUE']['TOTAL'].' Loka</span>' : '';

/** @embed foot */
$embedpage = array();

$key = Foursquare::_api_key();

$clientid = $key['api_id'];
$clientsecret = $key['api_secret'];

$icon_venue_size = DEFAULT_ICON_VENUE_SIZE;
$icon_venue_bg = DEFAULT_ICON_VENUE_BG;

$date = date('Ymd');

$panoramioVariable = PanoramioControl::variable($param);
//$venueMap = MapModule::get($param);
$venueMap = MapControl::variable($param);

$arrayScriptVars = array('date'=>$date,
						 'currentCity'=>$param,
						 'SizeIcon'=>$icon_venue_size,
						 'BgIcon'=>$icon_venue_bg,
						 'clid'=>$clientid,
						 'clsc'=>$clientsecret
						);

$embedpage['variable'] = GenTag::script(
				array(
					'panoramio'=>$panoramioVariable,
					'vars'=>json_encode($arrayScriptVars),
					'foursquare'=>$venueMap
					)
				,'variable'
			);

$embedpage['library'] = GenTag::script(
				array('src'=>'fn.geomodal.js')
			);

$embedfoot = array_merge($embedpage,$embedfoot);

$content = <<<HTML
<div id="wrapper" class="bg-gray-lighter">
	{$navbar}

	<div id="title_port" class="row text-primary bg-gray-blind nomargin border-solid border-primary border-1px border-bottom">

		<div class="column col-lg-2 nopadding">
			<h4 class="text-center nomargin">
				<i class="forecast-icon wi wi-stormshowers"></i> Cuaca
			</h4>
		</div>

		<div class="column col-lg-3 nopadding nomargin">
			<h4 class="text-center nomargin">
				<i class="glyphicon glyphicon-bullhorn"></i> Berita
			</h4>
		</div>

		<div class="column col-lg-2 nopadding nomargin">
			<h4 class="text-center nomargin">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<i class="glyphicon glyphicon-picture"></i> Foto
			</h4>
		</div>

		<div class="column col-lg-5 nopadding">
			<h4 class="text-center nomargin">
				<i class="glyphicon glyphicon-map-marker"></i> Tempat 
				{$total_venue}
			</h4>
		</div>

	</div>

	<div class="container-fluid clearfix">

		<div id="ContainerSide" class="row nomargin bg-white border-smoke border-left-right border-1px border-solid">
			<div id="sideleft" class="col-lg-2 no-overflowv nopadding">
				<div class="inner-side">{$weather}</div>
			</div>

			<div id="insideleft" class="col-lg-3 nopadding border-smoke border-left-right border-1px border-solid">
				<div class="inner-side">{$news}</div>
			</div>

			<div id="insideright" class="col-lg-3 nopadding border-smoke border-right border-1px border-solid">
				<div class="inner-side">{$panoramio}</div>
			</div>

			<div id="sideright" class="col-lg-4 nopadding border-smoke border-right-left border-1px border-solid">
				<div class="inner-side">{$foursquare}</div>
			</div>
		</div>

	</div>

</div>
HTML;
}
?>