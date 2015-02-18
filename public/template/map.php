<?php if ( !defined('BASEPATH')) header('Location:/404');

/** @template HTML tag dasar untuk memuat halaman pencarian */
$domainfix = $param_theme['domainfix'];
$webname = $param_theme['webname'];
$navbar = $param_theme['navbar'];

$current_city = isset($param_theme['currentcity']) ? $param_theme['currentcity'] : $GLOBALS['CURRENTCITY'];
$current_city = urldecode($current_city);

$navbar = NavbarControl::html(
				array('domainfix'=>$domainfix,
					  'navbar'=>$param_theme['navbar'],
					  'webname'=>$webname
					)
				);

$venueMap = MapModule::get($current_city);

if($venueMap==null)
{
	header("Location: $domainfix/404");
}
else
{
$header = header("HTTP/1.0 200 Success");

$venueLL = $GLOBALS['CURRENTVENUE']['LATLNG'];

$foursquare = FoursquareModule::get($param_theme['param_all'],'map',0,false);

/** @embed foot */
$embedfoot = array();

$param = array('key_file'=>'foursquare.api.json','random'=>true);
$key = JsonKeyExtractor::get($param);

$clientid = $key['api_id'];
$clientsecret = $key['api_secret'];

$icon_venue_size = DEFAULT_ICON_VENUE_SIZE;
$icon_venue_bg = DEFAULT_ICON_VENUE_BG;

$arrayScriptVars = array('date'=>date('Ymd', strtotime('now')),
						 'SizeIcon'=>$icon_venue_size,
						 'BgIcon'=>$icon_venue_bg,
						 'clid'=>$clientid,
						 'clsc'=>$clientsecret
						);

$embedfoot['variable'] = GenTag::script(
				array('foursquare'=>$venueMap,
					  'geolocation'=>$venueLL,
					  'vars'=>json_encode($arrayScriptVars)
					)
					,'variable'
			);

$embedfoot['map'] = GenTag::script(
					array('google-map'=>array('path'=>'dynamic','src'=>'http://maps.google.com/maps/api/js?v=3&#038;sensor=false&#038;libraries=places')
				)
			);

$embedfoot['library'] = GenTag::script(
				array(//'google-map'=>array('path'=>'dynamic','src'=>'http://maps.google.com/maps/api/js?v=3&#038;sensor=false&#038;libraries=places'),
					  'markerclusterer'=>array('src'=>'markerclusterer.js'),
					  'jquery-ui-map'=>array('src'=>'jquery.ui.map3-rc.js'),
					  //'marker.with.label'=>array('src'=>'marker.with.label.js'),
					  //'jquery-notify'=>array('src'=>'jquery-notify.js'),
					  //'geomodal'=>array('src'=>'fn.geomodal.js'),
					  //'geomap'=>array('src'=>'geomap.js')
				)
				,'combine'
			);

$embedfoot['script'] = GenTag::script(
				array('geomodal'=>array('src'=>'fn.geomodal.js'),
					  'geomap'=>array('src'=>'geomap.js')
				)
			);

$title = 'Peta '.$current_city.' | '.$title;

$meta = GenTag::meta(
			array(
				'title'=>array('content'=>html_entity_decode($title)),
				'keyword'=>array('content'=>$GLOBALS['CURRENTVENUE']['LISTS']),
				'description'=>array('content'=>$GLOBALS['CURRENTVENUE']['DESCRIPTION']),
			)
		);

$content = <<<HTML
<div id="wrapper">
	{$navbar}

	<div class="container-fluid clearfix bg-white">

		<div class="clearfix">

			<div class="col-lg-9 nopadding border-primary border-right border-4px border-solid">
				<div id="MapCanvas" class="position-relative"></div>
			</div>

			<div class="col-lg-3 nopadding">

				<div id="FilterVenue" style="margin:15px 0 0 15px">
					<div class="navbar-form nopadding nomargin" role="filter">
						<div class="input-group w-100cent">
							<input id="filtersearchbox" class="form-control" placeholder="Filter Tempat (contoh: Hotel)" name="filter" type="text">
						</div>
					</div>
				</div>

				<div id="sideright" class="nopadding border-smoke border-right border-1px border-solid">
					<div id="scollVenue" style="padding:15px">
						{$foursquare}
					</div>
				</div>
			</div>

		</div>

	</div>
</div>
HTML;
}
?>