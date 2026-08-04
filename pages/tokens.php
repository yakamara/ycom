<?php

/**
 * The tokens that are still valid — registration confirmations, password resets, login links.
 *
 * @var Yakamara\YCom\YComAddon $this
 */

use Redaxo\Core\Core;
use Redaxo\Core\Filesystem\Url;
use Redaxo\Core\Http\Request;
use Redaxo\Core\Translation\I18n;
use Redaxo\Core\Util\Formatter;
use Redaxo\Core\View\DataList;
use Redaxo\Core\View\Fragment;
use Redaxo\Core\View\Message;
use Yakamara\YCom\Auth\UserToken;

use function Redaxo\Core\View\escape;

UserToken::clearExpiredTokens();

switch (Request::request('func', 'string', '')) {
    case 'delete_tokens':
        echo Message::success($this->i18n('tokens_deleted', UserToken::deleteAllTokens()));
        break;

    case 'remove_token':
        UserToken::removeTokenByHash(Request::request('hash', 'string', ''));
        echo Message::success($this->i18n('token_removed'));
        break;
}

echo Message::warning(I18n::rawMsg(
    'ycom_user_delete_all_tokens_link',
    Url::currentBackendPage(['func' => 'delete_tokens']),
));

$list = DataList::factory(
    'SELECT hash, user_id, email, type, createdate, expiredate
     FROM ' . Core::getTable('ycom_user_token') . '
     ORDER BY createdate DESC',
);

$list->addColumn('remove_token', '<i class="rex-icon rex-icon-delete"></i>', 0, [
    '<th class="rex-table-icon"></th>',
    '<td class="rex-table-icon">###VALUE###</td>',
]);
$list->setColumnParams('remove_token', ['func' => 'remove_token', 'hash' => '###hash###']);
$list->addLinkAttribute('remove_token', 'data-confirm', I18n::msg('delete') . '?');

$list->setColumnLabel('hash', I18n::msg('ycom_hash'));
$list->setColumnLabel('user_id', I18n::msg('ycom_user_id'));
$list->setColumnLabel('email', I18n::msg('email'));
$list->setColumnLabel('type', I18n::msg('ycom_token_type'));
$list->setColumnLabel('createdate', I18n::msg('createdate'));
$list->setColumnLabel('expiredate', I18n::msg('ycom_expiredate'));

$list->setColumnFormat('hash', 'custom', static fn (): string => '<span title="'
    . escape((string) $list->getValue('hash')) . '">'
    . escape(Formatter::truncate((string) $list->getValue('hash'), ['length' => 30]))
    . '</span>');

foreach (['createdate', 'expiredate'] as $column) {
    $list->setColumnFormat($column, 'custom', static fn (): string => Formatter::intlDateTime(
        (string) $list->getValue($column),
        IntlDateFormatter::SHORT,
    ));
}

foreach (['user_id', 'type', 'createdate', 'expiredate'] as $column) {
    $list->setColumnSortable($column);
}

$fragment = new Fragment();
$fragment->setVar('title', I18n::msg('ycom_token_caption'));
$fragment->setVar('content', $list->get(), false);
echo $fragment->parse('core/page/section.php');
