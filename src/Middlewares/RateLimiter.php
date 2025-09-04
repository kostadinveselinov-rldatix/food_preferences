<?php

namespace App\Middlewares;
use App\cache\redis\LimiterCache;

class RateLimiter
{ // Fixed Window RateLimiter for every route request, expiration time is handled in LimiterCache and setup with dependency injection
    private $maxRequests;
    private $limiterCache;

    public function __construct(LimiterCache $limiterCache, int $maxRequests)
    {
        $this->limiterCache = $limiterCache;
        $this->maxRequests = $maxRequests;
    }

    public function isRateLimited(string $userId): bool
    {
        $currentRequests = $this->limiterCache->incrementRequestsForUser($userId);
        return $currentRequests > $this->maxRequests;
    }

    public function resetRateLimit(string $userId): void
    {
        $this->limiterCache->clearRequestsForUser($userId);
    }

    public function getCurrentRequests(string $userId): int
    {
        return $this->limiterCache->getRequestsForUser($userId);
    }
}