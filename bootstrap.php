<?php
require_once __DIR__ . "/consts.php";

require_once \BASE_PATH . "/vendor/autoload.php";
// require_once \BASE_PATH . "/src/redis/RedisUsersCache.php";
// require_once \BASE_PATH . "/config/RedisConfiguration.php";



function getRedisConfig(): \App\config\RedisConfiguration
{
    return new \App\config\RedisConfiguration(
        $databaseHost = 'redis',
        $databasePort = 6379,
    );
}


