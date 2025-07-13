
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
        <input type="text" id="name" name="name" required />
        <br />

        <label for="lastname">Last Name:</label>
        <input type="text" id="lastname" name="lastName" required />
        <br />

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required />
        <br />

        <label for="foods">Food Preferences (multiple select):</label>
        <select name="foods[]" multiple>

            <?php foreach ($foods as $food): ?>
                <option value="<?= $food->getId() ?>"><?= $food->getName() ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Add User</button>
    </form>
</body>
</html>
