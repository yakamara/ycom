<?php

/** @var rex_yform_value_abstract $this */
/** @var rex_ycom_user $user */
/** @var array|null $SessionInstance */
/** @var rex_ycom_otp_password_config $config */
/** @var int $otp_article_id */

$addon = rex_plugin::get('ycom', 'auth');

$otp = rex_ycom_otp_password::getInstance();
$otp_options = $otp->getAuthOption();

$func = rex_request('otp-func', 'string');
$myOTP = rex_post('otp', 'string', null);

$otpOptions = [];

switch ($otp_options) {
    case rex_ycom_otp_password::OPTION_ALL:
        $otpOptions[] = 'email';
        $otpOptions[] = 'totp';
        $defaultOption = 'email';
        break;
    case rex_ycom_otp_password::OPTION_EMAIL:
        $defaultOption = 'email';
        $otpOptions[] = $defaultOption;
        break;
    case rex_ycom_otp_password::OPTION_TOTP:
        $defaultOption = 'totp';
        $otpOptions[] = $defaultOption;
        break;
}

// 1. Setup neu durchlaufen wenn man schon verified ist
// 1.1. Authcode um OTP zu deaktivieren -> Schritt 2

if ($otp->isEnabled() && $config->enabled) {
    // TODO: AuthCode zum deaktivieren abfragen

    if ('disable' == $func) {
        $OTPInstance = rex_ycom_otp_password::getInstance();
        $OTPMethod = $OTPInstance->getMethod();
        $config = rex_ycom_otp_password_config::loadFromDb($OTPMethod, $user);
        $config->disable();
        $func = '';

        $this->params['warning'][$this->getId()] = $this->params['error_class'];
        $this->params['warning_messages'][$this->getId()] = '{ ycom_otp_diabled }';
    } else {
        echo '
                <div class="form-check">
                    <input class="form-check" type="radio" name="otp-func" id="otp-func" value="disable" checked="checked" />
                    <label class="form-check" for="otp-func-">{ ycom_otp_disable_info }</label>
                </div>';
    }

    return;
}

// 2. Setup starten
// 2.1 jeweile Methode auswählen
// 2.2 -> Codeseite /email oder totp

if (in_array($func, $otpOptions)) {
    switch ($func) {
        case 'email':
            $defaultOption = 'email';
            $otpMethod = new rex_ycom_otp_method_email();

            if (null === $myOTP || 'resend' == rex_request('otp-func-email', 'string')) {
                $this->params['warning'][$this->getId()] = $this->params['error_class'];
                $this->params['warning_messages'][$this->getId()] = '{ ycom_otp_email_check }';
                rex_ycom_otp_password::getInstance()->challenge();
            }

            break;
        case 'totp':
            $defaultOption = 'totp';
            $otpMethod = new rex_ycom_otp_method_totp();
            break;
    }

    // initial starten wenn beim user nicht vorhanden oder noch nicht enabled.
    if (null === $myOTP) {
        $passwordConfig = rex_ycom_otp_password_config::loadFromDb($otpMethod, $user);
        $passwordConfig->updateMethod($otpMethod);
        $this->params['warning'][$this->getId()] = $this->params['error_class'];
    } else {
        if ($otp->verify($myOTP)) {
            $config = rex_ycom_otp_password_config::loadFromDb($otpMethod, $user);
            $config->enable();

            $user->resetOTPTries()->save();
            rex_ycom_user_session::getInstance()->setOTPverified($user);
            $article_jump_ok = (int) rex_plugin::get('ycom', 'auth')->getConfig('article_id_jump_ok');
            rex_response::sendRedirect(rex_getUrl($article_jump_ok, rex_clang::getCurrentId()));
        } else {
            $this->params['warning'][$this->getId()] = $this->params['error_class'];
            $this->params['warning_messages'][$this->getId()] = '{ ycom_otp_code_error }';
        }
    }

    if ('totp' == $func) {
        $config = rex_ycom_otp_password_config::loadFromDb($otpMethod, $user);
        $uri = $config->provisioningUri;

        ?>
        <div class="row">
            <div class="col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">{ ycom_otp_setup_scan }</h3>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <canvas id="ycom-auth-otp-qr-code"></canvas>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <label for="ycom-auth-otp-uri">{ ycom_otp_setup_uri }</label>
                                <input type="text" class="form-control" value="<?= $uri ?>" id="ycom-auth-otp-uri" readonly />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="<?= $addon->getAssetsUrl('qrious.min.js') ?>" nonce="<?= rex_response::getNonce() ?>"></script>
        <script nonce="<?= rex_response::getNonce() ?>">
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
                        <h3 class="panel-title">{ ycom_otp_setup_title }</h3>
                    </div>
                    <div class="panel-body">
                        <input type="hidden" name="func" value="verify-totp" />
                        <div class="form-group">
                            <div class="input-group">
                                <label for="otp-setup-code">{ ycom_otp_setup_code }</label>
                                <input type="text" class="form-control" name="otp" id="otp-setup-code" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php

    if ('email' == $func) {
        ?>
    <div class="row">
    <div class="col-lg-6">
        <div class="form-check">
            <input class="form-check" type="checkbox" name="otp-func-email" id="otp-func-email-resend" value="resend" />
            <label class="form-check" for="otp-func-email-resend">{ ycom_otp_email_resend_info }</label>
        </div>
    </div>
    </div><?php

    }
}

foreach ($otpOptions as $option) {
    echo '
    <div class="form-check">
        <input class="form-check" type="radio" name="otp-func" id="otp-func-' . $option . '" value="' . $option . '" ' . ($defaultOption == $option ? 'checked="checked"' : '') . ' />
        <label class="form-check" for="otp-func-' . $option . '">{ ycom_otp_' . $option . '_info }</label>
    </div>';
}
