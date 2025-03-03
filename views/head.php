<?
// path to config file

$wac_config_dir = __DIR__ . '/../config/';
require_once($wac_config_dir."config.php");
$wac_dir = $site_url . '/web-assets-converter/';

$org_dir = __DIR__ . '/../../open-records-generator/';
$org_config_dir = $org_dir . "config/";
require_once($org_config_dir."config.php");
require_once($org_config_dir."request.php");

// logged in user via .htaccess, .htpasswd
$user = $_SERVER['PHP_AUTH_USER'] ? $_SERVER['PHP_AUTH_USER'] : $_SERVER['REDIRECT_REMOTE_USER'];
$db = db_connect($user);

$oo = new Objects();
$mm = new Media();
$ww = new Wires();
$name = "Web Assets Converter";

// document title
$title = $name;

$css = array( 'main', 'wac' );
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $title; ?></title>
		<meta charset="utf-8">
		<!-- <meta name="description" content="anglophile"> -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<?php foreach($css as $c): 
			$query = filemtime(__DIR__ . '/../static/css/' . $c . '.css');
		?>
			<link id="<?php echo $c; ?>-style" rel="stylesheet" href="<? echo $wac_dir; ?>static/css/<?php echo $c; ?>.css?v=<?php echo $query; ?>">
		<?php endforeach; ?>
		<script src="<?php echo $wac_dir; ?>static/js/_cookie.js"></script>
	</head>
	<body>
		<div id="app">
			