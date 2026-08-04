<?php

/**
 * The active frontend sessions, with the option to revoke one or to open a session for a user
 * ("log in as this user").
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
use Yakamara\YCom\Auth\Auth;
use Yakamara\YCom\Auth\UserSession;
use Yakamara\YCom\Config;
use Yakamara\YCom\Log;
use Yakamara\YCom\User;

use function Redaxo\Core\View\escape;

UserSession::clearExpiredSessions();

switch (Request::request('func', 'string', '')) {
    case 'delete_sessions':
        echo Message::success($this->i18n('sessions_deleted', UserSession::deleteAllSessions()));
        break;

    case 'remove_session':
        UserSession::getInstance()->removeSession(
            Request::request('session_id', 'string', ''),
            Request::request('user_id', 'string', ''),
        );
        echo Message::success($this->i18n('session_removed'));
        break;

    case 'create_session':
        $userId = Request::get('user_id', 'int', 0);
        $ycomUser = 0 < $userId ? User::get($userId) : null;

        if (null !== $ycomUser) {
            if (User::STATUS_REQUESTED >= (int) $ycomUser->getValue('status')) {
                echo Message::error(I18n::rawMsg('ycom_session_could_not_been_added'));
            } else {
                // Puts the user into *this* browser's frontend session, so the editor can walk the
                // site as them.
                Auth::createSessionForUser($ycomUser);

                echo Message::success(I18n::rawMsg(
                    'ycom_session_added_ready_to_login',
                    Url::article((int) Config::get('article_id_jump_ok')),
                ));

                $beUser = Core::getUser();
                if (null !== $beUser) {
                    Log::log($ycomUser, Log::TYPE_IMPERSONATE, [
                        'be_user_id' => $beUser->id,
                        'be_user_login' => $beUser->login,
                    ]);
                }
            }
        }
        break;
}

echo Message::warning(I18n::rawMsg(
    'ycom_user_session_delete_all_sessions_link',
    Url::currentBackendPage(['func' => 'delete_sessions']),
));

$list = DataList::factory(
    'SELECT session_id, cookie_key, ip, user_id, useragent, starttime, last_activity
     FROM ' . Core::getTable('ycom_user_session') . '
     ORDER BY last_activity DESC',
);

$list->addColumn('remove_session', '<i class="rex-icon rex-icon-delete"></i>', 0, [
    '<th class="rex-table-icon"></th>',
    '<td class="rex-table-icon">###VALUE###</td>',
]);
$list->setColumnParams('remove_session', [
    'func' => 'remove_session',
    'session_id' => '###session_id###',
    'user_id' => '###user_id###',
]);
$list->addLinkAttribute('remove_session', 'data-confirm', I18n::msg('delete') . '?');

$list->removeColumn('cookie_key');

$list->setColumnLabel('session_id', I18n::msg('session_id'));
$list->setColumnLabel('user_id', I18n::msg('ycom_user_id'));
$list->setColumnLabel('ip', I18n::msg('ip'));
$list->setColumnLabel('useragent', I18n::msg('user_agent'));
$list->setColumnLabel('starttime', I18n::msg('starttime'));
$list->setColumnLabel('last_activity', I18n::msg('last_activity'));

$list->setColumnFormat('session_id', 'custom', static function () use ($list): string {
    // A session id is a credential — showing a prefix is enough to tell the rows apart.
    $sessionId = (string) $list->getValue('session_id');
    $label = escape(substr($sessionId, 0, 8)) . '…';

    if (session_id() === $sessionId) {
        $label .= ' <span class="label label-info">' . I18n::msg('active_session') . '</span>';
    }
    if ($list->getValue('cookie_key')) {
        $label .= ' <span class="label label-warning">' . I18n::msg('stay_logged_in') . '</span>';
    }

    return $label;
});

foreach (['starttime', 'last_activity'] as $column) {
    $list->setColumnFormat($column, 'custom', static fn (): string => Formatter::intlDateTime(
        (string) $list->getValue($column),
        IntlDateFormatter::SHORT,
    ));
}

foreach (['user_id', 'ip', 'useragent', 'starttime', 'last_activity'] as $column) {
    $list->setColumnSortable($column);
}

$fragment = new Fragment();
$fragment->setVar('title', I18n::msg('session_caption'));
$fragment->setVar('content', $list->get(), false);
echo $fragment->parse('core/page/section.php');
