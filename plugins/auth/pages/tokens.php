<?php

/** @var rex_addon $this */

echo rex_view::title($this->i18n('ycom_title'));

rex_ycom_user_token::clearExpiredTokens();

$func = rex_request('func', 'string', '');
switch ($func) {
    case 'ycom_user_delete_tokens':
        $amount = (int) rex_ycom_user_token::deleteAllTokens();
        echo rex_view::success($this->i18n('tokens_deleted', $amount));
        break;
    case 'remove_token':
        $hash = (string) rex_request::get('hash', 'string', '');
        rex_ycom_user_token::removeTokenByHash($hash);
        echo rex_view::success($this->i18n('token_removed'));
        break;
}

echo rex_view::warning(rex_i18n::rawMsg('ycom_user_delete_all_tokens_link', rex_url::currentBackendPage() . '&func=ycom_user_delete_tokens'));

$list = rex_list::factory('SELECT * from ' . rex::getTable('ycom_user_token') . ' ORDER BY createdate DESC');

$list->addColumn('remove_token', '<i class="rex-icon rex-icon-delete"></i>', 0, ['<th class="rex-table-icon"></th>', '<td class="rex-table-icon">###VALUE###</td>']);
$list->setColumnParams('remove_token', ['func' => 'remove_token', 'hash' => '###hash###']);

$list->setColumnLabel('user_id', rex_i18n::msg('ycom_user_id'));

$list->setColumnLabel('token', rex_i18n::msg('ycom_token'));
$list->setColumnLabel('hash', rex_i18n::msg('ycom_hash'));
$list->setColumnLabel('type', rex_i18n::msg('ycom_token_type'));

$list->setColumnLabel('createdate', rex_i18n::msg('createdate'));
$list->setColumnLabel('expiredate', rex_i18n::msg('ycom_expiredate'));

$list->removeColumn('selector');

$list->setColumnFormat('hash', 'custom', static function () use ($list) {
    return '<span title="' . rex_escape($list->getValue('hash')) . '">' . rex_escape(rex_formatter::truncate($list->getValue('hash'), [
        'length' => 30,
    ])) . '</span>';
});

$list->setColumnFormat('expiredate', 'custom', static function () use ($list) {
    return rex_formatter::intlDateTime((string) $list->getValue('expiredate'), IntlDateFormatter::SHORT);
});
$list->setColumnFormat('createdate', 'custom', static function () use ($list) {
    return rex_formatter::intlDateTime((string) $list->getValue('createdate'), IntlDateFormatter::SHORT);
});

$list->setColumnSortable('user_id');
$list->setColumnSortable('createdate');
$list->setColumnSortable('expiredate');

$content = $list->get();

$fragment = new rex_fragment();
$fragment->setVar('title', rex_i18n::msg('ycom_token_caption'));
$fragment->setVar('content', $content, false);
echo $fragment->parse('core/page/section.php');

// generate_key|activation_key
//
// text|email|E-Mail:|
//
// validate|type|email|email|Bitte geben Sie Ihre E-Mail-Adresse ein.
// validate|empty|email|Bitte geben Sie Ihre E-Mail-Adresse ein.
// validate|in_table|email|rex_ycom_user|email|Für die angegebene E-Mail-Adresse existiert kein Nutzer.|
//
// action|db_query|update rex_ycom_user set activation_key = ? where email = ?|activation_key,email
// action|tpl2email|resetpassword_de|email|
//
// action|showtext|Sie erhalten eine E-Mail mit einem Link, über den Sie das Passwort zurücksetzen können.|<p>|</p>|1

// Brauche User_ID, HASH
// Passwort zurücksetzen, Register, email_change, Newsletter?
// Ans Templatesystem andocken und eigene Kennung bauen REX_YCOM[tokenlink]

// ycom_token|password_reset

// UserToken=[Token][NonDBToken][id]
// Tokenvalidierung
// Form ausführbar, wenn Token valide und danach, wenn Formular ausgeführt wurde, Token löschen
// ycom_token_validate (request und hidden value wenn abgeschickt)

// REX_YCOM[id, token]

// Passwort vergessen / email + id
// Registrierung / email + id
// E-Mail-Änderung / email + id
