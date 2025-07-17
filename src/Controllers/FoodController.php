<?php
namespace App\Controllers;

require_once "/var/www/config/EntityManagerConfig.php";

use Doctrine\ORM\EntityManager;
use App\Entity\Food;
use App\EntityManagerFactory;

class FoodController
{
    private EntityManager $entityManager;

    public function __construct()
    {
        $this->entityManager = EntityManagerFactory::getEntityManager();
    }

    public function index()
    {
        $foods = $this->entityManager->getRepository(Food::class)->findAll();
        require_once __DIR__ . "/../../public/food_assets/food.php";
    }

    public function create(){
        require_once __DIR__ . "/../../public/food_assets/addFood.php";
    }

    public function addFood(string $name)
    {
        $food = new Food();
        $food->setName($name);
        $food->setCreatedAt(new \DateTime());
        $this->entityManager->persist($food);
        $this->entityManager->flush();

        header("Location: /food");
        die();
    }

    public function delete(int $id){
        $food = $this->entityManager->getRepository(Food::class)->find($id);
        if (!is_null($food)) {
            $this->entityManager->remove($food);
            $this->entityManager->flush();
        }
        
        header("Location: /food");
        die();
    }
}