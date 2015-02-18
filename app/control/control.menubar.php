<?php if ( !defined('BASEPATH')) header('Location:/404');
class MenubarControl extends GenTag
{
	public static function html($param=false)
	{
		if(is_array($param))
		{
			extract($param);

			$type = isset($param['type']) ? $param['type'] : 'menubutton';

			return self::$type($param);
		}
	}

	public static function menubutton($param=false)
	{
		extract($param);

		$current_city = isset($param['currentcity']) ? $currentcity : $GLOBALS['CURRENTCITY'];
		
		/** @match Menghapus text lain selain nama kota */
		//$city_split = CityRegex::test($currentcity);
		$city_split = CityRegex::trimcity($currentcity);
		$city_split = TrimString::simpleTruncate($city_split,false,'$1',1);

		$page = isset($param['page']) ? $page : '';

		$Render = '
		<div class="col-xs-12 col-lg-4">
			<div class="nav navbar-nav pull-right">
				<div class="collapse navbar-collapse" id="navbar-collapse-topmenu">
					<a href="'.$domain.'/map='.$current_city.'" class="navbar-btn btn btn-primary text-white important" data-no-turbolink '.($page == 'map' ? 'disabled' : '').'>
						<i class="glyphicon glyphicon-globe"></i> Peta '.$city_split.'
					</a>
					<a href="'.$domain.'/wiki='.$current_city.'" class="navbar-btn btn btn-primary text-white important '.($page == 'wiki' ? 'disabled' : '').'">
						<i class="glyphicon glyphicon-briefcase"></i> Wiki '.$city_split.'
					</a>
				</div>
			</div>
		</div>
		';

		return $Render;
	}

	public static function menutext()
	{
		global $webname;

		$Render = '
		<div class="col-xs-12 col-lg-4">
			<ul id="TopMenu" class="nav navbar-nav navbar-right">
				<li class="text-uppercase">
					<a href="javascript:void(0)" onclick="scrollToTag({tags:\'#kota-favorit\',direct:\'up\',speed:500})" id="favorite-city"><i class="glyphicon glyphicon-bookmark v-align-middle"></i> Kota Favorit</a>
				</li>
				<li class="dropdown">
				  <a href="#" class="dropdown-toggle text-uppercase" data-toggle="dropdown" role="button" aria-expanded="false">
				  	Menu <span class="caret"></span>
				  </a>
				  <ul class="dropdown-menu" role="menu">
				    <li><a href="about" id="about">Tentang '.$webname.'</a></li>
				    <li><a href="feedback">Beri Saran</a></li>
				    <li><a href="faq">F.A.Q</a></li>
				    <li class="divider"></li>
				    <li><a href="tos">Peraturan Layanan</a></li>
				    <li><a href="contact">Hubungi '.$webname.'</a></li>
				  </ul>
				</li>
			</ul>
		</div>
		';

		return $Render;
	}
}
?>