<?php
/* @default setting date location */
date_default_timezone_set("Asia/Jakarta");

/* @define Penamaan konstan untuk direktori */
define('BASEPATH', dirname(__FILE__));
define('BASENAME', basename(__DIR__));
define('LIBPATH', BASEPATH.'/lib');
define('PUBLICPATH', BASEPATH.'/public');
define('TEMPLATEPATH', PUBLICPATH.'/template');
define('TEMPLATEHTML', TEMPLATEPATH.'/html.php');
define('APPPATH', BASEPATH.'/app');
define('APPDATAPATH', APPPATH.'/data');
define('APPRUNPATH', APPPATH.'/run');
define('APPTESTPATH', APPPATH.'/test');
define('MODULEPATH', APPPATH.'/module');
define('CONTROLPATH', APPPATH.'/control');

/* @global variable default untuk nilai GLOBALS */
$GLOBALS['CURRENTCITY'] = isset($_GET['p']) ? $_GET['p'] : 'undefined city';
$GLOBALS['CURRENTWEATHER'] = '';
$GLOBALS['CURRENTVENUE'] = array();
$GLOBALS['CURRENTPAGE'] = '';
$GLOBALS['EXPIREPAGECACHE'] = '';

/* @var Alamat default metadata.json */
$metadata = BASEPATH.'/metadata.json';

/* @cond Cek ketersediaan file metadata.json */
if(file_exists($metadata))
{
	$GLOBALS['METADATA'] = json_decode(@file_get_contents($metadata));
}

/* @cond Cek index array metadata pada GLOBALS */
if(isset($GLOBALS['METADATA']))
{
	/* @var Mengambil isi require di file metadata melalui nilai GLOBAL */
	$REQUIRELIB = $GLOBALS['METADATA']->require;

	/* @global Mengubah nilai default requirelib pada Global */
	$GLOBALS['REQUIRELIB'] = $REQUIRELIB;

	/* @var Mengambil isi setup di file metadata melalui nilai Global */
	$meta_setup = $GLOBALS['METADATA']->setup;

	/* @ternary Menenukan variable $sitetest dengan indentifikasi nilai host dari referensi $meta_setup */
	$siteset = (@$meta_setup->host ? $meta_setup->host : exit('Invalid Setup Host'));

	/* @define Penamaan konstan untuk Prefix Host */
	define('PREFIXHOST', (@$siteset->prefix ? $siteset->prefix : 'http://'));

	/* @define Penamaan konstan untuk Domain Fix */
	define('DOMAINFIX', PREFIXHOST.'/'.BASENAME);
	$domainfix = DOMAINFIX;

	/* @ternary Variable $mode untuk meta setup mode develop/online */
	$mode = (@$meta_setup->mode ? $meta_setup->mode : exit('Invalid Setup Mode'));
	define('DEVMODE',$mode->developing);

	/* @ternary Variable $webname untuk default name website */
	$webname = (@$meta_setup->webname ? $meta_setup->webname : exit('Please Add Web Name into metadata'));

	define('BASELANG', (@$meta_setup->lang->base ? $meta_setup->lang->base : 'id'));
	define('CODELANG', (@$meta_setup->lang->code ? $meta_setup->lang->code : 'id_ID'));
	$baselang = BASELANG;
	$codelang = CODELANG;

	define('DEFAULT_ICON_VENUE_SIZE', (@$meta_setup->icon_venue->size ? $meta_setup->icon_venue->size : 32));
	define('DEFAULT_ICON_VENUE_BG', (@$meta_setup->icon_venue->background ? $meta_setup->icon_venue->background : ''));

	define('LIMITSUGGEST', (@$meta_setup->limitsuggest ? $meta_setup->limitsuggest : 1));

	/* @ternary Load library file */
	$loadlib = BASEPATH.'/lib/loadlib.php';
	file_exists($loadlib) ? require_once($loadlib) : exit('Library invalid.');
}
else
{
	exit('Metadata invalid.');
}
?>