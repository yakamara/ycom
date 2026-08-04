<?php

/**
 * The article permission panel in the structure sidebar.
 *
 * Included from a `STRUCTURE_CONTENT_SIDEBAR` listener; returns its markup instead of echoing it.
 *
 * Addon::includeFile() `extract()`s its context array, so the values arrive as individual
 * variables — not as a `$params` array the way a REDAXO 5 page received them.
 *
 * @var Yakamara\YCom\YComAddon $this
 * @var int $article_id
 * @var int $clang
 * @var int $ctype
 */

use Redaxo\Core\Content\ArticleCache;
use Redaxo\Core\Core;
use Redaxo\Core\ExtensionPoint\Extension;
use Redaxo\Core\ExtensionPoint\ExtensionPoint;
use Redaxo\Core\Filesystem\Url;
use Redaxo\Core\View\Message;
use Yakamara\YCom\Auth\Auth;
use Yakamara\YForm\YForm;


$articleId = (int) ($article_id ?? 0);
$clang = (int) ($clang ?? 0);
$ctype = (int) ($ctype ?? 0);

$user = Core::getUser();

if (1 > $articleId) {
    return '';
}

if (null === $user) {
    return '';
}

$yform = new YForm();
$yform->setObjectparams('form_action', Url::backendController([
    'page' => 'content/edit',
    'article_id' => $articleId,
    'clang' => $clang,
    'ctype' => $ctype,
]));
$yform->setObjectparams('form_id', 'ycom-auth-perm');
$yform->setObjectparams('form_name', 'ycom-auth-perm');
$yform->setHiddenField('ycom_auth_func', 'perm');
$yform->setObjectparams('form_showformafterupdate', 1);

$yform->setObjectparams('main_table', Core::getTable('article'));
$yform->setObjectparams('main_id', $articleId);
$yform->setObjectparams('main_where', 'id = ' . $articleId . ' AND clang_id = ' . $clang);
$yform->setObjectparams('getdata', true);

$yform->setValueField('choice', [
    'name' => 'ycom_auth_type',
    'label' => $this->i18n('ycom_auth_perm'),
    'choices' => Auth::PERMS,
]);

// The group plugin used to add its own fields here; the listener now lives in YComAddon::bootBackend().
$yform = Extension::dispatch(new ExtensionPoint('YCOM_ARTICLE_PERM_SELECT', $yform, [
    'article_id' => $articleId,
]));

$permissionInfo = '';

if ($user->hasPerm('ycomArticlePermissions[]')) {
    $yform->setActionField('db', [Core::getTable('article'), 'id = ' . $articleId . ' AND clang_id = ' . $clang]);
    $yform->setObjectparams('submit_btn_label', $this->i18n('ycom_auth_update_perm'));
} else {
    $yform->setObjectparams('submit_btn_show', false);
    $permissionInfo = '<div><p class="alert alert-danger">' . $this->i18n('no_permission_to_edit') . '</p></div>';
}

$form = $yform->getForm();

if ($yform->objparams['actions_executed'] && $user->hasPerm('ycomArticlePermissions[]')) {
    $form = Message::success($this->i18n('ycom_auth_perm_updated')) . $form;
    ArticleCache::delete($articleId, $clang);
}

// REDAXO 5 disabled the inputs with an inline script when the user lacked the permission. Hiding the
// submit button is enough — the db action is not registered at all in that case, so a hand-crafted
// POST would not write anything either.
return '<section id="rex-page-sidebar-ycom-auth-perm"'
    . ' data-pjax-container="#rex-page-sidebar-ycom-auth-perm" data-pjax-no-history="1">'
    . $permissionInfo . $form . '</section>';
