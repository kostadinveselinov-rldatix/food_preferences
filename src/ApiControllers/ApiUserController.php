<?php
namespace App\ApiControllers;
require_once \BASE_PATH . "/bootstrap.php";

use Doctrine\ORM\EntityManager;
use App\Entity\User;
use App\cache\redis\IUsersCache;

class ApiUserController{
    use \App\Traits\HttpResponses;

    private EntityManager $entityManager;
    private IUsersCache $usersCache;

    public function __construct()
    {
        $this->entityManager = require \BASE_PATH . '/config/EntityManagerConfig.php';
        $this->usersCache = new \App\cache\redis\RedisUsersCache(\getRedisConfig());
    }

    public function index()
    {
        $usersFromCache = $this->usersCache->getUsers('all');

        if(is_null($usersFromCache)){
            $users = $this->entityManager->createQueryBuilder()
                ->select('u', 'f')
                ->from(User::class, 'u')
                ->leftJoin("u.foods", 'f')
                ->getQuery()
                ->getArrayResult();

            if (!empty($users)) {
                $this->usersCache->storeUsers("all",$users);
            }
        }else{
            $users = $usersFromCache;
        }


        return $this->jsonResponse($users, 'Users retrieved successfully');
    }

    public function create(array $data)
    {
        if (isset($data['name'], $data['lastName'], $data['email'])) {
            $name = trim($data['name']);
            $lastName = trim($data['lastName']);
            $email = trim($data['email']);

            $user = new User();
            $user->setName($name);
            $user->setLastname($lastName);
            $user->setEmail($email);
            $user->setCreatedAt(new \DateTime());
           
            $foodIds = $data['foodIds'] ?? [];
            // add preferenced foods to user
            if(is_array($foodIds) && !empty($foodIds)) {
                $foods = $this->entityManager->getRepository(\App\Entity\Food::class)->findBy(['id' => $foodIds]);
                foreach ($foods as $food) {
                    if(!is_null($food)){
                        $user->getFoods()->add($food);
                    }
                }
            }

            $this->entityManager->persist($user);
            $this->entityManager->flush();
            
            return $this->jsonResponse(data: $user->toArray(),message: 'User created successfully', statusCode: 201);
        }
        return $this->errorResponse('Invalid input data', 400);
    }

    public function delete(int $id)
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!is_null($user)) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
            return $this->jsonResponse(message: 'User deleted successfully');
        }
        return $this->errorResponse('User not found', 404);
    }

    public function edit(int $id, array $data)
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }

        // check which fields are changed and update them
        if (isset($data['name'])) {
            $user->setName(trim($data['name']));
        }
        if (isset($data['lastName'])) {
            $user->setLastname(trim($data['lastName']));
        }
        if (isset($data['email'])) {
            $user->setEmail(trim($data['email']));
        }

        // Update foods
        if (isset($data['foodIds']) && is_array($data['foodIds'])) {
            $foods = $this->entityManager->getRepository(\App\Entity\Food::class)->findBy(['id' => $data['foodIds']]);
            foreach ($foods as $food) {
                if (!is_null($food)) {
                    $user->getFoods()->add($food);
                }
            }
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->jsonResponse(data:$user->toArray(),message: 'User updated successfully');
    }

    public function show(int $id)
    {
        $loadedFromCache = $this->usersCache->getUser((string)$id);
        if(is_null($loadedFromCache)){
            $user = $this->entityManager->getRepository(User::class)->find($id);

            if(!is_null($user)){
                $user->getFoods()->initialize();
                $this->usersCache->storeUser($user);
            }
        }else{
            $user = $loadedFromCache;
        }

        if (is_null($user)) {
            return $this->errorResponse('User not found', 404);
        }

        return $this->jsonResponse(data: $user->toArray(), message: 'User retrieved successfully');
    }

    public function searchByNameOrLastName(string $searchTerm){
        if (empty($searchTerm)) {
            return $this->errorResponse('Search term cannot be empty', 400);
        }

        $loadedFromCache = $this->usersCache->getUsers($searchTerm);

        if(is_null($loadedFromCache)){
            $users = $this->entityManager->createQueryBuilder()
            ->select('u', 'f')
            ->from(User::class, 'u')
            ->leftJoin("u.foods", 'f')
            ->where('u.name LIKE :searchTerm')
            ->orWhere('u.lastname LIKE :searchTerm')
            ->setParameter('searchTerm', '%' . $searchTerm . '%')
            ->getQuery()
            ->getArrayResult();

            if(!empty($users)){
                $this->usersCache->storeUsers($searchTerm,$users);
            }
        }else{
            $users = $loadedFromCache;
        }

        if (empty($users)) {
            return $this->jsonResponse([], 'No users found', 404);
        }

        return $this->jsonResponse(data: $users, message: 'Users retrieved successfully');
    }
}