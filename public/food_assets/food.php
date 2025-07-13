<?php

if(!isset($foods)){
    header("Location: /");
    die();
}

require_once __DIR__ . '/../parts/header.php';
?>
    <h1>Available foods:</h1>
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
require_once __DIR__ . '/../parts/footer.php';
?>