<?php
declare(ticks=1);

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__. '/../src/worker/ReportGenerator.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$conn = new AMQPStreamConnection("rabbitmq",5672,"guest","guest");
$ch = $conn->channel();
$ch->queue_declare('report_queue',false,false,false,false);
$ch->basic_consume('report_queue','',false,true,false,false, function($msg){
    $report = new ReportGenerator();
    $report->generate();
    echo "Report generated\n";
});

while(true){
    $ch->wait();
}
