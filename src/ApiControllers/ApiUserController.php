<?php
namespace App\ApiControllers;
require_once \BASE_PATH . "/bootstrap.php";
require_once \BASE_PATH ."/config/EntityManagerConfig.php";

use App\Repositories\UserRepository;
use App\Entity\User;
use App\EntityManagerFactory;
class ApiUserController
{
    use \App\Traits\HttpResponses;

    private UserRepository $userRepository;
    private  $entityManager;
    public function __construct()
    {
       $this->entityManager = EntityManagerFactory::getEntityManager();
       $this->userRepository = $this->entityManager->getRepository(User::class);
       $this->userRepository->setRedis(new \App\cache\redis\RedisUsersCache(\getRedisConfig()));
    }

    public function index()
    {
        $users = $this->userRepository->findAllUsersReturnsArray();

        return $this->jsonResponse($users, 'Users retrieved successfully');
    }

    public function create(array $data)
    {
        try{
            $user = $this->userRepository->storeUser($data);
            return $this->jsonResponse(data: $user->toArray(),message: 'User created successfully', statusCode: 201);
        }catch(\Exception $e){
            return $this->errorResponse('Invalid input data', 400);
        }
    }

    public function delete(int $id)
    {
        if($this->userRepository->deleteUser($id)){
            return $this->jsonResponse(message: 'User deleted successfully');
        }
        return $this->errorResponse('User not found', 404);
    }

    public function edit(int $id, array $data)
    {
        $updatedUser = $this->userRepository->updateUser($id, $data);
        if(!is_null($updatedUser)){
            return $this->jsonResponse(data:$updatedUser->toArray(),message: 'User updated successfully');
        }

       return $this->errorResponse("Error updating user with id: {$id}");
    }

    public function show(int $id)
    {
       $user = $this->userRepository->findUserById($id);

        if (is_null($user)) {
            return $this->errorResponse('User not found', 404);
        }

        return $this->jsonResponse(data: $user->toArray(), message: 'User retrieved successfully');
    }

    public function searchByNameOrLastName(string $searchTerm){
        if (empty($searchTerm)) {
            return $this->errorResponse('Search term cannot be empty', 400);
        }

        $users = $this->userRepository->searchByNameOrLastName($searchTerm);

        if (empty($users)) {
            return $this->jsonResponse([], 'No users found', 404);
        }

        return $this->jsonResponse(data: $users, message: 'Users retrieved successfully');
    }
}