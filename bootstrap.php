<?php

define('BASE_PATH', dirname(__DIR__) . "/www");
define("APP_URL", "http://localhost:8080/");

require_once BASE_PATH . "/vendor/autoload.php";
$entityManager = BASE_PATH . '/config/EntityManagerConfig.php';