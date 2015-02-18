<?php if ( !defined('BASEPATH')) header('Location:/404');
$webname = $param_theme['webname'];
$navbar = $param_theme['navbar'];
$currentcity = $param_theme['currentcity'];

$header = header("HTTP/1.0 404 Not Found");
$title = empty($currentcity) ? 'Kotanya Hilang!' : "Kota \"$currentcity\" Tidak Ditemukan!";
$title = "$title | $webname";

$domain = DOMAINFIX;

$search = SearchControl::html(
				array('domain'=>$domain,
					  'addholder'=>'',
					  'class'=>''
					)
		);

$navbar = NavbarControl::html(
				array('domainfix'=>DOMAINFIX,
					  'navbar'=>'',
					  'webname'=>$webname
					)
		);

$footer = FooterControl::build(array('webname'=>$webname));

$content = <<<HTML
<div id="wrapper">

	{$navbar}

	<div class="container clearfix">

		<div class="celarfix">
			<div class="image-error text-center w-100cent">
				<img src="{$domain}/public/images/errorpage.gif" width="711" height="400" />
			</div>
			<div class="well nomargin">
				{$search}
			</div>
		</div>

	</div>
</div>
HTML;
?>