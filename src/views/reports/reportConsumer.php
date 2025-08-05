<?php
error_reporting(E_ALL & ~E_DEPRECATED);
declare(ticks=1);

require_once __DIR__ . "/../../../vendor/autoload.php";
require_once __DIR__ . '/../../worker/ReportGenerator.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use App\worker\ReportGenerator;

$host = getenv('RABBITMQ_HOST') ?: 'rabbitmq';
$port = getenv('RABBITMQ_PORT') ?: 5672;
$user = getenv('RABBITMQ_USER') ?: 'guest';
$password = getenv('RABBITMQ_PASSWORD') ?: 'guest';

sleep(1);

for ($i = 0; $i < 5; $i++) {
    try {
        $conn = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
        break;
    } catch (\Exception $e) {
        echo "Waiting for RabbitMQ...\n";
        sleep(3);
    }
}

$ch = $conn->channel();
$ch->queue_declare('report_queue',false,true,false,false);

$ch->basic_consume('report_queue','',false,true,false,false, function($msg){
    sleep(3); // simulate delay
    $report = new ReportGenerator();
    $time = json_decode($msg->getBody(),true)["request_time"];
    $report->generate($time);
    echo "Report generated - report_{$time}.csv";
});

while ($ch->is_consuming()) {
    $ch->wait();
}
