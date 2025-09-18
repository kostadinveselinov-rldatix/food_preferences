<?php

if(!isset($foods)){
    header("Location: /");
    die();
}

require_once \BASE_PATH . "/src/parts/header.php";
?>
    <div style="display:flex; justify-content:space-around; align-items:center">
        <h2>Available food: ( <?= $totalItems ?> records total)</h2>
        <form action="/seeders" method="POST">
            <input type="hidden" name="create_food" value="1" />
            <button>Generate 10 Food items</button>
        </form>
    </div>
    <div>
        <a href="<?= \APP_URL . 'food/create'?>" style="font-size:28px">Add food</a>
    </div>
   <table>
        <thead>
            <tr>
                <th style="padding-right:20px">Name</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($foods as $food): ?>
                <tr>
                    <td><?= htmlspecialchars($food->getName()) ?></td>
                    <td><?= $food->getCreatedAt()->format('F j, Y, g:i a') ?></td>
                    <td>
                        <form action="/food/delete" method="POST">
                            <input type="hidden" name="id" value="<?= $food->getId() ?>" />
                            <button>Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
   </table>

<?php
require_once \BASE_PATH . "/src/parts/footer.php";
?>