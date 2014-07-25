<?php

/**
 * COM - Plugin - Newsletter
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

rex_register_extension('OUTPUT_FILTER', function () {

    $field = array(
      'table_name' => 'rex_com_user',
      'prio' => 1500,
      'type_id' => 'value',
      'type_name' => 'text',
      'name' => 'newsletter_last_id',
      'label' => 'translate:newsletter_last_id',
      'list_hidden' => 1,
      'search' => 1
    );

    rex_xform_manager_table_api::setTableField('rex_com_user', $field);

    $field = array(
      'table_name' => 'rex_com_user',
      'prio' => 1510,
      'type_id' => 'value',
      'type_name' => 'checkbox',
      'name' => 'newsletter',
      'label' => 'translate:newsletter',
      'default' => 0,
      'list_hidden' => 1,
      'search' => 1
    );

    rex_xform_manager_table_api::setTableField('rex_com_user', $field);

    rex_xform_manager_table_api::generateTablesAndFields();

  }, array(), REX_EXTENSION_LATE);


$REX['ADDON']['install']['newsletter'] = true;
