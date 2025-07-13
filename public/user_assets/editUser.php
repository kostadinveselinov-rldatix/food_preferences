
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Edit User <?= $user->getName()?></title>
</head>
<body>
    <h1>Edit User <?= $user->getName() . " " . $user->getLastName()?></h1>
    <form action="/user/update" method="POST">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?= $user->getName()?>" required />
        <br />

        <label for="lastname">Last Name:</label>
        <input type="text" id="lastname" name="lastName" value="<?= $user->getLastName()?>" required />
        <br />

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= $user->getEmail()?>" required />
        <br />

        <label for="foods">Food Preferences (multiple select):</label>
        <select name="foods[]" multiple>

            <?php foreach ($foods as $food): ?>
                <option value="<?= $food->getId() ?>"
                <?= (in_array($food->getId(),$userFoodIds))? "selected" : ""?>
                ><?= $food->getName() ?>
            </option>
            <?php endforeach; ?>
        </select>

        <input type="hidden" name="id" value="<?= $user->getId() ?>" />

        <button type="submit">Update User</button>
    </form>
</body>
</html>
