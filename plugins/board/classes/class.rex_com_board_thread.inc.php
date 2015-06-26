<?php

class rex_com_board_thread extends rex_com_board_post
{
    private $countReplies;
    private $recentPost;
    private $notificationUsers;

    public static function get($id)
    {
        $sql = rex_sql::factory();
        $sql->setQuery('SELECT * FROM rex_com_board_post WHERE status = 1 and thread_id = "" and id = ' . (int) $id);
        return new self($sql->getRow());
    }

    public function countReplies()
    {
        if (null !== $this->countReplies) {
            return $this->countReplies;
        }

        $sql = rex_sql::factory();
        $sql->setQuery('SELECT COUNT(*) as count FROM rex_com_board_post WHERE status = 1 and thread_id = ' . (int) $this->getId());
        return $this->countReplies = (int) $sql->getValue('count');
    }

    public function getRecentPost()
    {
        if (null !== $this->recentPost) {
            return $this->recentPost;
        }

        $sql = rex_sql::factory();
        $sql->setQuery(sprintf('SELECT * FROM rex_com_board_post WHERE status = 1 AND (thread_id = %d OR id = %1$d) ORDER BY created DESC LIMIT 1', (int) $this->getId()));
        return $this->recentPost = new rex_com_board_post($sql->getRow());
    }

    public function getNotificationUsers()
    {
        if (null !== $this->notificationUsers) {
            return $this->notificationUsers;
        }

        $sql = rex_sql::factory();
        $data = $sql->getArray(sprintf('SELECT user_id FROM rex_com_board_thread_notification WHERE thread_id = %d', $this->getId()));
        $this->notificationUsers = array();
        foreach ($data as $row) {
            $this->notificationUsers[] = $row['user_id'];
        }
        return $this->notificationUsers;
    }

    public function addNotificationUser(rex_com_user $user)
    {
        if (in_array($user->getId(), $this->getNotificationUsers())) {
            return;
        }

        $sql = rex_sql::factory();
        $sql->setTable('rex_com_board_thread_notification');
        $sql->setValue('thread_id', $this->getId());
        $sql->setValue('user_id', $user->getId());
        $sql->insert();

        $this->notificationUsers[] = $user->getId();
    }

    public function removeNotificationUser(rex_com_user $user)
    {
        $sql = rex_sql::factory();
        $sql->setTable('rex_com_board_thread_notification');
        $sql->setWhere(sprintf('thread_id = %d AND user_id = %d', $this->getId(), $user->getId()));
        $sql->delete();

        $this->notificationUsers = null;
    }

    public function isNotificationEnabled(rex_com_user $user)
    {
        return in_array($user->getId(), $this->getNotificationUsers());
    }
}
