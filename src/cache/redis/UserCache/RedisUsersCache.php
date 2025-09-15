<?php

namespace App\cache\redis\UserCache;

use App\cache\redis\UserCache\IUsersCache;
use \App\Entity\User;
use \Predis\Client as PredisClient;

class RedisUsersCache implements IUsersCache
{
    private string $keyPrefix = "user_key_";
    public function __construct(private PredisClient $redisClient, private int $ttl)
    {
    }

    public function storeUser(User $user): void
    {
        if($user->getId() != null) {
            $key = $this->keyPrefix . $user->getId();
         
            $this->redisClient->set($key, serialize($user), 'EX', $this->ttl);

            // invalidate cache for all users
            $this->invalidateAllUsersCache();
        }
    }

    public function storeUsers(array $users,string $key = "all"): void
    {
        if(!empty($users)){
            $this->redisClient->set($this->keyPrefix . $key, serialize($users), 'EX', $this->ttl);
        }
    }

    public function getUser(string $key): ?User
    {
        $key = $this->keyPrefix . $key;
        $loadedFromCache = $this->redisClient->get($key);
        if ($loadedFromCache == null)
        {
            return null;
        }

        return unserialize($loadedFromCache);
    }

    public function getUsers(string $key): array | null
    {
        $key = $this->keyPrefix . $key;
        $loadedFromCache = $this->redisClient->get($key);
        
        if ($loadedFromCache == null)
        {
            return null;
        }
   
        return unserialize($loadedFromCache);
    }

    public function deleteUser(string $key): void
    {
        $this->redisClient->del([$this->keyPrefix . $key]);
        $this->invalidateAllUsersCache();
    }

    private function invalidateAllUsersCache(): void
    {
        if($this->redisClient->exists($this->keyPrefix . "all")) {
            $this->redisClient->del([$this->keyPrefix . "all"]);
        }
    }
}