<?php
define('BASE_PATH', dirname(__DIR__) . "/www");
define("APP_URL", "http://localhost:8080/");

require_once \BASE_PATH . "/vendor/autoload.php";
require_once \BASE_PATH . "/cache/redis/RedisUsersCache.php";
require_once \BASE_PATH . "/config/RedisConfiguration.php";



function getRedisConfig(): \App\configuration\RedisConfiguration
{
    return new \App\configuration\RedisConfiguration(
        $databaseHost = 'redis',
        $databasePort = 6379,
    );
}


