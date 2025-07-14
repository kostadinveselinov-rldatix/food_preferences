<?php

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $file = __DIR__ . '/../src/reports/report.csv';

    if (file_exists($file)) {
        
        header('Content-Description: File Transfer');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        
        // Clear output buffering if any
        flush();
        
        // Read the file and output to the browser
        readfile($file);
        exit;
    } else {
        echo "File does not exist.";
    }

}

echo "Download report";

if(file_exists(\BASE_PATH .'/src/reports/report.csv')){
   echo '<form method="post"><button type="submit">Download Report</button></form>';
}