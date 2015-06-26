<?php
    /** @var rex_com_board $this */

    $pager = $this->getPager();
?>

<div class="pagination">
    <?php for ($page = $pager->getFirstPage(); $page <= $pager->getLastPage(); ++$page): ?>
        <a<?= $pager->isActivePage($page) ? ' class="active"' : '' ?> href="<?= $this->getCurrentUrl(array('start' => $pager->getCursor($page))) ?>"><?= $page + 1 ?></a>
    <?php endfor ?>
</div>
