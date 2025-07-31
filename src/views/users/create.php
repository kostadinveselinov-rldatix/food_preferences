<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Add New User</title>
</head>
<body>
    <h1>Add New User</h1>
    <form action="/user/create" method="POST">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name"  />
        <br />

        <label for="lastname">Last Name:</label>
        <input type="text" id="lastname" name="lastName"  />
        <br />

        <label for="email">Email:</label>
        <input type="email" id="email" name="email"  />
        <br />

        <label for="foods">Food Preferences (multiple select):</label>
        <select name="foods[]" multiple>

            <?php foreach ($foods as $food): ?>
                <option value="<?= $food->getId() ?>"><?= $food->getName() ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Add User</button>
    </form>

    <?php
        if (isset($_SESSION['errors'])) {
            echo '<ol style="color: red;">';
            // var_dump($_SESSION["errors"]);
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
    ?>
</body>
</html>
