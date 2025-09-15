<?php
namespace App\cache\redis;

use \App\Entity\User;

interface IUsersCache
{
    public function storeUser(User $user): void;

    public function getUser(string $key): ?User;

    public function storeUsers(array $users, string $key): void;

    public function getUsers(string $key): array | null;

    public function deleteUser(string $key): void;
}