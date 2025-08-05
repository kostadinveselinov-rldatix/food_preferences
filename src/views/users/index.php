<?php

if(!isset($users)){
    header("Location: /");
    die();
}

require_once \BASE_PATH . '/src/parts/header.php';
?>
    <h1>Available users:</h1>
    <div>
        <a href="<?= \APP_URL . 'user/create'?>" style="font-size:28px">Add user</a>
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
                            if($user->getFoods()->isEmpty())
                                echo "<li>No food preferences</li>";
                            else

                            foreach ($user->getFoods() as $food): ?>
                                <li><?= htmlspecialchars($food->getName()) ?> (<?= $food->getCreatedAt()->format('F j, Y, g:i a') ?>)</li>
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
require_once \BASE_PATH . '/src/parts/footer.php';
?>