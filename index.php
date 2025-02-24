<?php
$request = $_SERVER['REQUEST_URI'];
$requestclean = strtok($request,"?");
$uri = explode('/', $requestclean);

require_once("views/head.php");
if (!isset($uri[2]) || !$uri[2])
    require_once("views/browse.php");
else require_once("views/$uri[2].php");
require_once("views/foot.php");
?>
