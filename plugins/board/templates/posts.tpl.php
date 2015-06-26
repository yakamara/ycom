<?php
    /**
     * @var rex_com_board $this
     * @var rex_com_board_thread $thread
     * @var rex_com_board_post[] $posts
     */
?>

<a href="<?= $this->getUrl() ?>">Zur Übersicht</a>

<h1><?= $thread->getTitle() ?></h1>

<p>
    <a href="<?= $this->getCurrentUrl(array('function' => 'create_post')) ?>">Antworten</a>
    <?php if ($thread->isNotificationEnabled(rex_com_user::getMe())): ?>
        <a href="<?= $this->getCurrentUrl(['function' => 'disable_notifications']) ?>">Benachrichtigungen ausschalten</a>
    <?php else: ?>
        <a href="<?= $this->getCurrentUrl(['function' => 'enable_notifications']) ?>">Benachrichtigungen einschalten</a>
    <?php endif ?>
</p>

<?= $this->render('pagination.tpl.php') ?>

<table style="width: 100%">
    <tbody>
        <?php foreach($posts as $post): ?>
            <tr id="<?= $this->getPostIdAttribute($post) ?>">
                <td>
                    <b><?= $post->getUser()->getFullName() ?></b><br><br>
                    <a href="<?= $this->getPostUrl($post) ?>"><?= $post->getCreated('%d.%m.%Y, %H:%M Uhr')?></a>
                </td>
                <td>
                    <h2><?= htmlspecialchars($post->getTitle()) ?></h2>
                    <p><?= nl2br(htmlspecialchars($post->getMessage())) ?></p>
                    <?php if ($post->hasAttachment()): ?>
                        <p>
                            Anhang: <a href="<?= $this->getAttachmentUrl($post) ?>"><?= $post->getAttachment() ?></a>
                        </p>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>

<?= $this->render('pagination.tpl.php') ?>

<p>
    <a href="<?= $this->getCurrentUrl(array('function' => 'create_post')) ?>">Antworten</a>
    <?php if ($thread->isNotificationEnabled(rex_com_user::getMe())): ?>
        <a href="<?= $this->getCurrentUrl(['function' => 'disable_notifications']) ?>">Benachrichtigungen ausschalten</a>
    <?php else: ?>
        <a href="<?= $this->getCurrentUrl(['function' => 'enable_notifications']) ?>">Benachrichtigungen einschalten</a>
    <?php endif ?>
</p>
