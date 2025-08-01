<?php
namespace App\ApiControllers;
require_once \BASE_PATH . "/bootstrap.php";

use App\Repositories\UserRepository;
use App\Entity\User;
use App\config\EntityManagerFactory;
use App\Validator\Validator;
class ApiUserController
{
    use \App\Traits\HttpResponses;

    public function __construct(private UserRepository $userRepository)
    {
    }

    public function index()
    {
        $users = $this->userRepository->findAllUsersReturnsArray();

        return $this->jsonResponse($users, 'Users retrieved successfully');
    }

    public function create(array $data)
    {
        $validator = new Validator();

        $rules = [
            'name' => 'required|string|min:2|max:50',
            'lastName' => 'required|string|min:2|max:50',
            'email' => 'required|email',
            'foodIds' => 'empty|numeric_array'
        ];

        if($validator->validate($data, $rules)){
            return $this->jsonResponse($validator->getOnlyErrorMessages(),"FIeld validation fails",400);
        }

        $name = trim($data['name']);
        $lastName = trim($data['lastName']);
        $email = trim($data['email']);
        $foodIds = $data['foodIds'] ?? [];

        try{
            $user = $this->userRepository->storeUser($name, $lastName, $email, $foodIds);
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
        $validator = new Validator();

        $rules = [
            'name' => 'required|string|min:2|max:50',
            'lastName' => 'required|string|min:2|max:50',
            'email' => 'required|email',
            "foodIds" => "empty|numeric_array"
        ];

        if($validator->validate($data, $rules)){
            return $this->jsonResponse($validator->getOnlyErrorMessages(),"FIeld validation fails",400);
        }

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