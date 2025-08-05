<?php

 if (isset($_SESSION['errors'])) {
    echo '<ol style="color: red;">';
    foreach ($_SESSION['errors'] as $field => $errors) {
        echo "<li>" . strtoupper($field) . "</li>";
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li>" . $error . "</li>";
        }
        echo "</ul>";
    }
    unset($_SESSION['errors']);
    echo '</ol>';
}