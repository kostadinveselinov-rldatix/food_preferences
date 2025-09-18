<?php
require_once \BASE_PATH . "/bootstrap.php";
require_once \BASE_PATH . "/src/Controllers/FoodController.php";
require_once \BASE_PATH . "/src/Controllers/UserController.php";

use \App\Controllers\FoodController;
use \App\Controllers\UserController;
use App\Entity\User;
use App\Middlewares\RateLimiter;
use App\Session\Session;

$rateLimiter = $container->get(RateLimiter::class);

if($rateLimiter->isRateLimited(Session::getCurrentSessionId())) {
    echo "Too Many Requests. Please try again later.";
    die();
}

switch($uri) {
    case '':
        echo "Request number: " . $rateLimiter->getCurrentRequests(Session::getCurrentSessionId());
        echo "Session ID: " . Session::getCurrentSessionId();
        require_once \BASE_PATH . "/src/views/mainPage.php";
        break;
    case '/food':
        $controller = $container->get(FoodController::class);
        echo $controller->index();
        break;
    case '/food/create':
        $controller = $container->get(FoodController::class);
        if($_SERVER["REQUEST_METHOD"] === 'POST') {
            $name = isset($_POST["name"]) ? trim($_POST['name']) : "";
            $controller->addFood($name);
    
        } else {
            echo $controller->create();
        }
        break;
    case '/food/delete':
        $controller = $container->get(FoodController::class);
        if($_SERVER["REQUEST_METHOD"] === 'POST') {
            if(isset($_POST['id']) && !empty($_POST['id'])) {
                if(!is_numeric($_POST['id']) || $_POST['id'] <= 0) {
                    header("Location: /food");
                    die();
                }
                $id = $_POST['id'];
                $controller->delete($id);
                break;
            }
        }
        header("Location: /food");
        break;
    case '/users':
        $controller = $container->get(UserController::class);
        echo $controller->index();
        break;
    case '/user/create':
        $controller = $container->get(UserController::class);

        if($_SERVER["REQUEST_METHOD"] === 'POST') {
                $name = trim($_POST['name']);
                $lastName = trim($_POST['lastName']);
                $email = trim($_POST['email']);
                $foods = isset($_POST['foods']) ? $_POST['foods'] : [];

                $data = ["name" => $name,"lastName" => $lastName,"email"=> $email,"foodIds"=> $foods];

                $controller->addUser(
                    $data
                );
            break;
        }

        echo $controller->create();
        break;

    case "/user/update":
        $controller = $container->get(UserController::class);

        if($_SERVER["REQUEST_METHOD"] === 'POST') {

                $id = $_POST['id'];
                $name = trim($_POST['name']);
                $lastName = trim($_POST['lastName']);
                $email = trim($_POST['email']);
                $foods = isset($_POST['foods']) ? $_POST['foods'] : [];

                $data = ["name" => $name,"lastName" => $lastName,"email"=> $email,"foodIds"=> $foods];

                $controller->update(
                    id: $id,
                    data: $data
                );
            break;
        }
        
        if(!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id']) || $_GET['id'] <= 0) {
            header("Location: /users");
            die();
        }

        $controller->edit($_GET['id']);
        break;

    case "/user/delete":
        $controller = $container->get(UserController::class);
        if($_SERVER["REQUEST_METHOD"] === 'POST') {
            if(isset($_POST['id']) && !empty($_POST['id'])) {
                $id = $_POST['id'];
                $controller->delete($id);
            }
            break;
        }
        header("Location: /users");
        break;
    case "/report/create":
        require_once \BASE_PATH . "/src/views/reports/reportRequest.php";
        break;
    case "/report/download":
        require \BASE_PATH . "/src/views/reports/downloadReport.php";
        break;
    
    case "/report/delete":
        require_once \BASE_PATH . "/src/views/reports/deleteReport.php";
        break;
    case "/seeders":
        require_once \BASE_PATH . "/src/seeders/seederProducer.php";
        break;
    default:
        echo "404 Not Found";
}

die();