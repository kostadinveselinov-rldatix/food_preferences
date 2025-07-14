<?php
namespace App\configuration;

class RedisConfiguration
{
    public string $databaseHost;
    public int $databasePort;

    public function __construct(string $databaseHost, string $databasePort)
    {
        $this->databaseHost = $databaseHost;
        $this->databasePort = $databasePort;
    }
}