<?php if ( !defined('BASEPATH')) header('Location:/404');

/** @require File Module PHP (config.php, library.php) */
require_once(APPRUNPATH.'/library.php');

/** @param Mengambil data 'cari' dari dynamic URL */
$param = URLrules::get('cari');

/** @regex Untuk menghapus kata pertama di query 'cari' */
$param_all = CityRegex::test($param);

/** @control Menempatkan search control sebagai variable */
$search = SearchControl::html(array('domain'=>$domainfix,'addholder'=>$param));

/** @control Menempatkan menubar control sebagai variable */
$menubar = MenubarControl::html(array('domain'=>$domainfix,'currentcity'=>$param));

/** @css Menempatkan embed array variable */
$embed = array();

/** @css Menempatkan CSS variable */
$embed['css'] = GenTag::css(
				array('bootstrap'=>array('href'=>'bootstrap.css'),
					  'bootstrap-theme'=>array('href'=>'bootstrap-theme.css'),
					  'bootstrap-addons'=>array('href'=>'bootstrap-addons.css'),
					  'nprogress'=>array('href'=>'nprogress.css'),
					  'weather-icons'=>array('href'=>'weather-icons.css')
					  //'style'=>array('href'=>'style.css')
				)
				,'combine'
		);

/** @css Menempatkan CSS variable */
$embed['style'] = GenTag::css( array('href'=>'style.css') );

/** @js variable Menempatkan JavaScript variable */
$URI = $_SERVER['REQUEST_URI'];

$sitevarArray = array(
					'domain'=>$domainfix,
					'uri'=>$URI,
					'lang'=>$baselang,
					'is_mobile'=>Browser::is_mobile()
				);

$sitevar = json_encode($sitevarArray);

$embed['js_var'] = GenTag::script(array('sitevar'=>$sitevar), 'variable');

/** @js Menempatkan JavaScript variable */
$embed['library'] = GenTag::script(
					array('phpto'=>array('src'=>'phpto.js'),
						  'turbolinks'=>array('src'=>'turbolinks.js'),
						  'modernizr'=>array('src'=>'modernizr.js'),
						  'respond'=>array('src'=>'respond.js'),
						  //'angular'=>array('src'=>'angular.js'),
						  'nprogress'=>array('src'=>'nprogress.js'),
						  'jquery'=>array('src'=>'jquery-1.11.1.js'),
						  'jquery-md5'=>array('src'=>'jquery.md5.js'),
						  'jquery-cookie'=>array('src'=>'jquery.cookie.js'),
						  'jquery-browser'=>array('src'=>'jquery-browser.js'),
						  'jquery-turbolinks'=>array('src'=>'jquery-turbolinks.js'),
						  'bootstrap'=>array('src'=>'bootstrap.js'),
						  'jquery-visible'=>array('src'=>'jquery.visible.js'),
						  'jquery-waitforimages'=>array('src'=>'jquery.waitforimages.js'),
						  'enscroll'=>array('src'=>'enscroll.js'),
						  'jquery-lazy'=>array('src'=>'jquery.lazy.js'),
						  'jquery-notify'=>array('src'=>'jquery-notify.js'),
						  //'script'=>array('src'=>'script.js')
					)
					,'combine'
		);

$embed['script'] = GenTag::script( array('script'=>array('src'=>'script.js')) );

/** @Router Menangani URL frontpage */
$route->add('/', function()
{
	global $param, $param_all, $webname, $search, $embed, $menubar;

	if(URLrules::exist_get('cari'))
	{
		$GLOBALS['CURRENTPAGE'] = 'search='.$param;
		$GLOBALS['EXPIREPAGECACHE'] = strtotime('+12 Hour');

		$embedfoot = array(
						GenTag::script(
							array('varfoot'=>json_encode(array('data_page'=>'search'))), 'variable'
						),
						GenTag::script(
							array('src'=>'script-foot.js')
						)
					);

		/** @condition Jika terdapat query 'cari' pada dynamic URL */
		$param_theme = array('param_all'=>$param_all,
							 'domainfix'=>DOMAINFIX,
							 'webname'=>$webname,
							 'navbar'=>$search.$menubar
							);

		return GenTag::template(
					array('publictheme'=>'search',
						  'title'=>"Intip $param",
						  'embed'=>$embed,
						  'datapage'=>'search',
						  'footer'=>'',
						  'embedfoot'=>$embedfoot,
						  'param_theme'=>array(	'param_all'=>$param_all,
												'domainfix'=>DOMAINFIX,
												'webname'=>$webname,
												'navbar'=>$search.$menubar
											)
						)
				);
	}
	else
	{
		if(URLrules::exist_get_and_empty('cari'))
		{
			header("Location: failed");
		}
		else
		{
			$GLOBALS['CURRENTPAGE'] = 'home';
			$GLOBALS['EXPIREPAGECACHE'] = strtotime('+5 Day');

			$embedfoot = array(
							GenTag::css(
					  			array('href'=>'video-js.css')
							),
							GenTag::script(
								array('varfoot'=>json_encode(array('data_page'=>'home','paging'=>array('m'=>'control','c'=>'Suggest','f'=>'loopSuggest')))), 'variable'
							),
							GenTag::script(
								array(
								  'video'=>array('src'=>'video.js'),
								  'script-foot'=>array('src'=>'script-foot.js')
								)
								,'combine'
							)
						);

			$footer = FooterControl::build(array('webname'=>$webname));

			return GenTag::template(
						array('publictheme'=>'front',
							  'title'=>"$webname",
							  'embed'=>$embed,
							  'datapage'=>'home',
							  'footer'=>$footer,
							  'embedfoot'=>$embedfoot,
							  'param_theme'=>array(	'param_all'=>$param_all,
													'domainfix'=>DOMAINFIX,
													'webname'=>$webname,
													'navbar'=>$search
												)
							)
					);
		}
	}
});

/** @Router Menangani URL page dengan variable */
$route->add('/.+', function($root)
{
	global $param, $param_all, $webname, $embed, $domainfix;

	$get_city = URLrules::get_city($root);

	if(is_array($get_city))
	{
		$query = $get_city['city'];
		$template = $get_city['template'];

		$menubar = MenubarControl::html(array('domain'=>$domainfix,'currentcity'=>$query,'page'=>$template));
	}
	else
	{
		$query = $param;
		$template = $root;
		$menubar = '';
	}

	$GLOBALS['CURRENTPAGE'] = $root;
	$GLOBALS['EXPIREPAGECACHE'] = strtotime('+7 Day');

	$embedfoot = array(
					GenTag::script(
						array('varfoot'=>json_encode(array('data_page'=>$template))), 'variable'
					),
					GenTag::script(
						array('src'=>'script-foot.js')
					)
				);

	$search = SearchControl::html(array('domain'=>$domainfix,'addholder'=>$query));

	return GenTag::template(
				array('publictheme'=>$template,
					  'title'=>$webname,
					  'embed'=>$embed,
					  'datapage'=>$template,
					  'footer'=>'',
					  'embedfoot'=>$embedfoot,
					  'param_theme'=>array(	'param_all'=>$query,
											'domainfix'=>DOMAINFIX,
											'webname'=>$webname,
											'navbar'=>$search.$menubar,
											'currentcity'=>$query,
											'page'=>$template
										)
					)
			);
});

$route->execute();
?>