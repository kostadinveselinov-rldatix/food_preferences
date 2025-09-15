<?php

namespace App\Repositories\Interfaces;

use App\Entity\User;

interface UserRepositoryInterface
{
    public function findUserById(int $id,bool $fetchFromCache): ?User;
    public function findAllUsers(bool $fetchFromCache = true,bool $fetchFood = true,string $key = "all"): array;
    public function storeUser(string $name, string $lastName, string $email, array $foodIds = []): User;
    public function updateUser(int $id, array $data): ?User;
    public function deleteUser(int $id): bool;
    public function searchByNameOrLastName(string $searchTerm): array;
}