<?php

class rex_ycom_injection_otp extends rex_ycom_injection_abtract
{
    public function getRewrite(): bool|string
    {
        $user = rex_ycom_auth::getUser();

        if (!$user) {
            return false;
        }

        $otp_article_id = (int) rex_addon::get('ycom')->getConfig('otp_article_id');

        // 1. User ist eingeloggt
        // 2. OTP Article vorhanden => OTP aktiviert ?
        if (0 == $otp_article_id) {
            return false;
        }

        // 1. User ist eingeloggt
        // 2. Keine OTP Konfiguration/Article vorhanden
        // 3. Session OTP Check bereits durchgeführt?
        $SessionInstance = rex_ycom_user_session::getInstance()->getCurrentSession($user);
        if (1 == $SessionInstance['otp_verified']) {
            return false;
        }

        // - user hat überprüfung und keine OTP Session -> zwingend auf otp-article
        // - OTP ist erzwungen.
        $config = rex_ycom_otp_password_config::forCurrentUser();
        $otp_auth_enforce = rex_addon::get('ycom')->getConfig('otp_auth_enforce');
        $enforcedAll = rex_ycom_otp_password::ENFORCED_ALL == $otp_auth_enforce ? true : false;
        if (!($enforcedAll || $config->enabled)) {
            return false;
        }

        if (rex_article::getCurrentId() == $otp_article_id) {
            return true;
        }

        return rex_getUrl($otp_article_id, '', [], '&');
    }

    public function getSettingsContent(): string
    {
        $addon = rex_addon::get('ycom');

        $selectEnforce = new rex_select();
        $selectEnforce->setId('otp_auth_enforce');
        $selectEnforce->setName('otp_auth_enforce');
        $selectEnforce->setAttribute('class', 'form-control selectpicker');
        $selectEnforce->setSelected($addon->getConfig('otp_auth_enforce'));

        $selectEnforce->addOption($addon->i18n('otp_auth_enforce_' . rex_ycom_otp_password::ENFORCED_ALL), rex_ycom_otp_password::ENFORCED_ALL);
        $selectEnforce->addOption($addon->i18n('otp_auth_enforce_' . rex_ycom_otp_password::ENFORCED_DISABLED), rex_ycom_otp_password::ENFORCED_DISABLED);

        $selectOption = new rex_select();
        $selectOption->setId('otp_auth_option');
        $selectOption->setName('otp_auth_option');
        $selectOption->setAttribute('class', 'form-control selectpicker');
        $selectOption->setSelected($addon->getConfig('otp_auth_option'));

        $selectOption->addOption($addon->i18n('otp_auth_option_' . rex_ycom_otp_password::OPTION_ALL), rex_ycom_otp_password::OPTION_ALL);
        $selectOption->addOption($addon->i18n('otp_auth_option_' . rex_ycom_otp_password::OPTION_TOTP), rex_ycom_otp_password::OPTION_TOTP);
        $selectOption->addOption($addon->i18n('otp_auth_option_' . rex_ycom_otp_password::OPTION_EMAIL), rex_ycom_otp_password::OPTION_EMAIL);

        $selectEmailPeriod = new rex_select();
        $selectEmailPeriod->setId('otp_auth_email_period');
        $selectEmailPeriod->setName('otp_auth_email_period');
        $selectEmailPeriod->setAttribute('class', 'form-control selectpicker');
        $selectEmailPeriod->setSelected($addon->getConfig('otp_auth_email_period'));

        $selectEmailPeriod->addOption('5 ' . $addon->i18n('minutes'), 300);
        $selectEmailPeriod->addOption('10 ' . $addon->i18n('minutes'), 600);
        $selectEmailPeriod->addOption('15 ' . $addon->i18n('minutes'), 900);
        $selectEmailPeriod->addOption('30 ' . $addon->i18n('minutes'), 1800);

        $selectTOTPPeriod = new rex_select();
        $selectTOTPPeriod->setAttribute('class', 'form-control selectpicker');
        $selectTOTPPeriod->setDisabled(true);
        $selectTOTPPeriod->addOption($addon->i18n('otp_auth_totp_period_info', rex_ycom_otp_method_totp::getPeriod()), 30);

        $selectLoginTries = new rex_select();
        $selectLoginTries->setAttribute('class', 'form-control selectpicker');
        $selectLoginTries->setDisabled(true);
        $selectLoginTries->addOption($addon->i18n('otp_auth_logintries_info', rex_ycom_otp_method_totp::getloginTries()), 30);

        return '

            <fieldset>

            <legend>' . $addon->i18n('otp_auth_config') . '</legend>

            <div class="row abstand">
                <div class="col-xs-12 col-sm-6">
                    <label for="rex-form-otp_article_id">' . $addon->i18n('otp_article_id') . '</label>
                </div>
                <div class="col-xs-12 col-sm-6">
                    ' . rex_var_link::getWidget(17, 'otp_article_id', (int) $addon->getConfig('otp_article_id')) . '
                    <small>[otp_article_id]</small>
                </div>
            </div>

            <div class="row abstand">
                <div class="col-xs-12 col-sm-6">
                    <label for="rex_ycom_otp_auth_enforce">' . $addon->i18n('otp_auth_enforce') . '</label>
                </div>
                <div class="col-xs-12 col-sm-6">
                ' . $selectEnforce->get() . '
                <small>[otp_auth_enforce]</small>
                </div>
            </div>

            <div class="row abstand">
                <div class="col-xs-12 col-sm-6">
                    <label for="2factor_auth_options">' . $addon->i18n('otp_auth_options') . '</label>
                </div>
                <div class="col-xs-12 col-sm-6">
                ' . $selectOption->get() . '
                <small>[otp_auth_option]</small>
                </div>
            </div>

            <div class="row abstand">
                <div class="col-xs-12 col-sm-6">
                    <label for="2factor_auth_email_period">' . $addon->i18n('otp_auth_email_period') . '</label>
                </div>
                <div class="col-xs-12 col-sm-6">
                ' . $selectEmailPeriod->get() . '
                <small>[otp_auth_email_period]</small>
                </div>
            </div>

            <div class="row abstand">
                <div class="col-xs-12 col-sm-6">
                    <label for="2factor_auth_email_period">' . $addon->i18n('otp_auth_totp_period') . '</label>
                </div>
                <div class="col-xs-12 col-sm-6">
                ' . $selectTOTPPeriod->get() . '
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <label for="2factor_auth_email_period">' . $addon->i18n('otp_auth_logintries') . '</label>
                </div>
                <div class="col-xs-12 col-sm-6">
                ' . $selectLoginTries->get() . '
                </div>
            </div>

    </fieldset>';
    }

    public function triggerSaveSettings(): void
    {
        $addon = rex_addon::get('ycom');
        $addon->setConfig('otp_article_id', rex_request('otp_article_id', 'int'));
        $addon->setConfig('otp_auth_enforce', rex_request('otp_auth_enforce', 'string'));
        $addon->setConfig('otp_auth_option', rex_request('otp_auth_option', 'string'));
        $addon->setConfig('otp_auth_email_period', rex_request('otp_auth_email_period', 'int', 300));
    }
}
