<?php
namespace App;
require_once __DIR__ . "/../bootstrap.php";

require_once __DIR__ . "/../src/Controllers/FoodController.php";
require_once __DIR__ . "/../src/Controllers/UserController.php";

use \App\Controllers\FoodController;
use \App\Controllers\UserController;
use App\Entity\User;

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = trim($uri);
$uri = rtrim($uri, '/');
$uri = str_replace(".php","",$uri);

switch($uri) {
    case '/':
        echo "Welcome to the Food Preferences App!";
        break;
    case '/food':
        $controller = new FoodController();
        echo $controller->index();
        break;
    case '/food/create':
        $controller = new FoodController();
        if($_SERVER["REQUEST_METHOD"] === 'POST') {
            if(isset($_POST['name']) && !empty($_POST['name'])) {
                $name = trim($_POST['name']);
                $controller->addFood($name);
            }
        } else {
            echo $controller->create();
        }
        break;
    case '/food/delete':
        $controller = new FoodController();
        if($_SERVER["REQUEST_METHOD"] === 'POST') {
            if(isset($_POST['id']) && !empty($_POST['id'])) {
                $id = $_POST['id'];
                $controller->delete($id);
                break;
            }
        }
        header("Location: /food");
        break;
    case '/users':
        $controller = new UserController();
        echo $controller->index();
        break;
    case '/user/create':
        $controller = new UserController();

        if($_SERVER["REQUEST_METHOD"] === 'POST') {
            if(isset($_POST['name']) && !empty($_POST['name']) &&
               isset($_POST['lastName']) && !empty($_POST['lastName']) &&
               isset($_POST['email']) && !empty($_POST['email'])) {

                $name = trim($_POST['name']);
                $lastName = trim($_POST['lastName']);
                $email = trim($_POST['email']);
                $foods = isset($_POST['foods']) ? $_POST['foods'] : [];

                $controller->addUser(
                    $name = $name,
                    $lastName = $lastName,
                    $email = $email,
                    $foodIds = $foods
                );
            }
            break;
        }

        echo $controller->create();
        break;

    case "/user/update":
        $controller = new UserController();

        if($_SERVER["REQUEST_METHOD"] === 'POST') {
            if(isset($_POST['id']) && !empty($_POST['id']) &&
               isset($_POST['name']) && !empty($_POST['name']) &&
               isset($_POST['lastName']) && !empty($_POST['lastName']) &&
               isset($_POST['email']) && !empty($_POST['email'])) {

                $id = $_POST['id'];
                $name = trim($_POST['name']);
                $lastName = trim($_POST['lastName']);
                $email = trim($_POST['email']);
                $foods = isset($_POST['foods']) ? $_POST['foods'] : [];

                $controller->update(
                    $id = $id,
                    $name = $name,
                    $lastName = $lastName,
                    $email = $email,
                    $foodIds = $foods
                );
            }
            break;
        }
        
        if(!isset($_GET['id']) || empty($_GET['id'])) {
            header("Location: /users");
            die();
        }

        $controller->edit($_GET['id']);
        break;

    case "/user/delete":
        $controller = new UserController();
        if($_SERVER["REQUEST_METHOD"] === 'POST') {
            if(isset($_POST['id']) && !empty($_POST['id'])) {
                $id = $_POST['id'];
                $controller->delete($id);
            }
            break;
        }
        header("Location: /users");
        break;
    default:
        echo "404 Not Found";
}

die();