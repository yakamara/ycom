<?php

$content = '';
$addon = rex_addon::get('ycom');

$article_id = $params['article_id'];
$clang = $params['clang'];
$ctype = $params['ctype'];

$yform = new rex_yform();
$yform->setDebug();
$yform->setObjectparams('form_action', rex_url::backendController(['page' => 'content/edit', 'article_id' => $article_id, 'clang' => $clang, 'ctype' => $ctype], false));
$yform->setObjectparams('form_id', 'ycom_auth-perm');
$yform->setObjectparams('form_name', 'ycom_auth-perm');
$yform->setHiddenField('ycom_auth_func', 'perm');

$yform->setObjectparams('form_showformafterupdate', 1);

$yform->setObjectparams('main_table', rex::getTable('article'));
$yform->setObjectparams('main_id', $article_id);
$yform->setObjectparams('main_where', 'id = ' . $article_id . ' and clang_id = ' . $clang);
$yform->setObjectparams('getdata', true);

$yform->setValueField('select', ['ycom_auth_type', $addon->i18n('ycom_auth_perm'), rex_ycom_auth::$perms, '', 0]);

$yform->setValueField('select', ['ycom_group_type', $addon->i18n('ycom_group_type'), rex_ycom_group::$perms, '', 0]);
$yform->setValueField('select', ['ycom_groups', $addon->i18n('ycom_groups'), rex_ycom_group::getGroups(), '', 0, 'size' => 5,'multiple' => true]);

$yform->setActionField('db', [rex::getTable('article'), 'id = ' . $article_id]);
$yform->setObjectparams('submit_btn_label', $addon->i18n('ycom_auth_update_perm'));
$form = $yform->getForm();

if ($yform->objparams['actions_executed']) {

    // update all language articles

    $com_auth_type = $this->objparams['value_pool']['sql']["ycom_auth_type"];
    $com_group_type = $this->objparams['value_pool']['sql']["ycom_group_type"];
    $com_groups = $this->objparams['value_pool']['sql']["ycom_groups"];

    /*$updates = rex_sql::factory()
        ->setTable(rex::getTable('article'))
        ->setWhere('id = ?', $article_id)
        ->setValue('ycom_auth_type', $ycom_auth_type)
        ->setValue('ycom_group_type', $ycom_group_type)
        ->setValue('ycom_groups', $ycom_groups)
        ->update();
    */

    // TODO: trigger ARTICLE_UPDATE
    // $article_id

    $form = rex_view::success($addon->i18n('ycom_auth_perm_updated')) . $form;
    rex_article_cache::delete($article_id, $clang);

} else {

}

$form = '<section id="rex-page-sidebar-ycom_auth-perm" data-pjax-container="#rex-page-sidebar-ycom_auth-perm" data-pjax-no-history="1">'.$form.'</section>';

return $form;
