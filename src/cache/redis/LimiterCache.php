<?php

namespace App\cache\redis;

use \Predis\Client as PredisClient;

class LimiterCache
{
    private  PredisClient $redis;
    private $expireTime;
    private $key = "rate_limiter_user-";

    public function __construct(PredisClient $client, int $expireTime = 60)
    {
         $this->redis = $client;
         $this->expireTime = $expireTime;
    }

    public function setExpireTime(int $seconds): void
    {
        $this->expireTime = $seconds;
    }

    public function getExpireTime(): int
    {
        return $this->expireTime;
    }

    private function setupUserCache(string $userId): void
    {
        $key = $this->key . $userId;
        if (!$this->redis->exists($key)) {
            $this->redis->set($key, 0);
            $this->redis->expire($key, seconds: $this->expireTime);
        }
    }

    public function incrementRequestsForUser(string $userId): int
    {
        $this->setupUserCache($userId);
        $key = $this->key . $userId;
        $this->redis->incr($key);
        return $this->getRequestsForUser($userId);
    }

    public function getRequestsForUser(string $userId): int
    {
        $key = $this->key . $userId;
        return $this->redis->get($key) ?? 0;
    }

    public function clearRequestsForUser(string $userId): void
    {
        $key = $this->key . $userId;
        $this->redis->del([$key]);
    }
}
