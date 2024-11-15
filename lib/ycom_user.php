<?php

class rex_ycom_user extends rex_yform_manager_dataset
{
    public string $password = '';
    public int $login_tries = 0;

    /**
     * @return rex_ycom_user|null
     */
    public static function getMe()
    {
        return rex_ycom_auth::getUser();
    }

    public function isInGroup(int $group_id): bool
    {
        $ycom_groups = (string) $this->getValue('ycom_groups');

        if (1 > $group_id) {
            return true;
        }
        if ('' !== $ycom_groups) {
            $ycom_groups_array = explode(',', $ycom_groups);
            if (in_array((string) $group_id, $ycom_groups_array, true)) {
                return true;
            }
        }

        return false;
    }

    public function getPassword(): string
    {
        return $this->getValue('password');
    }

    /**
     * @return array|array<string>
     */
    public function getGroups(): array
    {
        if ('' === $this->getValue('ycom_groups')) {
            return [];
        }

        return explode(',', $this->getValue('ycom_groups'));
    }

    /**
     * @param array<string|int, mixed> $data
     * @return rex_ycom_user|rex_yform_manager_dataset|null
     */
    public static function createUserByEmail(array $data)
    {
        $data['status'] = 1;
        $data['password'] = str_shuffle('1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz');
        $data['login'] = $data['email'];
        $data['login_tries'] = 0;
        $data['termsofuse_accepted'] = 0;

        $data = rex_extension::registerPoint(new rex_extension_point('YCOM_USER_CREATE', $data, []));

        $user = self::create();
        foreach ($data as $k => $v) {
            $user->setValue((string) $k, (string) $v);
        }
        if ($user->save()) {
            return $user;
        }
        return null;
    }

    /**
     * @param array<int|string, mixed> $data
     */
    public static function updateUser(array $data): bool
    {
        $data = rex_extension::registerPoint(new rex_extension_point('YCOM_USER_UPDATE', $data, []));
        $user = self::getMe();

        if (null === $user) {
            return false;
        }

        foreach ($data as $k => $v) {
            $user->setValue((string) $k, (string) $v);
        }

        return $user
            ->save();
    }

    public function increaseLoginTries(): self
    {
        $this->setValue('login_tries', $this->getValue('login_tries') + 1);
        return $this;
    }

    public function increaseOTPTries(): self
    {
        $otp_tries = (int) $this->getValue('otp_tries');
        $this->setValue('otp_tries', $otp_tries + 1);
        $this->setValue('otp_last_try_time', time());
        return $this;
    }

    public function resetOTPTries(): self
    {
        $this->setValue('otp_tries', 0);
        $this->setValue('otp_last_try_time', time());
        return $this;
    }
}
