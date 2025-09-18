<?php

namespace App\Repositories;

use App\Repositories\Interfaces\FoodRepositoryInterface;
use Doctrine\ORM\EntityRepository;
use App\cache\redis\FoodCache\IFoodCache;
use App\Entity\Food;

class FoodRepository extends EntityRepository
{

    private $redis;
    public function setRedis(IFoodCache $redis)
    {
        $this->redis = $redis;
    }

    public function findFoodById(int $id,bool $fetchFromCache): ?Food
    {
        if($fetchFromCache)
        {
            $foods = $this->redis->getFoods();
            foreach($foods as $food)
            {
                if($food->getId() === $id)
                {
                    return $food;
                }
            }
        }

        return $this->find($id);
    }
    public function findAllFoods(bool $fetchFromCache = true): array
    {
        $foods = null;
        if($fetchFromCache)
        {
            $foods = $this->redis->getFoods();
        }

        if(empty($foods) || is_null($foods))
        {
            $foods = $this->findAll();
            if(!empty($foods)){
                $this->redis->storeFoods($foods);
            }
        }

        return $foods;
    }
    public function storeFood(string $name): Food
    {
        $food = new Food();
        $food->setName($name);
        $food->setCreatedAt(new \DateTime());

        $this->getEntityManager()->persist($food);
        $this->getEntityManager()->flush();

        $foods = $this->redis->getFoods();
        $foods[] = $food;
        $this->redis->storeFoods($foods);

        return $food;
    }
    public function updateFood(int $id, array $data): ?Food
    {
        $food = $this->find($id);
        if ($food === null) {
            return null;
        }

        $this->redis->deleteFood($food);

        if (isset($data['name'])) {
            $food->setName($data['name']);
        }

        $this->getEntityManager()->persist($food);
        $this->getEntityManager()->flush();

        $foods = $this->redis->getFoods();
        $foods[] = $food;
        $this->redis->storeFoods($foods);

        return $food;
    }
    public function deleteFood(int $id): bool
    {
        $food = $this->find($id);
        if ($food === null) {
            return false;
        }
        
        $this->redis->deleteFood($food);
        $this->getEntityManager()->remove($food);
        $this->getEntityManager()->flush();

        return true;
    }

    public function getAllFoodArray():array
    {
        return $this->createQueryBuilder("f")
            ->select("f")
            ->getQuery()
            ->getArrayResult();
    }

}