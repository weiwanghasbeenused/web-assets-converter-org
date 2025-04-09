<?php
$request = $_SERVER['REQUEST_URI'];
$requestclean = strtok($request,"?");
$uri = explode('/', $requestclean);
if(count($uri) >= 4 && $uri[2] === 'api') {
    $endpoint_name = $uri[3];
    require_once(__DIR__ . "/static/php/api/$endpoint_name.php");
} else {
    require_once("views/head.php");
    require_once("views/main.php");
    require_once("views/foot.php");
}

?>
