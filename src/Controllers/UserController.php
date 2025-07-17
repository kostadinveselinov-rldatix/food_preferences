<?php
namespace App\Controllers;

require_once "/var/www/config/EntityManagerConfig.php";

use Doctrine\ORM\EntityManager;
use App\Entity\User;
use App\EntityManagerFactory;
use App\Repositories\UserRepository;

class UserController
{
    private UserRepository $userRepository;
    private EntityManager $entityManager;

    public function __construct()
    {
        $this->entityManager = EntityManagerFactory::getEntityManager();
        $this->userRepository = $this->entityManager->getRepository(User::class);
        $this->userRepository->setRedis(new \App\cache\redis\RedisUsersCache(\getRedisConfig()));
    }

    public function index()
    {
        $users = $this->userRepository->findAll();

        require_once \BASE_PATH . '/public/user_assets/user.php';
    }

    public function create()
    {
        $foods = $this->entityManager->getRepository(\App\Entity\Food::class)->findAll();
        require_once \BASE_PATH . '/public/user_assets/addUser.php';
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
        require_once \BASE_PATH . '/public/user_assets/editUser.php';
    }

    public function update(int $id,array $data):void
    {
        $this->userRepository->updateUser($id, $data);

        header("Location: /users");
        die();
    }
}