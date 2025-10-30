<?php
require_once \BASE_PATH . "/bootstrap.php";

use \App\ApiControllers\ApiUserController;
use \App\Middleware\ParametersMiddleware;
use \App\Middlewares\RateLimiter;
use App\Session\Session;

$rateLimiter = $container->get(RateLimiter::class);

if($rateLimiter->isRateLimited(Session::getCurrentSessionId())) {
    http_response_code(429);
    header('Content-Type: application/json');
    echo json_encode(['status' => 429, 'message' => 'Too Many Requests. Please try again later.']);
    die();
}

switch($uri){
    case "/api/users":
        $controller = $container->get(ApiUserController::class);
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if(isset($_GET["offset"]) && isset($_GET["limit"])){
                $offset = (int)$_GET["offset"];
                $limit = (int)$_GET["limit"];

                if($offset < 0) { $offset = 0; }
                if($limit <= 0) { $limit = 10; }

                echo $controller->getUsersInBatches($offset, $limit);
                // die();
                break;
            }

            echo $controller->index();
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $lastName = isset($_POST['lastName']) ? trim($_POST['lastName']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $foods = isset($_POST['foods']) ? $_POST['foods'] : [];

            $data = ["name" => $name,"lastName" => $lastName,"email"=> $email,"foodIds"=> $foods];

            echo $controller->create($data);
        }
        break;
    case "/api/user":
        $controller = $container->get(ApiUserController::class);
    
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
            if(!isset($_GET["id"])){
                $rawInput = file_get_contents('php://input');
                $id = json_decode($rawInput, true)['id'] ?? null;
            }else{
                $id = $_GET["id"];
            }

            if(is_numeric($id)){
                echo $controller->show((int)$id);
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid user ID']);
            }
        }else if($_SERVER['REQUEST_METHOD'] === 'POST'){
            if(!isset($_POST['id'])){
                echo $controller->errorResponse('ID in body is required', 400);
                break;
            }
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $lastName = isset($_POST['lastName']) ? trim($_POST['lastName']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $foods = isset($_POST['foods']) ? $_POST['foods'] : [];

            $data = ["name" => $name,"lastName" => $lastName,"email"=> $email,"foodIds"=> $foods];

            if(isset($_GET["id"])){
                $id = $_GET["id"];

                if($id != $_POST["id"]){
                    http_response_code(400);
                    echo $controller->errorResponse('ID mismatch!', 400);
                    // die();
                    break;
                }
            }

            echo $controller->edit($_POST["id"],$data);
        }
        
        break;
    case "/api/user/delete":
        $controller = $container->get(ApiUserController::class);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rawInput = file_get_contents('php://input');
            $data = json_decode($rawInput, true);
            $id = $data['id'] ?? $_GET["id"];
          
            if (is_numeric($id)) {
                echo $controller->delete((int)$id);
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid user ID']);
            }
        }
        break;
    case "/api/user/search":
        $controller = $container->get(ApiUserController::class);
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $search = $_GET['searchTerm'] ?? null;
            if (!is_null($search)) {
                echo $controller->searchByNameOrLastName($search);
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'SearchTerm parameter is required']);
            }
        }
        break;
    default:
        http_response_code(404);
        echo json_encode(['message' => 'Route not Found']);
        break;
}