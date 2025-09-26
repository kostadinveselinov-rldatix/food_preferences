<?php

namespace App\cache\redis\FoodCache;

use \App\cache\redis\FoodCache\IFoodCache;
use \App\Entity\Food;
use \Predis\Client as PredisClient;

use function PHPSTORM_META\type;

class RedisFoodCache implements IFoodCache
{
    private string $keyPrefix = "food_key_all";
    public function __construct(private PredisClient $redisClient, private int $ttl)
    {
    }

    public function storeFoods(array $foods): void
    {
        if(empty($foods)){ return; }
        $oldFoods = $this->getFoods();
        $foods = array_merge($oldFoods,$foods);
        $this->redisClient->set($this->keyPrefix, serialize($foods), 'EX', $this->ttl);
    }

    public function getFoods(): array
    {
        $loadedFromCache = $this->redisClient->get($this->keyPrefix);
        if ($loadedFromCache == null)
        {
            return [];
        }

        return unserialize($loadedFromCache);
    }

    public function deleteFood(Food $food): void
    {
        $foodArray = $this->getFoods();
        if(empty($foodArray)){ return; }

        $filteredFoods = array_filter($foodArray, fn($f) => (int)$f->getId() != (int)$food->getId());

        $this->redisClient->set($this->keyPrefix, serialize($filteredFoods), 'EX', $this->ttl);
    }

    public function invalidateAllFoodsCache(): void
    {
        if($this->redisClient->exists($this->keyPrefix)) {
            $this->redisClient->del([$this->keyPrefix]);
        }
    }
}