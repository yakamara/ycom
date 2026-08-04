<?php

/**
 * The OTP setup step of the `ycom_auth_otp` field.
 *
 * Three states:
 *  - OTP is already active for this user -> offer to switch it off
 *  - a method was picked                 -> show the secret (TOTP) or send a code (email) and ask
 *                                           for a confirmation code
 *  - nothing picked yet                  -> show the available methods
 *
 * @var \Yakamara\YForm\View\Fragment $this
 * @var \Yakamara\YForm\Value\AbstractValue $yfield
 * @var \Yakamara\YCom\User $user
 * @var array<string, mixed>|null $session
 * @var \Yakamara\YCom\Auth\Otp\PasswordConfig $config
 * @var int $otp_article_id
 */

use Redaxo\Core\Addon\Addon;
use Redaxo\Core\Filesystem\Url;
use Redaxo\Core\Http\Request;
use Redaxo\Core\Http\Response;
use Redaxo\Core\Language\Language;
use Redaxo\Core\Translation\I18n;
use Yakamara\YCom\Auth\Otp\EmailMethod;
use Yakamara\YCom\Auth\Otp\Password;
use Yakamara\YCom\Auth\Otp\PasswordConfig;
use Yakamara\YCom\Auth\Otp\TotpMethod;
use Yakamara\YCom\Auth\UserSession;
use Yakamara\YCom\Config;

use function Redaxo\Core\View\escape;

extract($this->getVariables());

$addon = Addon::require('ycom');
$otp = Password::getInstance();

$func = Request::request('otp-func', 'string', '');
$myOtp = Request::post('otp', 'string', null);

$otpOptions = match ($otp->getAuthOption()) {
    Password::OPTION_EMAIL => ['email'],
    Password::OPTION_TOTP => ['totp'],
    default => ['email', 'totp'],
};
$defaultOption = $otpOptions[0];

// ----- 1. already active: the only thing left to offer is switching it off
if ($otp->isEnabled() && $config->enabled) {
    if ('disable' === $func) {
        PasswordConfig::loadFromDb($otp->getMethod(), $user)
            ->disable()
            ->save();

        $yfield->params['warning'][$yfield->getId()] = $yfield->params['error_class'];
        $yfield->params['warning_messages'][$yfield->getId()] = I18n::msg('ycom_otp_disabled');
    } else {
        echo '
        <div class="form-check">
            <input class="form-check" type="radio" name="otp-func" id="otp-func-disable" value="disable" checked="checked" />
            <label class="form-check" for="otp-func-disable">' . I18n::msg('ycom_otp_disable_info') . '</label>
        </div>';
    }

    return;
}

// ----- 2. a method was picked: hand out the secret and ask for a confirmation code
if (in_array($func, $otpOptions, true)) {
    $otpMethod = 'email' === $func ? new EmailMethod() : new TotpMethod();
    $defaultOption = $func;

    if (null === $myOtp) {
        // Entering the setup creates a fresh secret, so an abandoned setup cannot be resumed
        // with a secret somebody else may have seen.
        PasswordConfig::loadFromDb($otpMethod, $user)
            ->updateMethod($otpMethod)
            ->save();
        $user->loadData();

        $yfield->params['warning'][$yfield->getId()] = $yfield->params['error_class'];
    }

    if ('email' === $func && (null === $myOtp || 'resend' === Request::request('otp-func-email', 'string', ''))) {
        $yfield->params['warning'][$yfield->getId()] = $yfield->params['error_class'];
        $yfield->params['warning_messages'][$yfield->getId()] = I18n::msg('ycom_otp_email_check');
        $otp->challenge();
    }

    if ('' !== (string) $myOtp) {
        if ($otp->verify((string) $myOtp)) {
            PasswordConfig::loadFromDb($otpMethod, $user)
                ->enable()
                ->save();
            $user->loadData();
            $user->resetOTPTries()->save();
            UserSession::getInstance()->setOTPverified($user);

            Response::sendRedirect(Url::article((int) Config::get('article_id_jump_ok'), Language::getCurrentId()));
        }

        $yfield->params['warning'][$yfield->getId()] = $yfield->params['error_class'];
        $yfield->params['warning_messages'][$yfield->getId()] = I18n::msg('ycom_otp_code_error');
    }

    if ('totp' === $func) {
        $uri = (string) PasswordConfig::loadFromDb($otpMethod, $user)->getProvisioningUri();
        ?>
        <div class="row">
            <div class="col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= I18n::msg('ycom_otp_setup_scan') ?></h3>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <canvas id="ycom-auth-otp-qr-code"></canvas>
                        </div>
                        <div class="form-group">
                            <label for="ycom-auth-otp-uri"><?= I18n::msg('ycom_otp_setup_uri') ?></label>
                            <input type="text" class="form-control" value="<?= escape($uri) ?>" id="ycom-auth-otp-uri" readonly />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="<?= $addon->getAssetsUrl('qrious.min.js') ?>" nonce="<?= Response::getNonce() ?>"></script>
        <script nonce="<?= Response::getNonce() ?>">
            new QRious({
                element: document.getElementById("ycom-auth-otp-qr-code"),
                value: document.getElementById("ycom-auth-otp-uri").value,
                size: 300
            });
        </script>
        <?php
    }
    ?>
        <div class="row">
            <div class="col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= I18n::msg('ycom_otp_setup_title') ?></h3>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="otp-setup-code"><?= I18n::msg('ycom_otp_setup_code') ?></label>
                            <input type="text" class="form-control" name="otp" id="otp-setup-code"
                                   inputmode="numeric" autocomplete="one-time-code" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php

    if ('email' === $func) {
        ?>
        <div class="row">
            <div class="col-lg-6">
                <div class="form-check">
                    <input class="form-check" type="checkbox" name="otp-func-email" id="otp-func-email-resend" value="resend" />
                    <label class="form-check" for="otp-func-email-resend"><?= I18n::msg('ycom_otp_email_resend_info') ?></label>
                </div>
            </div>
        </div>
        <?php
    }
}

// ----- 3. the method choice, always shown
foreach ($otpOptions as $option) {
    echo '
    <div class="form-check">
        <input class="form-check" type="radio" name="otp-func" id="otp-func-' . escape($option) . '"'
        . ' value="' . escape($option) . '"' . ($defaultOption === $option ? ' checked="checked"' : '') . ' />
        <label class="form-check" for="otp-func-' . escape($option) . '">'
        . I18n::msg('ycom_otp_' . $option . '_info') . '</label>
    </div>';
}
