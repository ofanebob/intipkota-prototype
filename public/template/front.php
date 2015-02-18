<?php if ( !defined('BASEPATH')) header('Location:/404');

global $meta_setup;

//echo json_encode(NewsGoogleFeed::_RequestData(array('query'=>'Bandung, Jawa Barat')));

//echo strtotime('-1 Year');

/** @template HTML tag dasar untuk memuat halaman pencarian */

$domainfix = $param_theme['domainfix'];
$webname = $param_theme['webname'];

$pushMenubar = array_merge($param_theme,array('type'=>'menutext'));

$menubar = MenubarControl::html($pushMenubar);

$navbar = NavbarControl::html(
			  array('domainfix'=>$domainfix,
					'navbar'=>$param_theme['navbar'].$menubar,
					'webname'=>$webname,
					//'addclass'=>'navbar-fixed-top'
			  )
		);

$keyword = $meta_setup->keywords;
$description = $meta_setup->description;

$meta = GenTag::meta(
			array(
				'keyword'=>array('content'=>implode(', ', $keyword)),
				'description'=>array('content'=>$description),
			)
		);

$suggest = SuggestControl::build(array('pages'=>0));

$labelSuggest = SuggestControl::labelSuggest(array('limit'=>60,'orderby'=>'rand()'));

//$geoplugin = new geoPlugin();
//$geoplugin = $geoplugin->locate('36.72.96.174');

//var_dump($geoplugin);

$content = <<<HTML
<div id="wrapper" class="bg-gray-lighter" style="padding: 0 0 20px 0;margin-top:50px">

	{$navbar}

	<div class="container-fluid clearfix">

		<div id="topside" class="col-lg-12 nopadding marginspace-bottom clearfix">
			<div class="inner-side">
			<center>
				<h3 class="text-primary text-uppercase marginspace-bottom">
					<i class="glyphicon glyphicon-facetime-video v-align-top"></i> 
					Baru di {$webname}? simak video nya!
				</h3>

				<div class="embed-responsive">
					<div class="thumbnail shaping nomargin d-inline-block">
						<video 
						controls="controls" 
						preload="auto" 
						width="900" 
						height="505" 
						id="footage-video" 
						class="video-js vjs-default-skin vjs-big-play-centered embed-responsive-item" 
						poster="{$domainfix}/public/images/video-snap-intipkota.jpg"
						>
							<!-- source src="sintel-trailer.ogv" type='video/ogg; codecs="theora, vorbis"' -->
							<source src="{$domainfix}/public/media/teaser-intipkota.com.mp4" type='video/mp4; codecs="avc1.42E01E, mp4a.40.2"'>
						</video>
					</div>
				</div>
			</center>
			</div>
		</div>

		<div id="kota-favorit" class="border-1px border-solid border-smoke border-bottom marginspace-bottom clearfix d-inline-block w-100cent"></div>

	</div>

	<div class="container-fluid clearfix marginspace-top">

		<div class="row nomargin bg-white border-smoke border-all border-1px border-solid">

			<div id="sideleft" class="col-lg-8 nopadding border-smoke border-right border-1px border-solid">
				<h3 class="bg-info text-info inner-side nomargin">
					<i class="glyphicon glyphicon-bookmark v-align-top"></i> KOTA FAVORIT
				</h3>
				<div class="inner-side">
					{$suggest}
				</div>
			</div>

			<div id="sideright-home" class="col-lg-4 nopadding">
				<div class="inner-side d-inline-block">
					{$labelSuggest}
				</div>
			</div>

		</div>

	</div>

</div>
HTML;
?>