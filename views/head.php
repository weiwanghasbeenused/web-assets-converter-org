<?php
require_once(__DIR__ . '/../utils/vendor/autoload.php');
try{
	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
} catch(Exception $err) {
	die('.env doesnt exist');
}
$dotenv->load();
if(!$_ENV['MEOW_URL'])
	die('MEOW_URL is not set in .env');
$environment = $_ENV['ENVIRONMENT'] ?? 'development';
$meow_url = $_ENV['MEOW_URL'];
require_once(__DIR__ . '/../utils/loadConfig.php');
$config = loadConfig();
$endpoints = array(
	'list' => $meow_url . '/api/list',
	'delete' => $meow_url . '/api/delete',
	'update' => $meow_url . '/api/update'
);
$user = $_SERVER['PHP_AUTH_USER'] ? $_SERVER['PHP_AUTH_USER'] : $_SERVER['REDIRECT_REMOTE_USER'];

$name = "Media-Encoding Optimizer for the Web";

// document title
$title = $name;

$css = array( 'main', 'wac', 'select', 'icon' );
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
			<link id="<?php echo $c; ?>-style" rel="stylesheet" href="<? echo $meow_url; ?>/static/css/<?php echo $c; ?>.css?v=<?php echo $query; ?>">
		<?php endforeach; ?>
		<script src="<?php echo $meow_url; ?>/static/js/utils/_cookie.js"></script>
	</head>
	<body>
		<script src="<?php echo $meow_url; ?>/static/js/utils/_sniffing.js"></script>