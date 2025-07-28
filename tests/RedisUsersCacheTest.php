<?php
declare(strict_types= 1);

use PHPUnit\Framework\TestCase;
use \App\cache\redis\RedisUsersCache;
use \App\config\RedisConfiguration;
use \App\Entity\User;
use \Predis\Client as PredisClient;

final class RedisUsersCacheTest extends TestCase
{
    private $redisClientMock;
    private $cache;
    private $redisConfigMock;

    
}