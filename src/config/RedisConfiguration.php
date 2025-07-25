<?php
namespace App\config;

class RedisConfiguration
{
    public string $databaseHost;
    public int $databasePort;

    public function __construct(string $databaseHost, int $databasePort)
    {
        $this->databaseHost = $databaseHost;
        $this->databasePort = $databasePort;
    }
}