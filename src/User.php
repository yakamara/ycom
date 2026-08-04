<?php

namespace Yakamara\YCom;

use Redaxo\Core\ExtensionPoint\Extension;
use Redaxo\Core\ExtensionPoint\ExtensionPoint;
use Yakamara\YCom\Auth\Auth;
use Yakamara\YForm\Manager\Dataset;

use function count;
use function explode;
use function in_array;
use function is_array;
use function str_shuffle;
use function time;

/**
 * A frontend user — the model class behind the yform table `rex_ycom_user`.
 */
class User extends Dataset
{
    public const int STATUS_INACTIVE_TERMINATION = -3;
    public const int STATUS_INACTIVE_LOGINS = -2;
    public const int STATUS_INACTIVE = -1;
    public const int STATUS_REQUESTED = 0;
    public const int STATUS_CONFIRMED = 1;
    public const int STATUS_ACTIVE = 2;

    /** @var array<int, string> */
    public const array DEFAULT_STATUS_OPTIONS = [
        self::STATUS_INACTIVE_TERMINATION => 'translate:ycom_account_inactive_termination',
        self::STATUS_INACTIVE_LOGINS => 'translate:ycom_account_inactive_logins',
        self::STATUS_INACTIVE => 'translate:ycom_account_inactive',
        self::STATUS_REQUESTED => 'translate:ycom_account_requested',
        self::STATUS_CONFIRMED => 'translate:ycom_account_confirm',
        self::STATUS_ACTIVE => 'translate:ycom_account_active',
    ];

    public static function getMe(): ?self
    {
        return Auth::getUser();
    }

    public function isInGroup(int $groupId): bool
    {
        if (1 > $groupId) {
            return true;
        }

        return in_array((string) $groupId, $this->getGroups(), true);
    }

    public function getPassword(): string
    {
        return (string) $this->getValue('password');
    }

    /** @return list<string> */
    public function getGroups(): array
    {
        $groups = (string) $this->getValue('ycom_groups');

        if ('' === $groups) {
            return [];
        }

        return explode(',', $groups);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function createUserByEmail(array $data): ?self
    {
        $data['status'] = self::STATUS_CONFIRMED;
        $data['password'] = str_shuffle('1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz');
        $data['login'] = $data['email'];
        $data['login_tries'] = 0;
        $data['termsofuse_accepted'] = 0;

        $data = Extension::dispatch(new ExtensionPoint('YCOM_USER_CREATE', $data, []));

        $user = self::create();
        foreach ((array) $data as $k => $v) {
            $user->setValue((string) $k, (string) $v);
        }

        return $user->save() ? $user : null;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function updateUser(array $data): bool
    {
        $data = Extension::dispatch(new ExtensionPoint('YCOM_USER_UPDATE', $data, []));
        $user = self::getMe();

        if (null === $user) {
            return false;
        }

        foreach ((array) $data as $k => $v) {
            $user->setValue((string) $k, (string) $v);
        }

        return $user->save();
    }

    public function increaseLoginTries(): self
    {
        $this->setValue('login_tries', (int) $this->getValue('login_tries') + 1);

        return $this;
    }

    public function increaseOTPTries(): self
    {
        $this->setValue('otp_tries', (int) $this->getValue('otp_tries') + 1);
        $this->setValue('otp_last_try_time', time());

        return $this;
    }

    public function resetOTPTries(): self
    {
        $this->setValue('otp_tries', 0);
        $this->setValue('otp_last_try_time', time());

        return $this;
    }

    /**
     * The choices of the `status` field, so a project can add its own.
     *
     * @return array<int, string>
     */
    public static function getStatusOptions(): array
    {
        $options = Extension::dispatch(new ExtensionPoint('YCOM_USER_STATUS_OPTIONS', self::DEFAULT_STATUS_OPTIONS));

        if (!is_array($options) || 1 > count($options)) {
            return self::DEFAULT_STATUS_OPTIONS;
        }

        return $options;
    }
}
