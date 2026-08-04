<?php

/**
 * The OTP verification step of the `ycom_auth_otp` field: a session that has not passed the second
 * factor yet asks for the current code.
 *
 * @var \Yakamara\YForm\View\Fragment $this
 * @var \Yakamara\YForm\Value\AbstractValue $yfield
 * @var \Yakamara\YCom\User $user
 * @var array<string, mixed>|null $session
 * @var \Yakamara\YCom\Auth\Otp\PasswordConfig $config
 * @var int $otp_article_id
 */

use Redaxo\Core\Filesystem\Url;
use Redaxo\Core\Http\Request;
use Redaxo\Core\Http\Response;
use Redaxo\Core\Language\Language;
use Redaxo\Core\Translation\I18n;
use Yakamara\YCom\Auth\Otp\EmailMethod;
use Yakamara\YCom\Auth\Otp\Password;
use Yakamara\YCom\Auth\UserSession;
use Yakamara\YCom\Config;


extract($this->getVariables());

$otp = Password::getInstance();
$method = $otp->getMethod();

$blockTime = (int) ($method::getPeriod() / 10);
$triesAllowed = $method::getLoginTries();
$tries = (int) $user->getValue('otp_tries');
$lastTry = (int) $user->getValue('otp_last_try_time');

$myOtp = Request::post('otp', 'string', null);

if (null === $myOtp) {
    // The email method has to deliver a code before the user can enter one.
    if ($method instanceof EmailMethod) {
        $otp->challenge();
    }
} elseif ($tries >= $triesAllowed && $lastTry > time() - $blockTime) {
    $countdown = $lastTry - time() + $blockTime;

    $yfield->params['warning'][$yfield->getId()] = $yfield->params['error_class'];
    $yfield->params['warning_messages'][$yfield->getId()] = I18n::msg('ycom_otp_code_error_blocked', $countdown);

    echo '<p>' . I18n::msg('ycom_otp_code_error_blocked', $countdown) . '</p>';
} elseif ($otp->verify($myOtp)) {
    $user->resetOTPTries()->save();
    UserSession::getInstance()->setOTPverified($user);

    Response::sendRedirect(Url::article((int) Config::get('article_id_jump_ok'), Language::getCurrentId()));
} else {
    $user->increaseOTPTries()->save();

    $yfield->params['warning'][$yfield->getId()] = $yfield->params['error_class'];
    $yfield->params['warning_messages'][$yfield->getId()] = I18n::msg('ycom_otp_code_error');
}

?>
<div class="row">
    <div class="col-lg-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= I18n::msg('ycom_otp_verify_title') ?></h3>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="otp-verify-code"><?= I18n::msg('ycom_otp_verify_code') ?></label>
                    <input type="text" class="form-control" name="otp" id="otp-verify-code"
                           inputmode="numeric" autocomplete="one-time-code" />
                </div>
            </div>
        </div>
    </div>
</div>
