<?php
namespace App;
require_once __DIR__ . "/../bootstrap.php";
$container = require_once __DIR__ . "/../src/config/DependencyInjection.php";


$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = trim($uri);
$uri = rtrim($uri, '/');
$uri = str_replace(".php","",$uri);

if(str_starts_with($uri,"/api")){
    require_once \BASE_PATH . "/public/routes/ApiRoutes.php";
}else{
    require_once \BASE_PATH . "/public/routes/WebRoutes.php";
}
die();

