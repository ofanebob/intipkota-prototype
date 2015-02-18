<?php if ( !defined('BASEPATH')) header('Location:/404');
class FooterControl extends GenTag
{
	public static function build($param=false)
	{
		global $meta_setup;
		
		extract($param);

		$thisYear = date('Y');
		$Render = '
		<footer id="footer" class="container-fluid clearfix bg-primary">

			<div class="inner-side">
				<div class="small text-center d-inline-block w-100cent">
					<img class="v-align-middle" src="'.DOMAINFIX.'/public/images/logo-16-white.gif" alt="intikota" valign="absmiddle" width="16" height="16" />
					&nbsp;
					<strong>'.$webname.' &copy; '.$thisYear.'</strong>
					 &middot;
					'.$meta_setup->location.'
				</div>
			</div>

		</footer>

		<div id="back-top" class="position-fixed">
			<a href="javascript:void(0)" class="d-inline-block text-white important bg-primary">
				<span class="border-white border-2px border-all border-solid" style="border-radius: 50%;padding: 5px 6px;">
					<i class="glyphicon glyphicon-menu-up v-align-middle"></i>
				</span>
			</a>
		</div>
		';

		return $Render;
	}
}