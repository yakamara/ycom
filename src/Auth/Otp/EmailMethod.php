<?php

namespace Yakamara\YCom\Auth\Otp;

use OTPHP\Factory;
use OTPHP\TOTP;
use Override;
use Redaxo\Core\Addon\Addon;
use Redaxo\Core\Core;
use Redaxo\Core\Http\Request;
use Redaxo\Core\Mailer\Mailer;
use Yakamara\YCom\User;
use Yakamara\YForm\Email\Template;

use function str_replace;
use function time;

/**
 * One-time passwords sent by email.
 *
 * If a yform email template named {@see self::EMAIL_TEMPLATE_KEY} exists it is used, so the mail can
 * be edited in the backend; otherwise a plain built-in message is sent.
 */
final class EmailMethod implements MethodInterface
{
    public const string EMAIL_TEMPLATE_KEY = 'ycom_otp_code_template';

    #[Override]
    public function challenge(string $provisioningUrl, User $user): void
    {
        $otpCode = Factory::loadFromProvisioningUri($provisioningUrl)->at(time());

        $template = Template::getTemplate(self::EMAIL_TEMPLATE_KEY);

        if ($template) {
            $mail = Template::replaceVars($template, [
                'email' => $user->getValue('email'),
                'name' => $user->getValue('name'),
                'firstname' => $user->getValue('firstname'),
                'code' => $otpCode,
            ]);
            $mail['mail_to'] = $user->getValue('email');
            $mail['mail_to_name'] = $user->getValue('name');

            if (!Template::sendMail($mail, self::EMAIL_TEMPLATE_KEY)) {
                throw new OtpException('Unable to send the OTP email via the yform template.');
            }

            return;
        }

        $host = Request::server('HTTP_HOST', 'string', '');

        $mailer = new Mailer();
        $mailer->addAddress((string) $user->getValue('email'));
        $mailer->Subject = 'OTP-Code: (' . $host . ')';
        $mailer->isHTML();
        $mailer->Body = '<style>body { font-size: 1.2em; text-align: center;}</style><h2>'
            . Core::getServerName() . ' Login verification</h2><br><h3><strong>'
            . $otpCode . '</strong></h3><br> is your 2 factor authentication code.';
        $mailer->AltBody = " Login verification \r\n ------------------ \r\n" . $otpCode
            . "\r\n ------------------ \r\nis your 2 factor authentication code.";

        if (!$mailer->send()) {
            throw new OtpException('Unable to send the OTP email. Check the mailer configuration.');
        }
    }

    #[Override]
    public static function getPeriod(): int
    {
        // REDAXO 5 read this from the unrelated `2factor_auth` addon, which ycom never required —
        // so the configured period was silently ignored and the default always applied. The value
        // belongs to ycom's own OTP settings.
        return (int) (Addon::require('ycom')->getConfig('otp_auth_email_period') ?: 300);
    }

    #[Override]
    public static function getLoginTries(): int
    {
        return 10;
    }

    #[Override]
    public function verify(string $provisioningUrl, string $otp): bool
    {
        $totp = Factory::loadFromProvisioningUri($provisioningUrl);

        if ($totp->verify($otp)) {
            return true;
        }

        // An email is slower than the clock: the code from the previous period still counts.
        return $totp->at(time() - self::getPeriod()) === $otp;
    }

    #[Override]
    public function getProvisioningUri(User $user): string
    {
        $otp = TOTP::create(null, self::getPeriod());

        $host = Request::server('HTTP_HOST', 'string', '');
        $label = str_replace(':', '_', (string) $user->getValue('login') . '@' . Core::getServerName() . ' (' . $host . ')');

        $otp->setLabel($label);
        $otp->setParameter('period', self::getPeriod());
        $otp->setIssuer(str_replace(':', '_', (string) $user->getValue('login')));

        return $otp->getProvisioningUri();
    }
}
