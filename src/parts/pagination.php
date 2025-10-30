<?php

// Only define function once
if (!function_exists('buildQuery')) {
    function buildQuery(array $overrides = []): string {
        $params = array_merge($_GET, $overrides);
        return '?' . http_build_query($params);
    }
}

// Initialize variables only if not already set
if (!isset($currentPage)) {
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 0;
}

if (!isset($pageSize)) {
    $pageSize = isset($_GET['size']) ? (int)$_GET['size'] : 10;
}

if (!isset($totalItems)) {
    $totalItems = 0;
}

if (!isset($pageSizeOptions)) {
    $pageSizeOptions = [5, 10, 20, 50, 100, 500, 1000, 5000];
}

$totalPages = (int)ceil($totalItems / $pageSize);
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
