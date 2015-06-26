<?php

class rex_com_board_post
{
    private $data;
    private $user;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public static function get($id)
    {
        $sql = rex_sql::factory();
        $sql->setQuery('SELECT * FROM rex_com_board_post WHERE id = ' . (int) $id);
        return new self($sql->getRow());
    }

    public function getId()
    {
        return $this->data['id'];
    }

    public function getThreadId()
    {
        return $this->data['thread_id'] ?: $this->getId();
    }

    public function getTitle()
    {
        return $this->data['title'];
    }

    public function getMessage()
    {
        return $this->data['message'];
    }

    public function hasAttachment()
    {
        return (bool) $this->data['attachment'];
    }

    public function getAttachment()
    {
        if (!$this->hasAttachment()) {
            return null;
        }

        $parts = explode('_', $this->getRealAttachment(), 2);
        return isset($parts[1]) ? $parts[1] : null;
    }

    public function getRealAttachment()
    {
        return $this->data['attachment'];
    }

    /**
     * @return rex_com_user
     */
    public function getUser()
    {
        if (!$this->user) {
            $this->user = rex_com_user::getById($this->data['user_id']);
        }

        return $this->user;
    }

    public function getCreated($format = null)
    {
        $timestamp = strtotime($this->data['created']);

        return $format ? strftime($format, $timestamp) : $timestamp;
    }

    public function getUpdated($format = null)
    {
        $timestamp = strtotime($this->data['updated']);

        return $format ? strftime($format, $timestamp) : $timestamp;
    }
}
