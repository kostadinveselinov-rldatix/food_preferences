<?php
require_once __DIR__ . '/../bootstrap.php';
require_once \BASE_PATH . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

if($_SERVER['REQUEST_METHOD']==='POST'){
    $conn=new AMQPStreamConnection("rabbitmq",5672,"guest","guest");
    $ch=$conn->channel();
    $ch->queue_declare('report_queue',false,false,false,false);
    $ch->basic_publish(new AMQPMessage(json_encode(['request_time'=>time()])), '', 'report_queue');
    echo "Report is being generated. Refresh this page in a moment.";
        echo '<script>setTimeout(() => { window.location.href = "/report/download"; }, 2000);</script>';
    exit();
}

echo '<form method="post"><button type="submit">Generate Report</button></form>';

