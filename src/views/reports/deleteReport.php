<?php

use App\Session\Session;

if($_SERVER["REQUEST_METHOD"] != "POST" || !isset($_POST['report_name']) || empty($_POST['report_name'])) {
    header("Location: " . \APP_URL . "report/download");
    exit;
}

$fileName = $_POST["report_name"];

// remove from session
if(Session::checkInArray('reports',$fileName)) {
   Session::removeValueFromArray("reports", $fileName);
}


// remove from file system
$reportGenerator = new \App\worker\ReportGenerator();
$reportGenerator->deleteReport($fileName);

header("Location: " . \APP_URL . "report/download");
die();