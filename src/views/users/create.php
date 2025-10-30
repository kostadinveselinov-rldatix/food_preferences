<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Add New User</title>
</head>
<body>
    <?php  require \BASE_PATH . '/src/parts/navigation.php'; ?>
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
        <select name="foods[]" multiple style="height: 150px; padding:5px 20px 5px 20px; margin-top:10px;">

            <?php foreach ($foods as $food): ?>
                <option value="<?= $food["id"] ?>"><?= $food["name"] ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit" style="margin:5px;">Add User</button>
    </form>

    <?php require \BASE_PATH . "/src/parts/printErrors.php";?>
</body>
</html>
