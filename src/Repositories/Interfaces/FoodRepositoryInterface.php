<?php

namespace App\Repositories\Interfaces;

use App\Entity\Food;

interface FoodRepositoryInterface
{
    public function findFoodById(int $id,bool $fetchFromCache): ?Food;
    public function findAllFoods(bool $fetchFromCache = true): array;
    public function storeFood(string $name): Food;
    public function updateFood(int $id, array $data): ?Food;
    public function deleteFood(int $id): bool;
}