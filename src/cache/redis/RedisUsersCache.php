<?php

namespace App\cache\redis;

use App\cache\redis\IUsersCache;
use \App\Entity\User;
use \Predis\Client as PredisClient;
use \App\config\RedisConfiguration;

class RedisUsersCache implements IUsersCache
{

    private PredisClient $redisClient;

    public function __construct(RedisConfiguration $redisConfiguration)
    {

        $this->redisClient = new PredisClient([
            'scheme' => 'tcp',
            'host' => $redisConfiguration->databaseHost,
            'port' => $redisConfiguration->databasePort,
            'password' => '',
            'database' => 0,
        ]);
    }

    public function storeUser(User $user): void
    {
        if($user->getId() != null) {
            $key = 'user_key_' . $user->getId();
            $ttl = 60;
            $this->redisClient->set($key, serialize($user), 'EX', $ttl);
        }
    }

    public function storeUsers(string $key,array $users): void
    {
        $key = 'user_key_' . $key;
        $ttl = 60;

        if(!empty($users)){
            $this->redisClient->set($key, serialize($users), 'EX', $ttl);
        }
    }

    public function getUser(string $key): ?User
    {
        $key = 'user_key_' . $key;
        $loadedFromCache = $this->redisClient->get($key);
        if ($loadedFromCache == null)
            return null;
        else {
            return unserialize($loadedFromCache);
        }
    }

    public function getUsers(string $key): array | null
    {
        $key = "user_key_" . $key;
        $loadedFromCache = $this->redisClient->get($key);
        
        if ($loadedFromCache == null)
        {
            return null;
        }
        else {
            return unserialize($loadedFromCache);
        }
    }

}