<?php

/** @var rex_yform_value_abstract $this */
/** @var rex_ycom_user $user */
/** @var array|null $SessionInstance */
/** @var rex_ycom_otp_password_config $config */
/** @var int $otp_article_id */

$addon = rex_plugin::get('ycom', 'auth');

$OTPInstance = rex_ycom_otp_password::getInstance();
$OTPMethod = $OTPInstance->getMethod();
$blockTime = (int) ($OTPMethod::getPeriod() / 10);
$loginTriesAllowed = $OTPMethod::getloginTries();
$loginTries = (int) $user->getValue('otp_tries');
$loginLastTry = (int) $user->getValue('otp_last_try_time');

$myOTP = rex_post('otp', 'string', null);

if (null !== $myOTP) {
    if ($loginTries >= $loginTriesAllowed && ($loginLastTry > time() - $blockTime)) {
        $this->params['warning'][$this->getId()] = $this->params['error_class'];
        $this->params['warning_messages'][$this->getId()] = '{{ ycom_otp_code_error_blocked }}';
        $countdownTime = $loginLastTry - time() + $blockTime;

        echo '<script nonce="' . rex_response::getNonce() . '">

    let countdown = ' . $countdownTime . ';
    let countdownElement = document.getElementById("otp_countdown");

    let interval = setInterval(() => {
       countdown--;
       countdownElement.innerHTML = countdown;
       if (countdown <= 0) {
           clearInterval(interval);
       }
    }, 1000);

</script>';
    } else {
        if ($OTPInstance->verify($myOTP)) {
            $user->resetOTPTries()->save();
            rex_ycom_user_session::getInstance()->setOTPVerified($user);
            $article_jump_ok = (int) rex_plugin::get('ycom', 'auth')->getConfig('article_id_jump_ok');
            rex_response::sendRedirect(rex_getUrl($article_jump_ok, rex_clang::getCurrentId()));
        } else {
            $user->increaseOTPTries()->save();
            $this->params['warning'][$this->getId()] = $this->params['error_class'];
            $this->params['warning_messages'][$this->getId()] = '{ ycom_otp_code_error }';
        }
    }
} else {
    if ('rex_ycom_otp_method_email' === $OTPMethod::class) {
        rex_ycom_otp_password::getInstance()->challenge();
    }
}

?>

    <div class="row">
        <div class="col-lg-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">{ ycom_otp_verify_title }</h3>
                </div>
                <div class="panel-body">
                    <input type="hidden" name="func" value="verify-totp" />
                    <div class="form-group">
                        <div class="input-group">
                            <label for="otp_verify_code">{ ycom_otp_verify_code }</label>
                            <input type="text" class="form-control" name="otp" id="otp_verify_code" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
