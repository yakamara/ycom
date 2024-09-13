<?php

interface rex_ycom_otp_method_interface
{
    /**
     * @throws exception
     */
    public function challenge(string $provisioningUrl, rex_ycom_user $user): void;

    /**
     * @throws exception
     */
    public function verify(string $provisioningUrl, string $otp): bool;

    public function getProvisioningUri(rex_ycom_user $user): string;

    public static function getPeriod(): int;

    public static function getloginTries();
}
