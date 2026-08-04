<?php

namespace Yakamara\YCom\Auth\Injection;

use Override;
use Redaxo\Core\Addon\Addon;
use Redaxo\Core\Content\Article;
use Redaxo\Core\Filesystem\Url;
use Redaxo\Core\Form\Select\Select;
use Redaxo\Core\Http\Request;
use Redaxo\Core\RexVar\LinkVar;
use Yakamara\YCom\Auth\Auth;
use Yakamara\YCom\Auth\Otp\Password;
use Yakamara\YCom\Auth\Otp\PasswordConfig;
use Yakamara\YCom\Auth\Otp\TotpMethod;
use Yakamara\YCom\Auth\UserSession;
use Yakamara\YCom\Config;


/**
 * Forces a logged-in user onto the OTP article until this session has passed the second factor.
 *
 * Active only once an OTP article is configured. Whether an individual user has to pass it depends
 * on their own OTP config plus the `otp_auth_enforce` setting.
 */
final class Otp extends AbstractInjection
{
    #[Override]
    public function getRewrite(): bool|string
    {
        $user = Auth::getUser();

        if (null === $user) {
            return false;
        }

        // No OTP article configured -> the whole feature is off.
        $otpArticleId = (int) Config::get('otp_article_id');
        if (0 === $otpArticleId) {
            return false;
        }

        // This session already passed the second factor.
        $session = UserSession::getInstance()->getCurrentSession($user);
        if (null !== $session && 1 === (int) $session['otp_verified']) {
            return false;
        }

        $config = PasswordConfig::forUser($user);
        $enforcedAll = Password::ENFORCED_ALL === Config::get('otp_auth_enforce');

        if (!$enforcedAll && !$config->enabled) {
            return false;
        }

        if ($otpArticleId === Article::getCurrentId()) {
            return true;
        }

        return Url::article($otpArticleId);
    }

    #[Override]
    public function getSettingsContent(): string
    {
        $addon = Addon::require('ycom');

        $selectEnforce = new Select();
        $selectEnforce->setId('otp_auth_enforce');
        $selectEnforce->setName('otp_auth_enforce');
        $selectEnforce->setAttribute('class', 'form-control selectpicker');
        $selectEnforce->setSelected((string) $addon->getConfig('otp_auth_enforce'));
        $selectEnforce->addOption($addon->i18n('otp_auth_enforce_' . Password::ENFORCED_ALL), Password::ENFORCED_ALL);
        $selectEnforce->addOption($addon->i18n('otp_auth_enforce_' . Password::ENFORCED_DISABLED), Password::ENFORCED_DISABLED);

        $selectOption = new Select();
        $selectOption->setId('otp_auth_option');
        $selectOption->setName('otp_auth_option');
        $selectOption->setAttribute('class', 'form-control selectpicker');
        $selectOption->setSelected((string) $addon->getConfig('otp_auth_option'));
        $selectOption->addOption($addon->i18n('otp_auth_option_' . Password::OPTION_ALL), Password::OPTION_ALL);
        $selectOption->addOption($addon->i18n('otp_auth_option_' . Password::OPTION_TOTP), Password::OPTION_TOTP);
        $selectOption->addOption($addon->i18n('otp_auth_option_' . Password::OPTION_EMAIL), Password::OPTION_EMAIL);

        $selectEmailPeriod = new Select();
        $selectEmailPeriod->setId('otp_auth_email_period');
        $selectEmailPeriod->setName('otp_auth_email_period');
        $selectEmailPeriod->setAttribute('class', 'form-control selectpicker');
        $selectEmailPeriod->setSelected((int) $addon->getConfig('otp_auth_email_period'));
        foreach ([300 => 5, 600 => 10, 900 => 15, 1800 => 30] as $seconds => $minutes) {
            $selectEmailPeriod->addOption($minutes . ' ' . $addon->i18n('minutes'), $seconds);
        }

        // Informational only — both values are fixed by the TOTP standard.
        $selectTotpPeriod = new Select();
        $selectTotpPeriod->setAttribute('class', 'form-control selectpicker');
        $selectTotpPeriod->setDisabled();
        $selectTotpPeriod->addOption($addon->i18n('otp_auth_totp_period_info', TotpMethod::getPeriod()), 30);

        $selectLoginTries = new Select();
        $selectLoginTries->setAttribute('class', 'form-control selectpicker');
        $selectLoginTries->setDisabled();
        $selectLoginTries->addOption($addon->i18n('otp_auth_logintries_info', TotpMethod::getLoginTries()), 30);

        return '
        <fieldset>
            <legend>' . $addon->i18n('otp_auth_config') . '</legend>

            <div class="row abstand">
                <div class="col-xs-12 col-sm-6">
                    <label for="rex-form-otp_article_id">' . $addon->i18n('otp_article_id') . '</label>
                </div>
                <div class="col-xs-12 col-sm-6">
                    ' . LinkVar::getWidget(17, 'otp_article_id', (int) $addon->getConfig('otp_article_id')) . '
                    <small>[otp_article_id]</small>
                </div>
            </div>

            <div class="row abstand">
                <div class="col-xs-12 col-sm-6">
                    <label for="otp_auth_enforce">' . $addon->i18n('otp_auth_enforce') . '</label>
                </div>
                <div class="col-xs-12 col-sm-6">
                    ' . $selectEnforce->get() . '
                    <small>[otp_auth_enforce]</small>
                </div>
            </div>

            <div class="row abstand">
                <div class="col-xs-12 col-sm-6">
                    <label for="otp_auth_option">' . $addon->i18n('otp_auth_options') . '</label>
                </div>
                <div class="col-xs-12 col-sm-6">
                    ' . $selectOption->get() . '
                    <small>[otp_auth_option]</small>
                </div>
            </div>

            <div class="row abstand">
                <div class="col-xs-12 col-sm-6">
                    <label for="otp_auth_email_period">' . $addon->i18n('otp_auth_email_period') . '</label>
                </div>
                <div class="col-xs-12 col-sm-6">
                    ' . $selectEmailPeriod->get() . '
                    <small>[otp_auth_email_period]</small>
                </div>
            </div>

            <div class="row abstand">
                <div class="col-xs-12 col-sm-6">
                    <label>' . $addon->i18n('otp_auth_totp_period') . '</label>
                </div>
                <div class="col-xs-12 col-sm-6">
                    ' . $selectTotpPeriod->get() . '
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <label>' . $addon->i18n('otp_auth_logintries') . '</label>
                </div>
                <div class="col-xs-12 col-sm-6">
                    ' . $selectLoginTries->get() . '
                </div>
            </div>
        </fieldset>';
    }

    #[Override]
    public function triggerSaveSettings(): void
    {
        $addon = Addon::require('ycom');
        $addon->setConfig('otp_article_id', Request::request('otp_article_id', 'int', 0));
        $addon->setConfig('otp_auth_enforce', Request::request('otp_auth_enforce', 'string', Password::ENFORCED_DISABLED));
        $addon->setConfig('otp_auth_option', Request::request('otp_auth_option', 'string', Password::OPTION_ALL));
        $addon->setConfig('otp_auth_email_period', Request::request('otp_auth_email_period', 'int', 300));
    }
}
