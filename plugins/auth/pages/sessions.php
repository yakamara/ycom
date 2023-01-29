<?php

/** @var rex_addon $this */

echo rex_view::title($this->i18n('ycom_title'));

rex_ycom_user_session::clearExpiredSessions();

$func = rex_request('func', 'string', '');
switch ($func) {
    case 'remove_session':
        // delete session.
        $session_id = rex_request('session_id', 'string', '');
        $user_id = rex_request::get('user_id', 'int', 0);

        rex_ycom_user_session::getInstance()->removeSession($user_id, $session_id);

        rex_sql::factory()->setQuery('delete from '.rex::getTablePrefix().'ycom_user_session where session_id=:session_id', [
            'session_id' => $session_id,
        ]);

        echo rex_view::success($this->i18n('session_removed'));
        // no break
    case 'create_session':
        $user_id = rex_request::get('user_id', 'int', null);
        $ycom_user = rex_ycom_user::get($user_id);
        if ($ycom_user) {
            if (1 > $ycom_user->getValue('status')) {
                echo rex_view::error(rex_i18n::rawMsg('ycom_session_could_not_been_added'));
            } else {
                $_SESSION[rex::getProperty('instname')][rex_ycom_auth::$sessionKey] = [
                    'UID' => $ycom_user->getId(),
                ];
                rex_ycom_user_session::getInstance()->storeCurrentSession($ycom_user);
                echo rex_view::success(rex_i18n::rawMsg('ycom_session_added_ready_to_login', rex_getUrl($this->getConfig('article_id_jump_ok'))));

                rex_ycom_log::log($ycom_user, rex_ycom_log::TYPE_IMPERSONATE, [json_encode(
                    [
                        'be_user_id' => rex::getUser()->getValue('id'),
                        'be_user_login' => rex::getUser()->getValue('login'),
                        'be_user_name' => rex::getUser()->getValue('name'),
                        'be_user_email' => rex::getUser()->getValue('email'),
                    ]
                ),
                ]);
            }
        }
}

$list = rex_list::factory('SELECT session_id, ip, user_id, useragent, starttime, last_activity from '.rex::getTablePrefix().'ycom_user_session');

$list->addColumn('remove_session', '<i class="rex-icon rex-icon-delete"></i>', 0, ['<th class="rex-table-icon"></th>', '<td class="rex-table-icon">###VALUE###</td>']);
$list->setColumnParams('remove_session', ['func' => 'remove_session', 'session_id' => '###session_id###', 'user_id' => '###user_id###']);
$list->setColumnLabel('session_id', rex_i18n::msg('session_id'));
$list->setColumnLabel('user_id', rex_i18n::msg('ycom_user_id'));
$list->setColumnLabel('ip', rex_i18n::msg('ip'));
$list->setColumnLabel('useragent', rex_i18n::msg('user_agent'));
$list->setColumnLabel('starttime', rex_i18n::msg('starttime'));
$list->setColumnLabel('last_activity', rex_i18n::msg('last_activity'));

$list->setColumnFormat('last_activity', 'custom', static function () use ($list) {
    if (session_id() === $list->getValue('session_id')) {
        return rex_i18n::msg('active_session');
    }
    return rex_formatter::intlDateTime((string) $list->getValue('last_activity'), IntlDateFormatter::SHORT);
});
$list->setColumnFormat('starttime', 'custom', static function () use ($list) {
    return rex_formatter::intlDateTime((string) $list->getValue('starttime'), IntlDateFormatter::SHORT);
});

$list->setColumnSortable('last_activity');
$list->setColumnSortable('active_session');
$list->setColumnSortable('ip');
$list->setColumnSortable('user_id');
$list->setColumnSortable('starttime');
$list->setColumnSortable('useragent');

$content = $list->get();

$fragment = new rex_fragment();
$fragment->setVar('title', rex_i18n::msg('session_caption'));
$fragment->setVar('content', $content, false);
echo $fragment->parse('core/page/section.php');
