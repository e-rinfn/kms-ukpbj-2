<?php
$surroundCount = 2; // Jumlah halaman yang ditampilkan di sekitar halaman aktif
$current = $pager->getCurrentPage();
$last = $pager->getPageCount();
?>

<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">
        <?php if ($current > 1) : ?>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getPageURI(1) ?>" aria-label="First">
                    <span aria-hidden="true">&laquo;&laquo;</span>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getPageURI($current - 1) ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        <?php endif ?>

        <?php
        // Hitung range halaman yang akan ditampilkan
        $start = max(1, $current - $surroundCount);
        $end = min($last, $current + $surroundCount);

        for ($i = $start; $i <= $end; $i++) : ?>
            <li class="page-item <?= $i == $current ? 'active' : '' ?>">
                <a class="page-link" href="<?= $pager->getPageURI($i) ?>">
                    <?= $i ?>
                </a>
            </li>
        <?php endfor ?>

        <?php if ($current < $last) : ?>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getPageURI($current + 1) ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getPageURI($last) ?>" aria-label="Last">
                    <span aria-hidden="true">&raquo;&raquo;</span>
                </a>
            </li>
        <?php endif ?>
    </ul>
</nav>

<div class="text-center text-muted small mt-2">
    Menampilkan <?= ($current - 1) * $pager->getPerPage() + 1 ?>
    sampai <?= min($current * $pager->getPerPage(), $pager->getTotal()) ?>
    dari <?= $pager->getTotal() ?> data
</div>