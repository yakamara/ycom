<?php

/**
 * The frontend user activity log.
 *
 * @var Yakamara\YCom\YComAddon $this
 */

use Redaxo\Core\Filesystem\Url;
use Redaxo\Core\Http\Request;
use Redaxo\Core\Log\LogFile;
use Redaxo\Core\Translation\I18n;
use Redaxo\Core\Util\Formatter;
use Redaxo\Core\View\Fragment;
use Redaxo\Core\View\Message;
use Yakamara\YCom\Log;

use function Redaxo\Core\View\escape;

$logFile = Log::logFile();

switch (Request::request('func', 'string', '')) {
    case 'activate_log':
        Log::activate();
        echo Message::success($this->i18n('ycom_user_log_activated'));
        break;

    case 'deactivate_log':
        Log::deactivate();
        echo Message::success($this->i18n('ycom_user_log_deactivated'));
        break;

    case 'delete_log':
        if (Log::delete()) {
            echo Message::success(I18n::msg('syslog_deleted'));
        } else {
            echo Message::error(I18n::msg('syslog_delete_error'));
        }
        break;
}

if (Log::isActive()) {
    echo Message::warning(I18n::rawMsg(
        'ycom_user_log_warning_logisactive',
        Url::currentBackendPage(['func' => 'deactivate_log']),
    ));
} else {
    echo Message::warning(I18n::rawMsg(
        'ycom_user_log_warning_logisinactive',
        Url::currentBackendPage(['func' => 'activate_log']),
    ));
}

$rows = '';

foreach (new LimitIterator(LogFile::factory($logFile), 0, 30) as $entry) {
    $data = $entry->getData();

    $rows .= '
        <tr>
            <td data-title="' . I18n::msg('ycom_user_log_time') . '" class="rex-table-tabular-nums">'
                . Formatter::intlDateTime($entry->getTimestamp(), [IntlDateFormatter::SHORT, IntlDateFormatter::MEDIUM]) . '</td>
            <td data-title="' . I18n::msg('ycom_user_log_ip') . '">' . escape($data[0] ?? '') . '</td>
            <td data-title="' . I18n::msg('ycom_user_log_user_id') . '">' . escape($data[1] ?? '') . '</td>
            <td data-title="' . I18n::msg('ycom_auth_config_login_field') . '">' . escape($data[2] ?? '') . '</td>
            <td data-title="' . I18n::msg('ycom_user_log_type') . '">' . escape($data[3] ?? '') . '</td>
            <td data-title="' . I18n::msg('ycom_user_log_params') . '">' . escape($data[4] ?? '') . '</td>
        </tr>';
}

// REDAXO 5 shifted the columns by one here: the header had six columns but the body started at
// $data[0] for the IP while labelling it with the timestamp header, so every value sat under the
// wrong heading and the params column was always empty.
$content = '
    <table class="table table-hover">
        <thead>
            <tr>
                <th>' . I18n::msg('ycom_user_log_time') . '</th>
                <th>' . I18n::msg('ycom_user_log_ip') . '</th>
                <th>' . I18n::msg('ycom_user_log_user_id') . '</th>
                <th>' . I18n::msg('ycom_auth_config_login_field') . '</th>
                <th>' . I18n::msg('ycom_user_log_type') . '</th>
                <th>' . I18n::msg('ycom_user_log_params') . '</th>
            </tr>
        </thead>
        <tbody>' . $rows . '</tbody>
    </table>';

$fragment = new Fragment();
$fragment->setVar('elements', [[
    'field' => '<button class="btn btn-delete" type="submit" name="del_btn" data-confirm="'
        . I18n::msg('ycom_user_delete_log_msg') . '">' . I18n::msg('syslog_delete') . '</button>',
]], false);
$buttons = $fragment->parse('core/form/submit.php');

$fragment = new Fragment();
$fragment->setVar('title', I18n::msg('ycom_user_log_title', $logFile), false);
$fragment->setVar('content', $content, false);
$fragment->setVar('buttons', $buttons, false);

echo '
    <form action="' . Url::currentBackendPage() . '" method="post">
        <input type="hidden" name="func" value="delete_log" />
        ' . $fragment->parse('core/page/section.php') . '
    </form>';
