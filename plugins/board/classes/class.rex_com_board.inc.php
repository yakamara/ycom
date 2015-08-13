<?php

class rex_com_board
{
    private $name = '';
    private $key = '';
    private $url = '';

    /** @var rex_pager */
    private $pager;
    private $threadsPerPage = 10;
    private $postsPerPage = 10;
    private $notificationTemplate;
    private $adminGroup;

    public function rex_com_board($key, $name = '')
    {
        $this->setKey($key);
        $this->setName($name);
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setKey($key)
    {
        $this->key = $key;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getPager()
    {
        return $this->pager;
    }

    public function setThreadsPerPage($threadsPerPage)
    {
        $this->threadsPerPage = $threadsPerPage;
    }

    public function setPostsPerPage($postsPerPage)
    {
        $this->postsPerPage = $postsPerPage;
    }

    public function setNotificationTemplate($notificationTemplate)
    {
        $this->notificationTemplate = $notificationTemplate;
    }

    public function setAdminGroup($groupId)
    {
        $this->adminGroup = $groupId;
    }

    public function isBoardAdmin(rex_com_user $user = null)
    {
        $user = $user ?: rex_com_user::getMe();

        if (!$user || !$this->adminGroup) {
            return false;
        }

        $groups = explode(',', $user->getValue('rex_com_group'));
        return in_array($this->adminGroup, $groups);
    }

    public function getUrl(array $params = array())
    {
        $url = $this->url;

        if ($params) {
            $url .= false === strpos($url, '?') ? '?' : '&amp;';
            $url .= http_build_query($params, null, '&amp;');
        }

        return $url;
    }

    public function getCurrentUrl(array $params = array())
    {
        $defaultParams = array(
            'function' => rex_get('function', 'string'),
            'thread' => rex_get('thread', 'int'),
            'start' => rex_get('start', 'int'),
        );

        $params = array_merge($defaultParams, $params);
        $params = array_filter($params);

        return $this->getUrl($params);
    }

    public function getPostUrl(rex_com_board_post $post)
    {
        return $this->getUrl(array('thread' => $post->getThreadId(), 'post' => $post->getId())) . '#' . $this->getPostIdAttribute($post);
    }

    public function getPostIdAttribute($post)
    {
        if ($post instanceof rex_com_board_post) {
            $post = $post->getId();
        }

        return sprintf('board-%s-post-%d', $this->getKey(), $post);
    }

    public function getPostDeleteUrl(rex_com_board_post $post)
    {
        return $this->getCurrentUrl(array(
            'post' => $post->getId(),
            'function' => 'delete',
        ));
    }

    public function getAttachmentUrl(rex_com_board_post $post)
    {
        return $this->getUrl(array('thread' => $post->getThreadId(), 'post' => $post->getId(), 'function' => 'attachment_download'));
    }

    /**
     * @return rex_com_board_thread[]
     */
    public function getThreads()
    {
        $this->pager = new rex_pager($this->threadsPerPage);

        return $this->getPosts(
            'thread_id = ""',
            '(SELECT MAX(created) FROM rex_com_board_post p2 WHERE (p2.thread_id = p.id OR p2.id = p.id) AND status = 1) DESC'
        );
    }

    /**
     * @param rex_com_board_thread $thread
     * @param int                  $findPost
     *
     * @return rex_com_board_post[]
     */
    public function getThreadPosts(rex_com_board_thread $thread, $findPost = null)
    {
        $this->pager = new rex_pager($this->postsPerPage);

        return $this->getPosts(
            sprintf('thread_id = %d OR id = %1$d', $thread->getId()),
            'created ASC',
            $findPost
        );
    }

    public function getPosts($where = '', $order = 'created ASC', $findPost = null)
    {
        $db = rex_sql::factory();
        //$db->debugsql = 1;

        $where = sprintf(' WHERE board_key = "%s" AND status = 1 AND (%s)', $db->escape($this->getKey()), $where);

        $db->setQuery('SELECT COUNT(*) as count FROM rex_com_board_post p '.$where);
        $count = $db->getValue('count');

        $this->pager->setRowCount($count);

        $order = 'ORDER BY '.$order;

        if ($findPost) {
            $db->setQuery('SELECT COUNT(*) as count FROM rex_com_board_post p '.$where.' AND created < (SELECT created FROM rex_com_board_post WHERE id = '.((int) $findPost).')'.$order);
            $lessCount = $db->getValue('count');

            $cursor = ((int) ($lessCount / $this->pager->getRowsPerPage())) * $this->pager->getRowsPerPage();
            $cursor = $this->pager->validateCursor($cursor);
            $_REQUEST[$this->pager->getCursorName()] = $cursor;
        }

        $limit = ' LIMIT '.$this->pager->getCursor().', '.$this->pager->getRowsPerPage();

        $posts_sql = $db->getArray('select * from rex_com_board_post p '.$where.$order.$limit);

        $posts = array();
        foreach ($posts_sql as $data) {
            if ($data['thread_id']) {
                $posts[] = new rex_com_board_post($data);
            } else {
                $posts[] = new rex_com_board_thread($data);
            }
        }
        return $posts;
    }

    /**
     * @param int $id
     * @return null|rex_com_board_post|rex_com_board_thread
     */
    public function getPost($id)
    {
        $sql = rex_sql::factory();
        $sql->setQuery('SELECT * FROM rex_com_board_post WHERE status = 1 and id = ' . (int) $id);

        if (!$sql->getRows()) {
            return null;
        }

        $data = $sql->getRow();
        if ($data['thread_id']) {
            return new rex_com_board_post($data);
        }

        return new rex_com_board_thread($data);
    }

    public function getView()
    {
        $thread = rex_get('thread', 'int');
        $function = rex_get('function', 'string');

        if (!$thread) {
            if ('create_thread' === $function) {
                $xform = $this->getForm();
                $form = $xform->getForm();

                if ($xform->getObjectparams('actions_executed')) {
                    $thread = rex_com_board_thread::get($xform->getObjectparams('main_id'));

                    if (isset($xform->objparams['value_pool']['email']['notifications']) && $xform->objparams['value_pool']['email']['notifications']) {
                        $thread->addNotificationUser(rex_com_user::getMe());
                    }

                    header('Location: ' . htmlspecialchars_decode($this->getUrl(array('thread' => $thread->getId()))));
                    exit;
                }

                return $this->render('thread.create.tpl.php', compact('form'));
            }

            $threads = $this->getThreads();
            return $this->render('threads.tpl.php', compact('threads'));
        }

        $thread = rex_com_board_thread::get($thread);

        if ('enable_notifications' === $function) {
            $thread->addNotificationUser(rex_com_user::getMe());
        }
        if ('disable_notifications' === $function) {
            $thread->removeNotificationUser(rex_com_user::getMe());
        }

        if ('create_post' === $function) {
            $xform = $this->getForm();
            $xform->setValueField('hidden', array('thread_id', $thread->getId()));
            $xform->setValueField('objparams', array('value.title.default', 'Re: ' . $thread->getTitle(), ''));

            $form = $xform->getForm();

            if ($xform->getObjectparams('actions_executed')) {
                $post = rex_com_board_post::get($xform->getObjectparams('main_id'));

                $this->sendNotifications($thread, $post);

                if (isset($xform->objparams['value_pool']['email']['notifications']) && $xform->objparams['value_pool']['email']['notifications']) {
                    $thread->addNotificationUser(rex_com_user::getMe());
                }

                header('Location: ' . htmlspecialchars_decode($this->getPostUrl($post)));
                exit;
            }

            return $this->render('post.create.tpl.php', compact('thread', 'form'));
        }

        if ('delete' === $function && $this->isBoardAdmin() && ($id = rex_get('post', 'int')) && $post = $this->getPost($id)) {
            $this->deletePost($post);

            $params = array();
            if (!$post instanceof rex_com_board_thread) {
                $params = array(
                    'thread' => rex_get('thread', 'int'),
                    'start' => rex_get('start', 'int'),
                );
            }
            header('Location: ' . htmlspecialchars_decode($this->getUrl($params)));
            exit;
        }

        $post = rex_get('post', 'int');

        if ($post && 'attachment_download' === $function) {
            $post = rex_com_board_post::get($post);
            $this->sendAttachment($post);
            exit;
        }

        $posts = $this->getThreadPosts($thread, $post);
        return $this->render('posts.tpl.php', compact('thread', 'posts'));
    }

    private function deletePost(rex_com_board_post $post)
    {
        $sql = rex_sql::factory();

        if (!$post instanceof rex_com_board_thread) {
            if ($post->hasAttachment()) {
                $file = rex_path::pluginData('community', 'board', 'attachments/'.$post->getRealAttachment());
                rex_file::delete($file);
            }

            $sql->setQuery('DELETE FROM rex_com_board_post WHERE id = '.(int) $post->getId());
            return;
        }

        $where = 'id = '.(int) $post->getId().' OR thread_id = '.(int) $post->getId();
        $attachments = $sql->getArray('SELECT attachment FROM rex_com_board_post WHERE attachment != "" AND ('.$where.')');
        foreach ($attachments as $attachment) {
            $file = rex_path::pluginData('community', 'board', 'attachments/'.$attachment['attachment']);
            rex_file::delete($file);
        }

        $sql->setQuery('DELETE FROM rex_com_board_post WHERE '.$where);
    }

    private function getForm()
    {
        $xform = new rex_xform();
        $xform->setObjectparams('real_field_names', true);
        $xform->setObjectparams('form_action', $this->getCurrentUrl());

        $xform->setValueField('hidden', array('board_key', $this->getKey()));
        $xform->setValueField('hidden', array('user_id', rex_com_user::getMe()->getId()));
        $xform->setValueField('hidden', array('status', 1));

        $xform->setValueField('text', array('title', 'translate:com_board_title'));
        $xform->setValidateField('empty', array('title', 'translate:com_board_enter_title'));
        $xform->setValueField('textarea', array('message', 'translate:com_board_message'));
        $xform->setValidateField('empty', array('message', 'translate:com_board_enter_message'));
        $xform->setValueField('upload', array(
            'name' => 'attachment',
            'label' => 'translate:com_board_attachment',
            'max_size' => 10000,
            'types' => '.gif,.jpg,.jpeg,.png,.pdf',
            'messages' => ',translate:com_board_attachment_error_max_size,translate:com_board_attachment_error_type,,translate:com_board_attachment_delete',
            'upload_folder' => rex_path::pluginData('community', 'board', 'attachments')
        ));
        $xform->setValueField('checkbox', array('notifications', 'translate:com_board_notifications', 'no_db' => 'no_db'));

        $xform->setValueField('datestamp', array('created', 'mysql', '', '1'));
        $xform->setValueField('datestamp', array('updated', 'mysql', '', '0'));

        $xform->setActionField('db', array('rex_com_board_post'));

        return $xform;
    }

    private function sendNotifications(rex_com_board_thread $thread, rex_com_board_post $post)
    {
        if (!$this->notificationTemplate) {
            return;
        }

        $template = $this->notificationTemplate;

        register_shutdown_function(function () use ($thread, $post, $template) {
            $template = rex_xform_emailtemplate::getTemplate($template);
            if (!$template) {
                return;
            }

            $userIds = $thread->getNotificationUsers();
            foreach ($userIds as $id) {
                if ($id == rex_com_user::getMe()->getId()) {
                    continue;
                }

                $user = rex_com_user::getById($id);
                $t = rex_xform_emailtemplate::replaceVars($template, array(
                    'user' => $user->getFullName(),
                    'thread_title' => $thread->getTitle(),
                    'post_user' => $post->getUser()->getFullName(),
                    'post_url' => htmlspecialchars_decode($this->getPostUrl($post)),
                ));
                $t['mail_to'] = $user->getEmail();
                $t['mail_to_name'] = $user->getFullName();

                rex_xform_emailtemplate::sendMail($t);
            }
        });
    }

    private function sendAttachment(rex_com_board_post $post)
    {
        while (ob_get_level()) {
            ob_end_clean();
        }

        $file = rex_path::pluginData('community', 'board', 'attachments/'.$post->getRealAttachment());

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.$post->getAttachment());
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    }

    public function render($template, array $params = array())
    {
        extract($params);

        ob_start();
        include $this->findTemplate($template);
        return ob_get_clean();
    }

    private function findTemplate($template)
    {
        $paths[] = rex_path::pluginData('community', 'board', 'templates/' . $template);
        $paths[] = rex_path::plugin('community', 'board', 'templates/' . $template);

        foreach ($paths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        throw new \RuntimeException(sprintf('Template "%s" not found', $template));
    }
}
