<?php
require_once \BASE_PATH . "/bootstrap.php";

require_once \BASE_PATH . "/src/ApiControllers/ApiUserController.php";
use \App\ApiControllers\ApiUserController;

switch($uri){
    case "/api/users":
        $controller = new ApiUserController();
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            echo $controller->index();
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rawInput = file_get_contents('php://input');
            $data = json_decode($rawInput, true);
            echo $controller->create($data);
        }
        break;
    case "/api/user":
        $controller = new ApiUserController();
    
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
            $rawInput = file_get_contents('php://input');
            $data = json_decode($rawInput, true);
            if(isset($_GET["id"])){
                $id = $_GET["id"];

                if($id != $data["id"]){
                    http_response_code(400);
                    echo json_encode(['message' => 'ID mismatch']);
                    die();
                }
            }else{
                $id = $data['id'] ?? null;
            }

            if(!is_null($id)){
                echo $controller->edit($id,$data);
            }
        }
        
        break;

    case "/api/user/delete":
        $controller = new ApiUserController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rawInput = file_get_contents('php://input');
            $data = json_decode($rawInput, true);
            $id = $data['id'] ?? null;
          
            if (is_numeric($id)) {
                echo $controller->delete((int)$id);
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid user ID']);
            }
        }
        break;
    case "/api/user/search":
        $controller = new ApiUserController();
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
        echo json_encode(['message' => 'Not Found']);
        break;
}

die();