<?php
namespace App\Controllers;

use Doctrine\ORM\EntityManager;
use App\Entity\User;
use App\config\EntityManagerFactory;
use App\Repositories\UserRepository;
use App\Validator\Validator;

class UserController extends BaseController
{
    public function __construct(private UserRepository $userRepository,private EntityManager $entityManager)
    {
    }

    public function index()
    {
        $page = $_GET["page"] ?? 0;
        $pageSize = $_GET["size"] ?? 10;

        $users = $this->userRepository->fetchUsersInBatches($page, $pageSize, true);
        // $users = $this->userRepository->findAllUsers();
        $totalUsers = $this->userRepository->countAllUsers();

        $this->view("users","index",["users" => $users, "totalItems" => $totalUsers,"currentPage" => $page, "pageSize" => $pageSize]);
    }

    public function create()
    {
        $foodRepo = $this->entityManager->getRepository(\App\Entity\Food::class);
        $foods = $foodRepo->createQueryBuilder("f")
            ->select("f.id","f.name") // don't fetch created_at because It creates object(DateTime) and waists resources and we dont need it here
            ->getQuery()
            ->getArrayResult();
 
        $this->view("users","create",["foods" => $foods]);
    }

    public function addUser(array $data)
    {
        $validator = new Validator();
        $rules = [
            'name' => 'required|string|min:2|max:50',
            'lastName' => 'required|string|min:2|max:50',
            'email' => 'required|email',
            'foodIds' => 'empty|numeric_array'
        ];

        if($validator->validate($data, $rules)){
            $_SESSION['errors'] = $validator->getErrors();
            header("Location: /user/create");
            // die();
            return;
        }

        $user = $this->userRepository->storeUser($data["name"],
            $data["lastName"],
            $data["email"],
            $data["foodIds"] ?? []);

        header("Location: /users");
        // die();
        return;
    }

    public function delete(int $id)
    {
        $validator = new Validator();
        $rules = [
            'id' => 'required|numeric'
        ];

         if($validator->validate(["id" => $id], $rules)){
            $_SESSION['errors'] = $validator->getErrors();
            header("Location: /users");
            // die();
            return;
        }

        $this->userRepository->deleteUser($id);

        header("Location: /users");
        return;
    }

    public function edit(int $id)
    {
        $user = $this->userRepository->findUserById($id);

        if (is_null($user)) {
            header("Location: /users");
            return;
        }

        $userFoods = $user->getFoods()->toArray();
        $userFoodIds = array_map(fn($food) => $food->getId(), $userFoods);

        $foodRepo = $this->entityManager->getRepository(\App\Entity\Food::class);
        $foods = $foodRepo->createQueryBuilder("f")
            ->select("f.id","f.name")
            ->getQuery()
            ->getArrayResult();

        $this->view("users","edit",["user" => $user, "userFoodIds" => $userFoodIds,"foods" => $foods]);
    }

    public function update(int $id,array $data):void
    {
        $validator = new Validator();
        $rules = [
            'name' => 'required|string|min:2|max:50',
            'lastName' => 'required|string|min:2|max:50',
            'email' => 'required|email',
            'foodIds' => 'empty|numeric_array'
        ];

        if($validator->validate($data, $rules)){
            $_SESSION['errors'] = $validator->getErrors();
            header("Location: /user/update?id=$id");
            return;
        }

        $this->userRepository->updateUser($id, $data);

        header("Location: /users");
        return;
    }
}