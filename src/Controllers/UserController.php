<?php
namespace App\Controllers;

use Doctrine\ORM\EntityManager;
use App\Entity\User;
use App\config\EntityManagerFactory;
use App\Repositories\UserRepository;

class UserController
{
    public function __construct(private UserRepository $userRepository,private EntityManager $entityManager)
    {
    }

    public function index()
    {
        $users = $this->userRepository->findAll();

        // var_dump($users);

        require_once \BASE_PATH . '/public/user_views/user.php';
    }

    public function create()
    {
        $foods = $this->entityManager->getRepository(\App\Entity\Food::class)->findAll();
        require_once \BASE_PATH . '/public/user_views/addUser.php';
    }

    public function addUser(array $data)
    {
        $user = $this->userRepository->storeUser($data);

        header("Location: /users");
        die();
    }

    public function delete(int $id)
    {
        $this->userRepository->deleteUser($id);

        header("Location: /users");
        die();
    }

    public function edit(int $id)
    {
        $user = $this->userRepository->findUserById($id);

        if (is_null($user)) {
            header("Location: /users");
            die();
        }

        $userFoods = $user->getFoods()->toArray();
        $userFoodIds = array_map(fn($food) => $food->getId(), $userFoods);

        $foods = $this->entityManager->getRepository(\App\Entity\Food::class)->findAll();
        require_once \BASE_PATH . '/public/user_views/editUser.php';
    }

    public function update(int $id,array $data):void
    {
        $this->userRepository->updateUser($id, $data);

        header("Location: /users");
        die();
    }
}