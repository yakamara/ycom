<?php

use OTPHP\Factory;
use OTPHP\TOTP;

use function str_replace;

final class rex_ycom_otp_method_totp implements rex_ycom_otp_method_interface
{
    public function challenge(string $provisioningUrl, rex_ycom_user $user): void
    {
        // nothing todo
    }

    public function verify(string $provisioningUrl, string $otp): bool
    {
        // re-create from an existant uri
        return Factory::loadFromProvisioningUri($provisioningUrl)->verify($otp);
    }

    public static function getPeriod(): int
    {
        // default period is 30s and digest is sha1. Google Authenticator is restricted to this settings
        return 30;
    }

    public static function getloginTries(): int
    {
        return 10;
    }

    public function getProvisioningUri(rex_ycom_user $user): string
    {
        // create a uri with a random secret
        $otp = TOTP::generate();

        // the label rendered in "Google Authenticator" or similar app
        $label = $user->getValue('login') . '@' . rex::getServerName() . ' (' . $_SERVER['HTTP_HOST'] . ')';
        $label = str_replace(':', '_', $label); // colon is forbidden
        $otp = $otp
            ->withParameter('period', self::getPeriod())
            ->withLabel($label)
            ->withIssuer(str_replace(':', '_', $user->getValue('login')));

        return $otp->getProvisioningUri();
    }
}
