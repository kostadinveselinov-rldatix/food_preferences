<?php
// Handle POST download request
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['report_name'])) {
    $fileName = basename($_GET['report_name']);
    $filePath = REPORTS_PATH . '/' . $fileName . ".csv";

    if (file_exists($filePath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $fileName . '.csv"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        flush();
        readfile($filePath);
        exit;
    } else {
        echo "File does not exist.";
        exit;
    }
}


require BASE_PATH . "/src/parts/header.php";

    $reports = \App\Session\Session::get('reports') ?? [];
    $foundAny = false;

    var_dump($reports);

    foreach ($reports as $key => $reportTimestamp) {
        $fullPath = REPORTS_PATH . '/report_' . $reportTimestamp . ".csv";

        if (file_exists($fullPath)) {
            $foundAny = true;
            echo "<a href='". \APP_URL . "report/download?report_name=report_" . $reportTimestamp . "'>Download report - report_{$reportTimestamp}</a>";
            echo "<form method='POST' action='". \APP_URL . "report/delete'>
                <input type='hidden' name='report_name' value='{$reportTimestamp}'>
                <button type='submit'>Delete report</button>
            </form>
            <hr />
                ";
        }
    }

    if (!$foundAny) {
        echo "<p>No reports available for download.</p>";
    }
    ?>
</body>
</html>
