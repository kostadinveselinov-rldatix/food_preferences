
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Add New Food</title>
</head>
<body>
    <?php require \BASE_PATH . "/src/parts/navigation.php";?>
    <h1>Add New Food</h1>
    <form action="/food/create" method="POST">
        <label for="name">Food Name:</label><br />
        <input type="text" id="name" name="name" required /><br /><br />

        <button type="submit">Add Food</button>
    </form>

    <?php require \BASE_PATH . "/src/parts/printErrors.php";?>
</body>
</html>
