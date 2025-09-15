<?php

namespace App\cache\redis\FoodCache;

use App\Entity\Food;

interface IFoodCache
{
    public function storeFoods(array $foods): void;
    public function getFoods(): array;
    public function deleteFood(Food $food): void;
}