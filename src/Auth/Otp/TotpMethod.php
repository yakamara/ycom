<?php

namespace Yakamara\YCom\Auth\Otp;

use OTPHP\Factory;
use OTPHP\TOTP;
use Override;
use Redaxo\Core\Core;
use Redaxo\Core\Http\Request;
use Yakamara\YCom\User;

use function str_replace;

/**
 * Time-based one-time passwords from an authenticator app (Google Authenticator, 1Password, …).
 */
final class TotpMethod implements MethodInterface
{
    #[Override]
    public function challenge(string $provisioningUrl, User $user): void
    {
        // The code comes from the user's own app — nothing to deliver.
    }

    #[Override]
    public function verify(string $provisioningUrl, string $otp): bool
    {
        return Factory::loadFromProvisioningUri($provisioningUrl)->verify($otp);
    }

    /**
     * 30 seconds with a SHA1 digest — Google Authenticator supports nothing else.
     */
    #[Override]
    public static function getPeriod(): int
    {
        return 30;
    }

    #[Override]
    public static function getLoginTries(): int
    {
        return 10;
    }

    #[Override]
    public function getProvisioningUri(User $user): string
    {
        $otp = TOTP::create(null, self::getPeriod());

        // The label an authenticator app shows. A colon would break the URI.
        $host = Request::server('HTTP_HOST', 'string', '');
        $label = str_replace(':', '_', (string) $user->getValue('login') . '@' . Core::getServerName() . ' (' . $host . ')');

        $otp->setLabel($label);
        $otp->setIssuer(str_replace(':', '_', (string) $user->getValue('login')));

        return $otp->getProvisioningUri();
    }
}
