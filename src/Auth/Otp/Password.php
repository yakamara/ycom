<?php

namespace Yakamara\YCom\Auth\Otp;

use Redaxo\Core\Addon\Addon;
use Redaxo\Core\Base\SingletonTrait;
use Redaxo\Core\Exception\InvalidArgumentException;
use Redaxo\Core\Http\Request;
use Yakamara\YCom\Auth\Auth;
use Yakamara\YCom\Config;

use function str_replace;

/**
 * The OTP entry point: issues and verifies codes for the current user.
 */
final class Password
{
    use SingletonTrait;

    public const string ENFORCED_ALL = 'all';
    public const string ENFORCED_DISABLED = 'disabled';

    public const string OPTION_ALL = 'all';
    public const string OPTION_TOTP = 'totp_only';
    public const string OPTION_EMAIL = 'email_only';

    private ?MethodInterface $method = null;

    public function challenge(): void
    {
        $user = Auth::getUser();

        if (null === $user) {
            return;
        }

        $this->getMethod()->challenge($this->getProvisioningUri(), $user);
    }

    public function verify(string $otp): bool
    {
        return $this->getMethod()->verify($this->getProvisioningUri(), $otp);
    }

    public function isVerified(): bool
    {
        return Request::session('otp_verified', 'bool', false);
    }

    public function isEnabled(): bool
    {
        return PasswordConfig::forCurrentUser()->enabled;
    }

    /** @param self::ENFORCED_* $enforce */
    public function enforce(string $enforce): void
    {
        Addon::require('ycom')->setConfig('otp_auth_enforce', $enforce);
        Config::reset();
    }

    /** @return self::ENFORCED_* */
    public function isEnforced(): string
    {
        return (string) Config::get('otp_auth_enforce', self::ENFORCED_DISABLED);
    }

    /** @return self::OPTION_* */
    public function getAuthOption(): string
    {
        return (string) Config::get('otp_auth_option', self::OPTION_ALL);
    }

    /** @param self::OPTION_* $option */
    public function setAuthOption(string $option): void
    {
        Addon::require('ycom')->setConfig('otp_auth_option', $option);
        Config::reset();
    }

    public function getMethod(): MethodInterface
    {
        if (null === $this->method) {
            $methodType = PasswordConfig::forCurrentUser()->getMethod();

            $this->method = match ($methodType) {
                'totp' => new TotpMethod(),
                'email' => new EmailMethod(),
                default => throw new InvalidArgumentException('Unknown OTP method: ' . (string) $methodType),
            };
        }

        return $this->method;
    }

    /**
     * The stored `otpauth://` URI.
     *
     * The URI is stored HTML-escaped by yform's save path, so the `&amp;` separators have to be
     * turned back into `&` before otphp parses it.
     */
    private function getProvisioningUri(): string
    {
        return str_replace('&amp;', '&', (string) PasswordConfig::forCurrentUser()->getProvisioningUri());
    }
}
