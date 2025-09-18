<?php
require_once __DIR__ . "/../../bootstrap.php";

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use App\Session\Session;

if($_SERVER['REQUEST_METHOD']==='POST' && (isset($_POST['create_user']) || isset($_POST['create_food']))){
    $conn=new AMQPStreamConnection("rabbitmq",5672,"guest","guest");
    $ch=$conn->channel();
    
    $redirectUrl = "/";

    if(isset($_POST["create_user"])){
        $ch->queue_declare('create_user',false,true,false,false);

        $data = [];
        if(empty($_POST["data"])){ // mock data
            for($i=1;$i<=10;$i++){
                $data[] = [
                    "name" => "User{$i}",
                    "lastname" => "LastName{$i}",
                    "email" => "user{$i}@example.com"
                ];
            }
        }else{
            $data = $_POST["data"];
        }

        $ch->basic_publish(new AMQPMessage(json_encode(['data'=>$data])), '', 'create_user');
        $redirectUrl = "/users";
    }
    
    if(isset($_POST["create_food"])){
        $ch->queue_declare('create_food',false,true,false,false);

        $data = [];
        if(empty($_POST["data"])){
            for($i=1;$i<=10;$i++){
                $data[] = [
                    "name" => "Food{$i}",
                ];
            }
        }else{
            $data = $_POST["data"];
        }
        $ch->basic_publish(new AMQPMessage(json_encode(['data'=>$data])), '', 'create_food');
        $redirectUrl = "/food";
    }

    header("Location: " . $redirectUrl);
    exit();
}