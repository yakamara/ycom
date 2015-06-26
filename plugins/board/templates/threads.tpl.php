<?php
    /**
     * @var rex_com_board $this
     * @var rex_com_board_thread[] $threads
     */
?>

<p>
    <a href="<?= $this->getUrl(array('function' => 'create_thread')) ?>">Neues Thema</a>
</p>

<?= $this->render('pagination.tpl.php') ?>

<table style="width: 100%">
    <thead>
        <tr>
            <th>Themen</th>
            <th>Antworten</th>
            <th>Letzter Beitrag</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($threads as $thread): ?>
            <tr>
                <td>
                    <b><a href="<?= $this->getUrl(array('thread' => $thread->getId())) ?>"><?= htmlspecialchars($thread->getTitle()) ?></a></b><br>
                    von <?= htmlspecialchars($thread->getUser()->getFullName()) ?> am <?= $thread->getCreated('%d.%m.%Y, %H:%M Uhr') ?>
                </td>
                <td><?= $thread->countReplies() ?></td>
                <td>
                    <a href="<?= $this->getPostUrl($thread->getRecentPost()) ?>">
                        von <?= $thread->getRecentPost()->getUser()->getFullName() ?><br>
                        am <?= $thread->getRecentPost()->getCreated('%d.%m.%Y, %H:%M Uhr') ?>
                    </a>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>

<?= $this->render('pagination.tpl.php') ?>
