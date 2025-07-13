<?php
namespace App\Controllers;

use Doctrine\ORM\EntityManager;
use App\Entity\User;

class UserController
{
    private EntityManager $entityManager;

    public function __construct()
    {
        $this->entityManager = require \BASE_PATH . '/config/EntityManagerConfig.php';
    }

    public function index()
    {
        $users = $this->entityManager->createQueryBuilder()
            ->select('u',"f")
            ->from(User::class, 'u')
            ->leftJoin("u.foods", 'f')
            ->getQuery()
            ->getResult();

        require_once \BASE_PATH . '/public/user_assets/user.php';
    }

    public function create()
    {
        $foods = $this->entityManager->getRepository(\App\Entity\Food::class)->findAll();
        require_once \BASE_PATH . '/public/user_assets/addUser.php';
    }

    public function addUser(string $name, string $lastName, string $email, array $foodIds)
    {
        $user = new User();
        $user->setName($name);
        $user->setLastname($lastName);
        $user->setEmail($email);
        $user->setCreatedAt(new \DateTime());
        $foods = $this->entityManager->getRepository(\App\Entity\Food::class)->findBy(['id' => $foodIds]);

        foreach ($foods as $food) {
            $user->getFoods()->add($food);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        header("Location: /users");
        die();
    }

    public function delete(int $id)
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!is_null($user)) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
        }

        header("Location: /users");
        die();
    }

    public function edit(int $id)
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            header("Location: /users");
            die();
        }

        $userFoods = $user->getFoods()->toArray();
        $userFoodIds = array_map(fn($food) => $food->getId(), $userFoods);

        $foods = $this->entityManager->getRepository(\App\Entity\Food::class)->findAll();
        require_once \BASE_PATH . '/public/user_assets/editUser.php';
    }

    public function update(int $id, string $name, string $lastName, string $email, array $foodIds)
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (is_null($user)) {
            header("Location: /users");
            die();
        }

        $user->setName($name);
        $user->setLastname($lastName);
        $user->setEmail($email);
        $user->getFoods()->clear();

        $foods = $this->entityManager->getRepository(\App\Entity\Food::class)->findBy(['id' => $foodIds]);
        foreach ($foods as $food) {
            $user->getFoods()->add($food);
        }

        $this->entityManager->flush();

        header("Location: /users");
        die();
    }
}