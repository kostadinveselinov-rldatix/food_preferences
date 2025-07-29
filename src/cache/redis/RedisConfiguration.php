<?php
namespace App\cache\redis;

class RedisConfiguration
{
    public function __construct(
        public string $scheme,
        public string $databaseHost,
        public int $databasePort,
        public string $password,
        public int $databaseConnection)
    {
    }
}