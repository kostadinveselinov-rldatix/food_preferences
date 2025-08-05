<?php

namespace App\Hydrators;
use App\Entity\User;
use App\Entity\Food;

abstract class UserHydrator
{
    public static function toArray(User $user):array
    {
        return [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'lastname' => $user->getLastname(),
            'email' => $user->getEmail(),
            'created_at' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
            'foods' => array_map(fn(Food $food) => $food->toArray(), $user->getFoods()->toArray())
        ];
    }
}