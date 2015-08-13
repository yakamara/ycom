<?php
    /** @var rex_com_board $this */

    $pager = $this->getPager();
?>

<?php if ($pager->getRowCount() > $pager->getRowsPerPage()): ?>

<div class="pagination">
    <?php for ($page = $pager->getFirstPage(); $page <= $pager->getLastPage(); ++$page): ?>
        <a<?= $pager->isActivePage($page) ? ' class="pagination__item active-page"' : ' class="pagination__item"' ?> href="<?= $this->getCurrentUrl(array('start' => $pager->getCursor($page))) ?>"><?= $page + 1 ?></a>
    <?php endfor ?>
</div>

<?php endif ?>
