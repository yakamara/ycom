<?php

/**
 * The ycom settings: which article does what, the login field, session and brute-force rules.
 *
 * @var Yakamara\YCom\YComAddon $this
 */

use Redaxo\Core\Filesystem\Url;
use Redaxo\Core\Form\Select\Select;
use Redaxo\Core\Http\Request;
use Redaxo\Core\RexVar\LinkVar;
use Redaxo\Core\View\Fragment;
use Redaxo\Core\View\Message;
use Yakamara\YCom\Auth\Auth;
use Yakamara\YCom\Auth\AuthRules;
use Yakamara\YCom\Config;

use function Redaxo\Core\View\escape;

/** The article pickers of the settings form; the widget id has to be unique per page. */
$articleFields = [
    // fieldset => [config key => [widget id, label key, notice key]]
    'ycom_auth_config_forwarder' => [
        'article_id_jump_ok' => [5, 'ycom_auth_config_id_jump_ok', null],
        'article_id_jump_logout' => [7, 'ycom_auth_config_id_jump_logout', null],
        'article_id_jump_denied' => [8, 'ycom_auth_config_id_jump_denied', 'ycom_auth_config_id_jump_denied_notice'],
    ],
    'ycom_auth_config_pages' => [
        'article_id_login' => [11, 'ycom_auth_config_id_login', null],
        'article_id_logout' => [12, 'ycom_auth_config_id_logout', null],
        'article_id_register' => [13, 'ycom_auth_config_id_register', null],
        'article_id_password' => [14, 'ycom_auth_config_id_password', null],
    ],
    'ycom_auth_config_extern' => [
        'article_id_jump_not_ok' => [15, 'ycom_auth_config_id_jump_not_ok', null],
    ],
];

if ('update' === Request::request('func', 'string', '')) {
    foreach ($articleFields as $fields) {
        foreach ($fields as $key => $_) {
            $this->setConfig($key, Request::request($key, 'int', 0));
        }
    }

    $this->setConfig('auth_rule', Request::request('auth_rule', 'string', AuthRules::DEFAULT_RULE));
    $this->setConfig('auth_cookie_ttl', Request::request('auth_cookie_ttl', 'int', 7));
    $this->setConfig('login_field', Request::request('login_field', 'string', 'email'));
    $this->setConfig('session_duration', Request::request('session_duration', 'int', 3600));
    $this->setConfig('session_max_overall_duration', Request::request('session_max_overall_duration', 'int', 21600));

    foreach (Auth::getInjections() as $injection) {
        $injection->triggerSaveSettings();
    }

    // The cached configuration still holds the values from before this save.
    Config::reset();

    echo Message::success($this->i18n('ycom_auth_settings_updated'));
}

$selectLoginField = new Select();
$selectLoginField->setName('login_field');
$selectLoginField->setId('ycom-login-field');
$selectLoginField->setAttribute('class', 'form-control selectpicker');
$selectLoginField->setSize(1);
foreach (['id', 'email', 'login'] as $field) {
    $selectLoginField->addOption($field, $field);
}
$selectLoginField->setSelected((string) $this->getConfig('login_field'));

$selectAuthRules = new Select();
$selectAuthRules->setId('ycom-auth-rule');
$selectAuthRules->setName('auth_rule');
$selectAuthRules->setAttribute('class', 'form-control selectpicker');
$selectAuthRules->addOptions(new AuthRules()->getOptions());
$selectAuthRules->setSelected((string) $this->getConfig('auth_rule'));

$selectCookieTtl = new Select();
$selectCookieTtl->setId('ycom-auth-cookie-ttl');
$selectCookieTtl->setName('auth_cookie_ttl');
$selectCookieTtl->setAttribute('class', 'form-control selectpicker');
$selectCookieTtl->addOption($this->i18n('ycom_days', 0) . ' (' . $this->i18n('inactive') . ')', 0);
foreach ([7, 14, 30, 90, 180, 360] as $days) {
    $selectCookieTtl->addOption($this->i18n('ycom_days', $days), $days);
}
$selectCookieTtl->setSelected((int) $this->getConfig('auth_cookie_ttl'));

/** Renders one label/field row. */
$row = static function (string $label, string $field, ?string $notice = null, bool $last = false): string {
    return '
    <div class="row' . ($last ? '' : ' abstand') . '">
        <div class="col-xs-12 col-sm-6">' . $label . '</div>
        <div class="col-xs-12 col-sm-6">' . $field . ($notice ?? '') . '</div>
    </div>';
};

$content = '
<form action="' . Url::currentBackendPage() . '" method="post" id="ycom-settings">
    <input type="hidden" name="func" value="update" />';

foreach ($articleFields as $legend => $fields) {
    $content .= '<fieldset><legend>' . $this->i18n($legend) . '</legend>';

    if ('ycom_auth_config_forwarder' === $legend) {
        $content .= '<div class="row abstand"><div class="col-xs-12"><p class="help-block">'
            . $this->i18n('ycom_auth_howtoread_configvalues') . '</p></div></div>';
    }

    $keys = array_keys($fields);
    foreach ($fields as $key => [$widgetId, $labelKey, $noticeKey]) {
        $notice = '<small>' . (null === $noticeKey ? '' : $this->i18n($noticeKey) . '<br />') . '[' . $key . ']</small>';

        $content .= $row(
            '<label for="REX_LINK_' . $widgetId . '_NAME">' . $this->i18n($labelKey) . '</label>',
            LinkVar::getWidget($widgetId, $key, (int) $this->getConfig($key) ?: null),
            $notice,
            $key === end($keys),
        );
    }

    $content .= '</fieldset>';
}

$content .= '
    <fieldset>
        <legend>' . $this->i18n('ycom_auth_config_login_field') . '</legend>'
    . $row(
        '<label for="ycom-login-field">' . $this->i18n('ycom_auth_config_login_field') . '</label>',
        $selectLoginField->get(),
        '<small>[login_field]</small>',
        true,
    ) . '
    </fieldset>

    <fieldset>
        <legend>' . $this->i18n('ycom_auth_config_security') . '</legend>'
    . $row(
        '<label for="ycom-auth-rule">' . $this->i18n('ycom_auth_config_auth_rules') . '</label>',
        $selectAuthRules->get(),
        '<small>[auth_rule]</small>',
    )
    . $row(
        '<label for="ycom-auth-cookie-ttl">' . $this->i18n('ycom_auth_config_auth_cookie_ttl') . '</label>',
        $selectCookieTtl->get(),
        '<small>' . $this->i18n('ycom_auth_config_auth_cookie_ttl_notice') . '<br />[auth_cookie_ttl]</small>',
    )
    . $row(
        '<label for="ycom-session-max">' . $this->i18n('ycom_auth_config_session_max_overall_duration') . '</label>',
        '<input class="form-control" type="number" min="0" id="ycom-session-max" name="session_max_overall_duration" value="'
            . escape((string) $this->getConfig('session_max_overall_duration', 21600)) . '" />',
        '<small>[session_max_overall_duration]</small>',
    )
    . $row(
        '<label for="ycom-session-duration">' . $this->i18n('ycom_auth_config_session_duration') . '</label>',
        '<input class="form-control" type="number" min="0" id="ycom-session-duration" name="session_duration" value="'
            . escape((string) $this->getConfig('session_duration', 3600)) . '" />',
        '<small>[session_duration]</small>',
        true,
    ) . '
    </fieldset>';

// Every injection contributes its own fieldset — OTP, forced password change, terms of use.
foreach (Auth::getInjections() as $injection) {
    $content .= $injection->getSettingsContent();
}

$content .= '
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-sm-push-6">
            <button class="btn btn-save right" type="submit" name="config-submit" value="1">'
                . $this->i18n('ycom_auth_config_save') . '</button>
        </div>
    </div>
</form>';

$fragment = new Fragment();
$fragment->setVar('class', 'edit');
$fragment->setVar('title', $this->i18n('ycom_auth_settings'));
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');
