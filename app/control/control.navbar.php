<?php if ( !defined('BASEPATH')) header('Location:/404');
class NavbarControl extends GenTag
{
	public static function html($param=false)
	{
		if(is_array($param))
		{
			extract($param);

			$addClass = isset($param['addclass']) ? $addclass : 'navbar-static-top';

			$r = '
				<nav id="TopNavbar" class="navbar '.$addClass.' navbar-default navbar-primary bg-primary z-index-999 nomargin" role="navigation">
					<div class="container-fluid">
						<div class="col-lg-2">
							<a class="navbar-brand text-white important" href="'.$domainfix.'/" title="'.$webname.'">
								<img src="'.$domainfix.'/public/images/title-font.png" alt="'.$webname.'" width="120" height="32" />
							</a>
						</div>
						'.$navbar.'
					</div>
				</nav>
			';
			return $r;
		}
	}
}
?>