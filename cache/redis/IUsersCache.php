<?php
namespace App\cache\redis;
require_once \BASE_PATH . "/src/Entity/User.php";

use \App\Entity\User;

interface IUsersCache
{
    public function storeUser(User $user): void;

    public function getUser(string $key): ?User;

    public function storeUsers(string $key, array $users): void;

    public function getUsers(string $key): array | null;
}