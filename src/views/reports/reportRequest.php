<?php
error_reporting(E_ALL & ~E_DEPRECATED);
require_once \BASE_PATH . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use App\Session\Session;

if($_SERVER['REQUEST_METHOD']==='POST'){
    $conn=new AMQPStreamConnection("rabbitmq",5672,"guest","guest");
    $ch=$conn->channel();
    $ch->queue_declare('report_queue',false,true,false,false);

    $timeMessage = time();
    $ch->basic_publish(new AMQPMessage(json_encode(['request_time'=>$timeMessage])), '', 'report_queue');
    Session::addToArray('reports', $timeMessage);
    
    header("Location: /report/download");
    exit();
}

require_once BASE_PATH . "/src/parts/header.php";
echo '<form method="post"><button type="submit">Generate Report</button></form>';

