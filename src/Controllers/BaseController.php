<?php

namespace App\Controllers;
require_once __DIR__ . "/../../consts.php";

abstract class BaseController
{

    // view routes must be defined [folder].[filename] -> users.addUser or users.editUser
    public function view(string $folder,string $file,array $vars = [])
    {
        extract($vars);
        require \BASE_PATH . "/src/views/{$folder}/{$file}.php";
    }
}