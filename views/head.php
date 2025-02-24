<?
// path to config file
$org_dir = __DIR__ . '/../open-records-generator/';
$wsc_dir = '/web-assets-converter/';
$config_dir = $org_dir . "config/";
require_once($config_dir."config.php");

// specific to this 'app'
require_once($config_dir."url.php");
require_once($config_dir."request.php");
require_once($config_dir."org-settings.php");

// logged in user via .htaccess, .htpasswd
$user = $_SERVER['PHP_AUTH_USER'] ? $_SERVER['PHP_AUTH_USER'] : $_SERVER['REDIRECT_REMOTE_USER'];
$db = db_connect($user);

$oo = new Objects();
$mm = new Media();
$ww = new Wires();
$uu = new URL($urls);
$rr = new Request();

// $js_back = "javascript:history.back();";

// self
// $item = $oo->get($uu->id);

// am i using the ternary operator correctly?
// if this url has an id, get the associated object,
// else, get the root object
$name = "Web Assets Converter";

// document title
$title = $name;

// $nav = $oo->nav_clean($uu->ids);

// used in add.php, edit.php, browse.php
// $ancestors = $oo->ancestors($uu->id);

// if ($view == "logout")
// 	header("HTTP/1.1 401 Unauthorized");

?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $title; ?></title>
		<meta charset="utf-8">
		<!-- <meta name="description" content="anglophile"> -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="<? echo $wsc_dir; ?>static/css/main.css">
	</head>
	<body>
		<div id="app">
			