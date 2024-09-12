<?php

use OTPHP\Factory;
use OTPHP\TOTP;

final class rex_ycom_otp_method_email implements rex_ycom_otp_method_interface
{
    public function challenge(string $provisioningUrl, rex_ycom_user $user): void
    {
        $mail = new rex_mailer();

        $otp = Factory::loadFromProvisioningUri($provisioningUrl);
        $otpCode = $otp->at(time());

        $mail->addAddress($user->getValue('email'));
        $mail->Subject = 'OTP-Code: (' . $_SERVER['HTTP_HOST'] . ')';
        $mail->isHTML();
        $mail->Body = '<style>body { font-size: 1.2em; text-align: center;}</style><h2>' . rex::getServerName() . ' Login verification</h2><br><h3><strong>' . $otpCode . '</strong></h3><br> is your 2 factor authentication code.';
        $mail->AltBody = " Login verification \r\n ------------------ \r\n" . $otpCode . "\r\n ------------------ \r\nis your 2 factor authentication code.";

        if (!$mail->send()) {
            throw new Exception('Unable to send e-mail. Make sure to setup the phpmailer AddOn.');
        }
    }

    public static function getPeriod(): int
    {
        return (int) rex_addon::get('2factor_auth')->getConfig('email_period', 300);
    }

    public static function getloginTries(): int
    {
        return 10;
    }

    public function verify(string $provisioningUrl, string $otp): bool
    {
        $TOTP = Factory::loadFromProvisioningUri($provisioningUrl);

        // re-create from an existant uri
        if ($TOTP->verify($otp)) {
            return true;
        }

        $lastOTPCode = $TOTP->at(time() - self::getPeriod());
        if ($lastOTPCode == $otp) {
            return Factory::loadFromProvisioningUri($provisioningUrl)->verify($TOTP->at(time()));
        }
        return false;
    }

    public function getProvisioningUri(rex_ycom_user $user): string
    {
        // create a uri with a random secret
        $otp = TOTP::create(null, self::getPeriod());

        // the label rendered in "Google Authenticator" or similar app
        $label = $user->getValue('login') . '@' . rex::getServerName() . ' (' . $_SERVER['HTTP_HOST'] . ')';
        $label = str_replace(':', '_', $label); // colon is forbidden
        $otp->setLabel($label);
        $otp->setParameter('period', self::getPeriod());
        $otp->setIssuer(str_replace(':', '_', $user->getValue('login')));

        return $otp->getProvisioningUri();
    }
}
