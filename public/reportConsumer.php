<?php
error_reporting(E_ALL & ~E_DEPRECATED);
declare(ticks=1);

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__. '/../src/worker/ReportGenerator.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$conn = new AMQPStreamConnection("rabbitmq",5672,"guest","guest");
$ch = $conn->channel();
$ch->queue_declare('report_queue',false,true,false,false);

$ch->basic_consume('report_queue','',false,true,false,false, function($msg){
    sleep(3); // simulate delay
    $report = new ReportGenerator();
    $time = json_decode($msg->getBody(),true)["request_time"];
    $report->generate($time);
    echo "Report generated\n";
    echo $time;
});

while ($ch->is_consuming()) {
    $ch->wait();
}
