<?php if ( !defined('BASEPATH')) header('Location:/404');

/** @template HTML tag dasar untuk memuat halaman pencarian */
$domainfix = $param_theme['domainfix'];
$webname = $param_theme['webname'];
$navbar = $param_theme['navbar'];

$current_city = isset($param_theme['currentcity']) ? $param_theme['currentcity'] : $GLOBALS['CURRENTCITY'];
$current_city = urldecode($current_city);
$navbar = NavbarControl::html(
				array('domainfix'=>$domainfix,
					  'navbar'=>'',
					  'webname'=>$webname
					)
			);

$footer = FooterControl::build(array('webname'=>$webname));

$content = <<<HTML
<div id="wrapper">
	{$navbar}

	<div class="container-fluid clearfix">

		<div id="wiki-page" class="nomargin">
			<h2 class="page-header inner-separator nomargin">Tentang Intip Kota</h2>
			<p>
				Lo-fi deep v shabby chic, chillwave trust fund polaroid flexitarian mixtape Portland craft beer. Polaroid lo-fi bitters mixtape chillwave Austin, McSweeney’s hashtag freegan drinking vinegar crucifix typewriter gentrify farm-to-table. Distillery Kickstarter quinoa small batch synth. Wolf freegan lo-fi, Truffaut art party typewriter pop-up wayfarers master cleanse actually lomo flexitarian. Cred lo-fi blog, slow-carb fap Shoreditch Blue Bottle typewriter fixie Pitchfork. Locavore DIY banjo normcore Echo Park, PBR&B gastropub. Distillery vegan art party chillwave.

				Blue Bottle High Life ethnic blog Kickstarter. YOLO pickled Helvetica narwhal, fashion axe +1 paleo gastropub asymmetrical. Pickled kale chips Intelligentsia keytar post-ironic, Helvetica authentic Marfa scenester banh mi you probably haven’t heard of them PBR. Schlitz scenester locavore slow-carb pour-over, wayfarers Tumblr pug sustainable cardigan. Gluten-free Portland wayfarers direct trade. Tofu skateboard meh, PBR sustainable tattooed next level salvia tote bag Odd Future. Intelligentsia retro gluten-free, cornhole Austin American Apparel PBR&B cardigan.
			</p>
		</div>

	</div>
</div>
HTML;
?>