<?php

/**
 * Community - board
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

$REX['ADDON']['install']['board'] = 1;
$REX['ADDON']['installmsg']['board'] = "";

rex_register_extension('OUTPUT_FILTER', function () {

    $table = array(
        'table_name' => 'rex_com_board_post',
        'name' => 'translate:com_board_name',
        'list_amount' => 50,
        'list_sortfield' => 'created',
        'list_sortorder' => 'DESC',
        'search' => 1,
        'status' => 1,
        'export' => 1,
        'import' => 1,

    );

    $fields = array();

    $fields[] = array(
        'table_name' => 'rex_com_board_post',
        'prio' => 1,
        'type_id' => 'value',
        'type_name' => 'text',
        'name' => 'board_key',
        'label' => 'translate:com_board_key',
        'list_hidden' => 1,
        'search' => 1
    );

    $fields[] = array(
        'table_name' => 'rex_com_board_post',
        'prio' => 2,
        'type_id' => 'value',
        'type_name' => 'be_manager_relation',
        'name' => 'thread_id',
        'label' => 'translate:com_board_thread',
        'table' => 'rex_com_board_post',
        'field' => 'title',
        'filter' => 'thread_id = ',
        'empty_option' => 1,
        'type' => 2,
        'size' => 1,
        'list_hidden' => 1,
        'search' => 1
    );

    $fields[] = array(
        'table_name' => 'rex_com_board_post',
        'prio' => 3,
        'type_id' => 'value',
        'type_name' => 'text',
        'name' => 'title',
        'label' => 'translate:com_board_title',
        'list_hidden' => 0,
        'search' => 1
    );

    $fields[] = array(
        'table_name' => 'rex_com_board_post',
        'prio' => 4,
        'type_id' => 'validate',
        'type_name' => 'empty',
        'name' => 'title',
        'message' => 'translate:com_board_enter_title',
    );

    $fields[] = array(
        'table_name' => 'rex_com_board_post',
        'prio' => 5,
        'type_id' => 'value',
        'type_name' => 'textarea',
        'name' => 'message',
        'label' => 'translate:com_board_message',
        'list_hidden' => 0,
        'search' => 1
    );

    $fields[] = array(
        'table_name' => 'rex_com_board_post',
        'prio' => 6,
        'type_id' => 'validate',
        'type_name' => 'empty',
        'name' => 'message',
        'message' => 'translate:com_board_enter_message',
    );

    $uploadDir = rex_path::pluginData('community', 'board', 'attachments');
    rex_dir::create($uploadDir);

    $fields[] = array(
        'table_name' => 'rex_com_board_post',
        'prio' => 7,
        'type_id' => 'value',
        'type_name' => 'upload',
        'name' => 'attachment',
        'label' => 'translate:com_board_attachment',
        'max_size' => 10000,
        'types' => '.gif,.jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx,.zip,.tar,.rar,',
        'messages' => ',translate:com_board_attachment_error_max_size,translate:com_board_attachment_error_type,,translate:com_board_attachment_delete',
        'upload_folder' => $uploadDir,
        'list_hidden' => 1,
        'search' => 0
    );

    $fields[] = array(
        'table_name' => 'rex_com_board_post',
        'prio' => 8,
        'type_id' => 'value',
        'type_name' => 'be_manager_relation',
        'name' => 'user_id',
        'label' => 'translate:com_user',
        'table' => 'rex_com_user',
        'field' => 'firstname, " ", name',
        'type' => 2,
        'empty_option' => 1,
        'size' => 1,
        'list_hidden' => 0,
        'search' => 1
    );

    $fields[] = array(
        'table_name' => 'rex_com_board_post',
        'prio' => 9,
        'type_id' => 'value',
        'type_name' => 'checkbox',
        'name' => 'status',
        'label' => 'translate:status',
        'default' => 1,
        'list_hidden' => 0,
        'search' => 1
    );

    $fields[] = array(
        'table_name' => 'rex_com_board_post',
        'prio' => 10,
        'type_id' => 'value',
        'type_name' => 'datestamp',
        'name' => 'created',
        'label' => 'mysql',
        'only_empty' => 1,
        'list_hidden' => 1,
        'search' => 1
    );

    $fields[] = array(
        'table_name' => 'rex_com_board_post',
        'prio' => 11,
        'type_id' => 'value',
        'type_name' => 'datestamp',
        'name' => 'updated',
        'label' => 'mysql',
        'only_empty' => 0,
        'list_hidden' => 1,
        'search' => 1
    );

    $fields[] = array(
        'table_name' => 'rex_com_board_post',
        'prio' => 12,
        'type_id' => 'value',
        'type_name' => 'be_manager_relation',
        'name' => 'notifications',
        'label' => 'translate:com_board_notifications',
        'table' => 'rex_com_user',
        'field' => 'firstname, " ", name',
        'relation_table' => 'rex_com_board_thread_notification',
        'type' => 3,
        'empty_option' => 1,
        'size' => 10,
        'list_hidden' => 1,
        'search' => 0
    );

    rex_xform_manager_table_api::setTable($table, $fields);

    $table = array(
        'table_name' => 'rex_com_board_thread_notification',
        'name' => 'translate:com_board_thread_notification',
        'list_amount' => 50,
        'list_sortfield' => 'id',
        'list_sortorder' => 'ASC',
        'search' => 1,
        'status' => 1,
        'hidden' => 1,
        'export' => 1,
        'import' => 1,
    );

    $fields = array();

    $fields[] = array(
        'table_name' => 'rex_com_board_thread_notification',
        'prio' => 1,
        'type_id' => 'value',
        'type_name' => 'be_manager_relation',
        'name' => 'thread_id',
        'label' => 'translate:com_board_thread',
        'table' => 'rex_com_board_post',
        'field' => 'title',
        'filter' => 'thread_id = ',
        'type' => 2,
        'empty_option' => 0,
        'size' => 1,
        'list_hidden' => 0,
        'search' => 1
    );

    $fields[] = array(
        'table_name' => 'rex_com_board_thread_notification',
        'prio' => 2,
        'type_id' => 'value',
        'type_name' => 'be_manager_relation',
        'name' => 'user_id',
        'label' => 'translate:com_user',
        'table' => 'rex_com_user',
        'field' => 'firstname, " ", name',
        'type' => 2,
        'empty_option' => 0,
        'size' => 1,
        'list_hidden' => 0,
        'search' => 1
    );

    rex_xform_manager_table_api::setTable($table, $fields);

    rex_xform_manager_table_api::generateTablesAndFields();

    $sql = rex_sql::factory();
    $sql->setQuery('SELECT id FROM rex_xform_email_template WHERE name = "board_thread_notification" LIMIT 1');
    if (!$sql->getRows()) {
        $sql->setTable('rex_xform_email_template');
        $sql->setValue('name', 'board_thread_notification');
        $sql->setValue('mail_from', 'no_reply@redaxo.org');
        $sql->setValue('mail_from_name', 'REDAXO Community Demo');
        $sql->setValue('subject', 'REDAXO Community Demo: Ein neuer Forumsbeitrag');
        $sql->setValue('body', 'Hallo ###user###,

###post_user### hat im Thread "###thread_title###" einen neuen Beitrag erstellt.

http://###REX_SERVER###/###post_url###');
        $sql->insert();
    }

    $info = rex_generateAll(); // kill cache ..

}, array(), REX_EXTENSION_LATE);
