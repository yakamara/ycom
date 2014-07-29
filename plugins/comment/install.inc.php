<?php

/**
 * Community - comment
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

$REX['ADDON']['install']['comment'] = 1;
$REX['ADDON']['installmsg']['comment'] = "";


rex_register_extension('OUTPUT_FILTER', function () {

    $table = array(
      'table_name' => 'rex_com_comment',
      'name' => 'translate:com_comment',
      'list_amount' => 50,
      'status' => 1,
      'export' => 1,
      'import' => 1,

    );

    $fields = array();
    // ('rex_com_comment', 10, 'value', 'textarea', 'comment', 'translate:com_comment_name', '', '', '', '', '', '', '', 1, 1);");
    $fields[] = array(
      'table_name' => 'rex_com_comment',
      'prio' => 100,
      'type_id' => 'value',
      'type_name' => 'textarea',
      'name' => 'comment',
      'label' => 'translate:com_comment_name',
      'list_hidden' => 0,
      'search' => 1
    );

    // ('rex_com_comment', 20, 'validate', 'empty', 'comment', 'translate:com_comment_enter_comment', '', '', '', '', '', '', '', 1, 0);");
    $fields[] = array(
      'table_name' => 'rex_com_comment',
      'prio' => 110,
      'type_id' => 'validate',
      'type_name' => 'empty',
      'name' => 'comment',
      'message' => 'translate:com_comment_enter_comment',
    );

    // ('rex_com_comment', 30, 'value', 'text', 'email', 'translate:email', '', '', '', '', '', '', '', 0, 1);");
    $fields[] = array(
      'table_name' => 'rex_com_comment',
      'prio' => 200,
      'type_id' => 'value',
      'type_name' => 'text',
      'name' => 'email',
      'label' => 'translate:email',
      'list_hidden' => 0,
      'search' => 1
    );

    // ('rex_com_comment', 35, 'validate', 'type', 'email', 'email', 'translate:com_comment_enteremail', '0', '', '', '', '', '', 1, 0);");
    $fields[] = array(
      'table_name' => 'rex_com_comment',
      'prio' => 210,
      'type_id' => 'validate',
      'type_name' => 'email',
      'name' => 'email',
      'message' => 'translate:com_comment_enteremail',
    );

    // ('rex_com_comment', 40, 'value', 'text', 'name', 'translate:name', '', '', '', '', '', '', '', 0, 1);");
    $fields[] = array(
      'table_name' => 'rex_com_comment',
      'prio' => 300,
      'type_id' => 'value',
      'type_name' => 'text',
      'name' => 'name',
      'label' => 'translate:name',
      'list_hidden' => 0,
      'search' => 1
    );

    // ('rex_com_comment', 45, 'validate', 'empty', 'name', 'translate:com_comment_enter_name', '', '', '', '', '', '', '', 1, 0);");
    $fields[] = array(
      'table_name' => 'rex_com_comment',
      'prio' => 310,
      'type_id' => 'validate',
      'type_name' => 'empty',
      'name' => 'name',
      'message' => 'translate:com_comment_enter_name',
    );

    // ('rex_com_comment', 50, 'value', 'be_manager_relation', 'user_id', 'translate:com_user', 'rex_com_user', 'name', '0', '1', '', '', '', 0, 1);");
    $fields[] = array(
      'table_name' => 'rex_com_comment',
      'prio' => 400,
      'type_id' => 'value',
      'type_name' => 'be_manager_relation',
      'name' => 'user_id',
      'label' => 'translate:com_user',
      'table' => 'rex_com_user',
      'field' => 'email',
      'type' => 2,
      'empty_option' => 1,
      'size' => 1,
      'list_hidden' => 0,
      'search' => 1
    );

    // ('rex_com_comment', 55, 'value', 'text', 'www', 'translate:com_comment_www', '', '', '', '', '', '', '', 1, 1);");
    $fields[] = array(
      'table_name' => 'rex_com_comment',
      'prio' => 500,
      'type_id' => 'value',
      'type_name' => 'text',
      'name' => 'www',
      'label' => 'translate:com_comment_www',
      'list_hidden' => 1,
      'search' => 1
    );

    // ('rex_com_comment', 60, 'value', 'datestamp', 'create_datetime', 'mysql', '', '1', '', '', '', '', '', 1, 0);");

    $fields[] = array(
      'table_name' => 'rex_com_comment',
      'prio' => 600,
      'type_id' => 'value',
      'type_name' => 'datestamp',
      'name' => 'create_datetime',
      'label' => 'mysql',
      'only_empty' => 1,
      'list_hidden' => 1,
      'search' => 1
    );

    // ('rex_com_comment', 70, 'value', 'datestamp', 'update_datetime', 'mysql', '', '0', '', '', '', '', '', 0, 0);");
    $fields[] = array(
      'table_name' => 'rex_com_comment',
      'prio' => 620,
      'type_id' => 'value',
      'type_name' => 'datestamp',
      'name' => 'update_datetime',
      'label' => 'mysql',
      'only_empty' => 0,
      'list_hidden' => 1,
      'search' => 1
    );

    // ('rex_com_comment', 80, 'value', 'checkbox', 'status', 'translate:status', '', '0', '', '', '', '', '', 1, 1);");
    $fields[] = array(
      'table_name' => 'rex_com_comment',
      'prio' => 700,
      'type_id' => 'value',
      'type_name' => 'checkbox',
      'name' => 'status',
      'label' => 'translate:status',
      'default' => 0,
      'list_hidden' => 1,
      'search' => 1
    );

    // ('rex_com_comment', 90, 'value', 'text', 'ckey', 'translate:com_comment_ckey', '', '', '', '', '', '', '', 0, 1);");
    $fields[] = array(
      'table_name' => 'rex_com_comment',
      'prio' => 800,
      'type_id' => 'value',
      'type_name' => 'text',
      'name' => 'ckey',
      'label' => 'translate:com_comment_ckey',
      'list_hidden' => 1,
      'search' => 1
    );

    // ('rex_com_comment', 110, 'value', 'checkbox', 'info_email', 'translate:com_comment_infomail', '', '0', '', '', '', '', '', 1, 1);");
    $fields[] = array(
      'table_name' => 'rex_com_comment',
      'prio' => 900,
      'type_id' => 'value',
      'type_name' => 'checkbox',
      'name' => 'info_email',
      'label' => 'translate:com_comment_infomail',
      'default' => 0,
      'list_hidden' => 1,
      'search' => 1
    );

    // ('rex_com_comment', 130, 'value', 'be_manager_relation', 'reply_to', 'translate:com_comment_replyto', 'rex_com_comment', 'ckey', '0', '1', '', '', '', 1, 1);");
    $fields[] = array(
      'table_name' => 'rex_com_comment',
      'prio' => 1000,
      'type_id' => 'value',
      'type_name' => 'be_manager_relation',
      'name' => 'reply_to',
      'label' => 'translate:com_comment_replyto',
      'table' => 'rex_com_comment',
      'field' => 'ckey',
      'empty_option' => 1,
      'type' => 2,
      'size' => 1,
      'list_hidden' => 0,
      'search' => 1
    );

    // ('rex_com_comment', 190, 'value', 'index', 'ukey', 'email,user_id,name,comment,ckey,www', '', 'sha1', '', '', '', '', '', 1, 0);");
    $fields[] = array(
      'table_name' => 'rex_com_comment',
      'prio' => 1100,
      'type_id' => 'value',
      'type_name' => 'index',
      'name' => 'ukey',
      'names' => 'email,user_id,name,comment,ckey,www',
      'label' => 'translate:com_comment_ckey',
      'function' => 'sha1',
      'list_hidden' => 1,
      'search' => 1
    );

    // ('rex_com_comment', 200, 'validate', 'unique', 'ukey', 'translate:com_comment_enter_exists', '', '', '', '', '', '', '', 1, 0);");
    $fields[] = array(
      'table_name' => 'rex_com_comment',
      'prio' => 1110,
      'type_id' => 'validate',
      'type_name' => 'unique',
      'name' => 'ukey',
      'table' => 'rex_com_comment',
      'message' => 'translate:com_comment_enter_exists',
    );

    rex_xform_manager_table_api::setTable($table, $fields);

    rex_xform_manager_table_api::generateTablesAndFields();

    $info = rex_generateAll(); // kill cache ..

  }, array(), REX_EXTENSION_LATE);