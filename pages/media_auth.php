<?php

/**
 * Media pool protection: on/off plus what happens when access is denied.
 *
 * @var Yakamara\YCom\YComAddon $this
 */

use Redaxo\Core\Filesystem\Url;
use Redaxo\Core\Form\Select\Select;
use Redaxo\Core\Http\Request;
use Redaxo\Core\View\Fragment;
use Redaxo\Core\View\Message;
use Yakamara\YCom\Config;
use Yakamara\YCom\MediaAuth\MediaAuthRules;


if ('update' === Request::request('func', 'string', '')) {
    $this->setConfig('media_auth', Request::request('media_auth', 'int', 0));
    $this->setConfig('media_auth_rule', Request::request('media_auth_rule', 'string', MediaAuthRules::DEFAULT_RULE));

    Config::reset();

    echo Message::success($this->i18n('ycom_media_auth_settings_updated'));
}

$selectActive = new Select();
$selectActive->setId('ycom-media-auth');
$selectActive->setName('media_auth');
$selectActive->setAttribute('class', 'form-control selectpicker');
$selectActive->addOption($this->i18n('inactive'), 0);
$selectActive->addOption($this->i18n('ycom_media_auth_active'), 1);
$selectActive->setSelected((int) $this->getConfig('media_auth'));

$selectRules = new Select();
$selectRules->setId('ycom-media-auth-rule');
$selectRules->setName('media_auth_rule');
$selectRules->setAttribute('class', 'form-control selectpicker');
$selectRules->addOptions(new MediaAuthRules()->getOptions());
$selectRules->setSelected((string) $this->getConfig('media_auth_rule'));

$content = '
<form action="' . Url::currentBackendPage() . '" method="post" id="ycom-media-auth-settings">
    <input type="hidden" name="func" value="update" />

    <fieldset>
        <div class="row abstand">
            <div class="col-xs-12 col-sm-4">
                <label for="ycom-media-auth">' . $this->i18n('ycom_media_auth_activate') . '</label>
            </div>
            <div class="col-xs-12 col-sm-8">
                ' . $selectActive->get() . '
                <small>' . $this->i18n('ycom_media_auth_activate_notice') . '<br />[media_auth]</small>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-4">
                <label for="ycom-media-auth-rule">' . $this->i18n('ycom_auth_config_media_auth_rules') . '</label>
            </div>
            <div class="col-xs-12 col-sm-8">
                ' . $selectRules->get() . '
                <small>[media_auth_rule]</small>
            </div>
        </div>
    </fieldset>

    <div class="row">
        <div class="col-xs-12 col-sm-8 col-sm-push-4">
            <button class="btn btn-save right" type="submit" name="config-submit" value="1">'
                . $this->i18n('ycom_auth_config_save') . '</button>
        </div>
    </div>
</form>';

$fragment = new Fragment();
$fragment->setVar('class', 'edit');
$fragment->setVar('title', $this->i18n('ycom_media_auth'));
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');
