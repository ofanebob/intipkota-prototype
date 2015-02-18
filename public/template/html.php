<?php
header('Content-Type: text/html; charset=utf-8');

global $meta_setup;

if(DEVMODE != true)
{
	proto_html_compression_start();
}

isset($header) ? $header : '';

?>
<!DOCTYPE html>
<html lang="<?= BASELANG ?>" data-page="<?= isset($datapage) ? $datapage : 'home' ?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="author" content="<?= $meta_setup->webauthor ?>" />

<?= isset($meta) ? $meta : '' ?>

<link rel="apple-touch-icon" sizes="152x152" href="<?= DOMAINFIX ?>/public/images/logo-152.png"/>
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?= DOMAINFIX ?>/public/images/logo-144.png"/>
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?= DOMAINFIX ?>/public/images/logo-114.png"/>
<link rel="apple-touch-icon" sizes="76x76" href="<?= DOMAINFIX ?>/public/images/logo-76.png"/>
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?= DOMAINFIX ?>/public/images/logo-72.png"/>
<link rel="apple-touch-icon" sizes="60x60" href="<?= DOMAINFIX ?>/public/images/logo-60.png"/>
<link rel="apple-touch-icon" href="<?= DOMAINFIX ?>/public/images/logo-1024.png"/>
<!--[if IE]><link rel="shortcut icon" href="<?= DOMAINFIX ?>/public/images/logo-16.png"><![endif]-->
<meta name="msapplication-TileColor" content="#2DA8AD">
<meta name="msapplication-TileImage" content="<?= DOMAINFIX ?>/public/images/logo-16.png">
<link rel="shortcut icon" href="<?= DOMAINFIX ?>/public/images/logo-16.png"/>
<link rel="icon" href="<?= DOMAINFIX ?>/public/images/logo-16.png"/>

<title><?= isset($title) ? $title : 'PHP Protoype App' ?></title>

<?= isset($embed) ? is_array($embed) ? implode('',$embed) : $embed : '' ?>

</head>
<body class="no-overflow-x bg-white">

<?= isset($content) ? $content : 'Empty Content' ?>
<?= isset($footer) ? $footer : '' ?>
<?= isset($embedfoot) ? is_array($embedfoot) ? implode('',$embedfoot) : $embedfoot : '' ?>

</body>
</html>