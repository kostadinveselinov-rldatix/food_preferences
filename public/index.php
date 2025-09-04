<?php
// skip session and rate limiting for ignored paths
$ignoredPaths = [
    '/favicon.ico',
    '/robots.txt',
    "/.well-known/appspecific/com.chrome.devtools.json",
    "/.well-known/assetlinks.json"
];

$originalUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = trim($originalUri);
$uri = rtrim($uri, '/');
$uri = str_replace(".php","", $uri);

foreach ($ignoredPaths as $path) {
    if ($originalUri === $path) {
        die();
    }
}

require_once __DIR__ . "/../bootstrap.php";
$container = require_once __DIR__ . "/../src/config/DependencyInjection.php";

if (str_starts_with($uri, "/api")) {
    require_once \BASE_PATH . "/public/routes/ApiRoutes.php";
} else {
    require_once \BASE_PATH . "/public/routes/WebRoutes.php";
}

die();