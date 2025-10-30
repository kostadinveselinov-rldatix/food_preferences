<?php
// Prevent worker script termination when a client connection is interrupted
ignore_user_abort(true);

// Boot your app once outside the request loop
require __DIR__ . "/../bootstrap.php";
$container = require_once __DIR__ . "/../src/config/DependencyInjection.php";

$ignoredPaths = [
    '/favicon.ico',
    '/robots.txt',
    "/.well-known/appspecific/com.chrome.devtools.json",
    "/.well-known/assetlinks.json"
];
// Handler for each request
$handler = static function () use ($container, $ignoredPaths) {
    session_start();
    // Called when a request is received, superglobals are reset ($_GET, $_POST, $_SERVER, $_FILE, $_COOKIE)
    error_log("Session status: " . session_status());

    static $counter = 0;
    $counter++;
    error_log("Count from franken: $counter (PID: " . gethostname() . ")\n");

    $originalUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri = trim($originalUri);
    $uri = rtrim($uri, '/');
    $uri = str_replace(".php","", $uri);

    // Skip processing for ignored paths
    foreach ($ignoredPaths as $path) {
        if ($originalUri === $path) {
            http_response_code(404);
            return;
        }
    }

    if (str_starts_with($uri, "/api")) {
        require \BASE_PATH . "/public/routes/ApiRoutes.php";
    } else {
        require \BASE_PATH . "/public/routes/WebRoutes.php";
    }

    register_shutdown_function(function () {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
    });
};

// FrankenPHP worker loop
$maxRequests = (int)($_SERVER['MAX_REQUESTS'] ?? 0);
for ($nbRequests = 0; !$maxRequests || $nbRequests < $maxRequests; ++$nbRequests) {
    $keepRunning = frankenphp_handle_request($handler);
    // echo "keepRunning - " . $keepRunning;

    gc_collect_cycles();

    if (!$keepRunning) break;
}
