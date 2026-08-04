<?php

namespace Yakamara\YCom\YForm\Value;

use Override;
use Yakamara\YCom\Auth\Auth;
use Yakamara\YCom\Auth\Otp\PasswordConfig;
use Yakamara\YCom\Auth\UserSession;
use Yakamara\YCom\Config;
use Yakamara\YForm\Attribute\AsValue;
use Yakamara\YForm\Value\AbstractValue;

/**
 * The OTP page: either the setup form (choose a method, confirm with a code) or the verification
 * form for a session that has not passed the second factor yet.
 *
 * `ycom_auth_otp`
 */
#[AsValue('ycom_auth_otp')]
class AuthOtp extends AbstractValue
{
    #[Override]
    public function enterObject()
    {
        $user = Auth::getUser();

        if (null === $user) {
            return;
        }

        $otpArticleId = (int) Config::get('otp_article_id');

        if (0 === $otpArticleId) {
            return;
        }

        $session = null;
        $config = PasswordConfig::forUser($user);
        $verify = false;

        if ($config->enabled) {
            $session = UserSession::getInstance()->getCurrentSession($user);
            $verify = null === $session || 1 !== (int) $session['otp_verified'];
        }

        $this->params['form_output'][$this->getId()] = $this->parse(
            $verify ? 'ycom_auth_otp_verify' : 'ycom_auth_otp_setup',
            [
                'user' => $user,
                'session' => $session,
                'config' => $config,
                'otp_article_id' => $otpArticleId,
            ],
        );
    }

    #[Override]
    public function getDescription(): string
    {
        return 'ycom_auth_otp -> Beispiel: ycom_auth_otp';
    }

    /** @return array<string, mixed> */
    #[Override]
    public function getDefinitions(): array
    {
        return [
            'type' => 'value',
            'name' => 'ycom_auth_otp',
            'values' => [
                'name' => ['type' => 'name', 'label' => 'Feld'],
                'label' => ['type' => 'text', 'label' => 'Bezeichnung'],
            ],
            'description' => '',
            'famous' => false,
        ];
    }
}
