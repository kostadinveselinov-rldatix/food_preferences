<?php
define('BASE_PATH',"/app");
define("APP_URL", "http://localhost:8080/");
define('REPORTS_PATH', \BASE_PATH . '/src/reports');

// Database
define('DB_HOST', 'db');
define('DB_NAME', 'test');
define('DB_USER', 'root');
define('DB_PASSWORD', 'root');

// Redis
define('REDIS_SCHEME', 'tcp');
define('REDIS_HOST', 'redis');
define('REDIS_PORT', 6379);
define('REDIS_PASSWORD', '');
define('REDIS_DB_CONNECTION', 0);