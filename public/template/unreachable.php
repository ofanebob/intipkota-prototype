<?php if ( !defined('BASEPATH')) header('Location:/404');
$header = header("HTTP/1.0 503 Unreachable");
$webname = $param_theme['webname'];
$navbar = $param_theme['navbar'];
$domain = DOMAINFIX;

$title = "Banyak Yang Ngintip! - $webname";

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
				<img src="{$domain}/public/images/unreachpage.gif" width="711" height="400" />
			</div>
			<h1 align="center" class="text-primary inner-separator">
				Hmm, Banyak Yang "Ngintip" Nih..
			</h1>
		</div>

	</div>
</div>
HTML;
?>