<?php

$content = '';
$addon = rex_addon::get('ycom');

$article_id = $params['article_id'];
$clang = $params['clang'];
$ctype = $params['ctype'];

$yform = new rex_yform();
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
$yform = rex_extension::registerPoint(new rex_extension_point('YCOM_ARTICLE_PERM_SELECT', $yform, [
    'article_id' => $article_id
]));

$yform->setActionField('db', [rex::getTable('article'), 'id = ' . $article_id]);
$yform->setObjectparams('submit_btn_label', $addon->i18n('ycom_auth_update_perm'));
$form = $yform->getForm();

if ($yform->objparams['actions_executed']) {
    // TODO: trigger ARTICLE_UPDATE
    $form = rex_view::success($addon->i18n('ycom_auth_perm_updated')) . $form;
    rex_article_cache::delete($article_id, $clang);
} else {
}

$form = '<section id="rex-page-sidebar-ycom_auth-perm" data-pjax-container="#rex-page-sidebar-ycom_auth-perm" data-pjax-no-history="1">'.$form.'</section>';

return $form;
