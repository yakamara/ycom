<?php

use InvalidArgumentException;
use rex;
use rex_config;
use rex_singleton_trait;

final class rex_ycom_otp_password
{
    use rex_singleton_trait;

    public const ENFORCED_ALL = 'all';
    public const ENFORCED_DISABLED = 'disabled';

    public const OPTION_ALL = 'all';
    public const OPTION_TOTP = 'totp_only';
    public const OPTION_EMAIL = 'email_only';

    /** @var rex_ycom_otp_method_interface|null */
    private $method;

    public function challenge(): void
    {
        $user = rex_ycom_auth::getUser();
        $uri = str_replace('&amp;', '&', (string) rex_ycom_otp_password_config::forCurrentUser()->getProvisioningUri());
        $this->getMethod()->challenge($uri, $user);
    }

    public function verify(string $otp): bool
    {
        $uri = str_replace('&amp;', '&', (string) rex_ycom_otp_password_config::forCurrentUser()->getProvisioningUri());
        $verified = $this->getMethod()->verify($uri, $otp);
        return $verified;
    }

    public function isVerified(): bool
    {
        return rex_session('otp_verified', 'boolean', false);
    }

    public function isEnabled(): bool
    {
        return rex_ycom_otp_password_config::forCurrentUser()->enabled;
    }

    /**
     * @param self::ENFORCE* $enforce
     */
    public function enforce($enforce): void
    {
        rex_config::set('ycom', 'otp_auth_enforce', $enforce);
    }

    /**
     * @return self::ENFORCE*
     */
    public function isEnforced()
    {
        return rex_config::get('ycom', 'otp_auth_enforce', self::ENFORCED_DISABLED);
    }

    /**
     * @return self::OPTION*
     */
    public function getAuthOption()
    {
        return rex_config::get('ycom', 'otp_auth_option', self::OPTION_ALL);
    }

    public function setAuthOption(string $option): void
    {
        rex_config::set('ycom', 'otp_auth_option', $option);
    }

    /**
     * @return rex_ycom_otp_method_interface
     */
    public function getMethod()
    {
        if (null === $this->method) {
            $methodType = rex_ycom_otp_password_config::forCurrentUser()->getMethod();

            if ('totp' === $methodType) {
                $this->method = new rex_ycom_otp_method_totp();
            } elseif ('email' === $methodType) {
                $this->method = new rex_ycom_otp_method_email();
            } else {
                throw new InvalidArgumentException("Unknown method: $methodType");
            }
        }

        return $this->method;
    }
}
