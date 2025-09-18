<?php
namespace App\Controllers;

use Doctrine\ORM\EntityManager;
use App\Entity\Food;
use App\config\EntityManagerFactory;
use App\Repositories\FoodRepository;
use App\Validator\Validator;

class FoodController extends BaseController
{
    public function __construct(private FoodRepository $foodRepository)
    {
    }

    public function index()
    {
        $foods = $this->foodRepository->findAllFoods();
        $totalFood = $this->foodRepository->createQueryBuilder("f")
            ->select("COUNT(f.id)")
            ->getQuery()
            ->getSingleScalarResult();

        $this->view("foods","index",["foods" => $foods, "totalItems" => $totalFood]);
    }

    public function create(){
       $this->view("foods","create");
    }

    public function addFood(string $name)
    {
        $validator = new Validator();
        $rules = [
            'name' => 'required|string|min:2|max:20'
        ];

        if($validator->validate(['name' => $name], $rules)){
            $_SESSION['errors'] = $validator->getErrors();
            header("Location: /food/create");
            die();
        }

        $this->foodRepository->storeFood($name);

        header("Location: /food");
        die();
    }

    public function delete(int $id){
        $validator = new Validator();
        $rules = [
            'id' => 'required|numeric'
        ];

        if($validator->validate(['id' => $id], $rules)){
            $_SESSION['errors'] = $validator->getErrors();
            header("Location: /food");
            die();
        }

        $this->foodRepository->deleteFood($id);

        
        header("Location: /food");
        die();
    }
}