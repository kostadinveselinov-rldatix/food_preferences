<?php

namespace App\cache\redis\UserCache;

use App\cache\redis\UserCache\IUsersCache;
use \App\Entity\User;
use App\Hydrators\UserHydrator;
use \Predis\Client as PredisClient;

// implementation without invalidating all users cache on insert/update/delete,
// instead performing updates to the hash set provided by redis
class RedisUsersCache2 implements IUsersCache
{
    private $key = "user_key_";
    private $hashKey = "user_hash_key_";


    public function __construct(private PredisClient $redisClient,private int $ttl)
    {
    }

    public function storeUser(User $user): void
    {
        if($user->getId() != null) {
            $key = $this->key . $user->getId();
            // $this->redisClient->set($key, serialize($user), 'EX', $this->ttl);

            $this->redisClient->hset($this->hashKey . "all", $user->getId(), serialize($user));
        }
    }

    public function storeUsers(array $users, string $key = "all"): void
    {
        $hashKey = $this->hashKey . $key;
       
        if (empty($users)) {
            return;
        }

        $fields = []; // [1,serializeUser1, 2,serializeUser2...]
        // hset accepts fieldName, value, fieldName2, value2...   
        foreach ($users as $user) {
            $fields[] = $user->getId();  
            $fields[] = serialize($user);
        }
          
        $this->redisClient->hset($hashKey,...$fields);
    }

    public function getUser(string $key): ?User
    {
        
        // $loadedFromCache = $this->redisClient->get($this->key . $key);
        $loadedFromCache = $this->redisClient->hget($this->hashKey . "all", $key);
        if ($loadedFromCache == null)
        {
            return null;
        }
  
        return unserialize($loadedFromCache);
    }

    public function getUsers(string $key): array | null
    {
        $hashKey = $this->hashKey . $key;
        $loadedFromCache = $this->redisClient->hvals($hashKey);
        
        if ($loadedFromCache == null)
        {
            return null;
        }

        $users = array_map('unserialize', $loadedFromCache);
        usort($users, fn($a, $b) => $b->getCreatedAt() <=> $a->getCreatedAt());
        return $users;
    }

    public function deleteUser(string $key): void
    {
        $this->redisClient->del([$this->key . $key]);
        $this->deleteUserFromArray((int)$key);
    }

    public function deleteUserFromArray(int $userId, string $key = "all"):void
    {
        $this->redisClient->hdel($this->hashKey . $key, [$userId]);
    }

}