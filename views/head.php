<?php
require_once(__DIR__ . '/../static/php/vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
// path to config file
$wac_config_dir = __DIR__ . '/../config/';
require_once($wac_config_dir."config.php");
$wac_url = $site_url . '/web-assets-converter/';
$endpoints = array(
	'list' => $wac_url . 'static/php/api/list.php',
	'delete' => $wac_url . 'static/php/api/delete.php',
	'update' => $wac_url . 'static/php/api/update.php'
);

require_once(__DIR__ . '/../static/php/functions.php');
require_once($org_config_dir."config.php");

$user = $_SERVER['PHP_AUTH_USER'] ? $_SERVER['PHP_AUTH_USER'] : $_SERVER['REDIRECT_REMOTE_USER'];
$db = db_connect($user);

$oo = new Objects();
$mm = new Media();
$ww = new Wires();
$name = "Web Assets Converter";

// document title
$title = $name;

$css = array( 'main', 'wac', 'select' );
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
			<link id="<?php echo $c; ?>-style" rel="stylesheet" href="<? echo $wac_url; ?>static/css/<?php echo $c; ?>.css?v=<?php echo $query; ?>">
		<?php endforeach; ?>
		<script src="<?php echo $wac_url; ?>static/js/utils/_cookie.js"></script>
	</head>
	<body>
		<script src="<?php echo $wac_url; ?>static/js/utils/_sniffing.js"></script>