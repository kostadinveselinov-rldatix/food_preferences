<?php
namespace App\Controllers;

use Doctrine\ORM\EntityManager;
use App\Entity\Food;
use App\config\EntityManagerFactory;

class FoodController extends BaseController
{
    private EntityManager $entityManager;

    public function __construct()
    {
        $this->entityManager = EntityManagerFactory::getEntityManager();
    }

    public function index()
    {
        $foods = $this->entityManager->getRepository(Food::class)->findAll();
        $this->view("foods","index",["foods" => $foods]);
    }

    public function create(){
       $this->view("foods","create");
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