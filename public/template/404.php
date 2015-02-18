<?php if ( !defined('BASEPATH')) header('Location:/404');
$header = header("HTTP/1.0 404 Not Found");
$webname = $param_theme['webname'];
$navbar = $param_theme['navbar'];
$domain = DOMAINFIX;

$title = "Aduh Galat! - $webname";

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
			<div class="image-error text-center w-100cent separator">
				<img src="{$domain}/public/images/not-found.gif" width="711" height="450" />
			</div>
			<h1 align="center" class="text-primary inner-separator">
				Aduh Sepertinya Terjadi Galat
			</h1>
		</div>

	</div>
</div>
HTML;
?>