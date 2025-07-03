<?php
$request = $_SERVER['REQUEST_URI'];
$requestclean = strtok($request,"?");
$uri = explode('/', $requestclean);
if(count($uri) > 2 && empty(end($uri))) array_pop($uri);

if(count($uri) >= 4 && $uri[2] === 'api') {
    $endpoint_name = $uri[3];
    require_once(__DIR__ . "/api/$endpoint_name.php");
} else {
    require_once("views/head.php");
    if(count($uri) === 3 && $uri[2] === 'init')
        require_once("views/init.php");
    else
        require_once("views/main.php");
    require_once("views/foot.php");
}

?>
