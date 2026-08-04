<?php

namespace Yakamara\YCom\Auth\Otp;

use Yakamara\YCom\User;

/**
 * One way of delivering and checking a one-time password.
 */
interface MethodInterface
{
    /**
     * Hands the current code to the user — a no-op for authenticator apps, an email for the email method.
     *
     * @throws OtpException
     */
    public function challenge(string $provisioningUrl, User $user): void;

    /**
     * @throws OtpException
     */
    public function verify(string $provisioningUrl, string $otp): bool;

    /** Creates a fresh secret and returns it as an `otpauth://` URI. */
    public function getProvisioningUri(User $user): string;

    /** How long one code stays valid, in seconds. */
    public static function getPeriod(): int;

    /** How many verification attempts a user gets. */
    public static function getLoginTries(): int;
}
