<?php
namespace App\cache\redis;

class RedisConfiguration
{
    public function __construct(
        public string $scheme = "tcp",
        public string $databaseHost = "redis",
        public int $databasePort = 6379,
        public string $password = "",
        public int $databaseConnection = 0)
    {
    }
}