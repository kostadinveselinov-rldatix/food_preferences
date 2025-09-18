<?php

if (!isset($currentPage)) {
    if(isset($_GET['page']))
        $currentPage = (int)$_GET['page'];
    else
        $currentPage = 0;
}

if (!isset($pageSize)) {
    if(isset($_GET['size']))
        $pageSize = (int)$_GET['size'];
    else
        $pageSize = 10;
}

if (!isset($totalItems)) $totalItems = 0;
if (!isset($pageSizeOptions)) $pageSizeOptions = [5,10, 20, 50, 100, 500, 1000,5000];

$totalPages = (int)ceil($totalItems / $pageSize);

// Build query string helper
function buildQuery(array $overrides = []) {
    $params = array_merge($_GET, $overrides);
    return '?' . http_build_query($params);
}

?>

<div>
    <!-- Page Size Dropdown -->
    <form method="get" style="display:inline-block;">
        <label for="page-size">Show:</label>
        <select id="page-size" name="size" onchange="this.form.submit()">
            <?php foreach ($pageSizeOptions as $sizeOption): ?>
                <option value="<?= $sizeOption ?>" <?= $sizeOption == $pageSize ? 'selected' : '' ?>>
                    <?= $sizeOption ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <div style="display:inline-block; margin-left:10px;">
        <?php if ($currentPage > 0): ?>
            <a href="<?= buildQuery(['page' => $currentPage - 1]) ?>">« Previous</a>
        <?php endif; ?>

        <span> Page <?= $currentPage + 1 ?> of <?= $totalPages ?> </span>

        <?php if ($currentPage + 1 < $totalPages): ?>
            <a href="<?= buildQuery(['page' => $currentPage + 1]) ?>">Next »</a>
        <?php endif; ?>
    </div>
</div>