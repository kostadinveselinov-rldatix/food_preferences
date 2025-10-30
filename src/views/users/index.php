<?php

if(!isset($users)){
    header("Location: /");
    // die();
}

require \BASE_PATH . '/src/parts/header.php';
?>
    <div style="display:flex; justify-content:space-around; align-items:center">
        <h2>Available users: ( <?= $totalItems ?> records total)</h2>
        <form action="/seeders" method="POST">
            <input type="hidden" name="create_user" value="1" />
            <button>Generate 10 Users</button>
        </form>
    </div>
    <div>
        <a href="<?= \APP_URL . 'user/create'?>" style="font-size:28px">Add user</a>
    </div>
    <div>
        <?php include \BASE_PATH . "/src/parts/pagination.php" ?>
    </div>
    <ol>
            <?php foreach ($users as $user): ?>
                <li style="border-bottom: 1px solid #ccc; padding: 10px;">
                    <div>Name: <?= htmlspecialchars($user->getName()) ?></div>
                    <div>LastName: <?= htmlspecialchars($user->getLastName()) ?></div>
                    <div>Email: <?= htmlspecialchars($user->getEmail()) ?></div>
                    <div>CreatedAt: <?= $user->getCreatedAt()->format('F j, Y, g:i a') ?></div>
                    <div>Foods:
                        <ul>
                            <?php 
                            $userFoods = $user->getFoods();
                            if(empty($userFoods))
                                echo "<li>No food preferences</li>";
                            else

                            foreach ($userFoods as $food): ?>
                                <li><?= htmlspecialchars($food->getName()) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div>
                        Actions:
                        <form action="/user/delete" method="POST">
                            <input type="hidden" name="id" value="<?= $user->getId() ?>" />
                            <button>Delete</button>
                        </form>
                        <a href="<?= \APP_URL . "user/update?id=" . $user->getId()?>">Update user</a>
                    </div>
                </li>        
            <?php endforeach; ?>
    </ol>
<?php
require \BASE_PATH . '/src/parts/footer.php';
?>