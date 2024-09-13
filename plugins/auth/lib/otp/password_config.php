<?php

/**
 * @internal
 */
final class rex_ycom_otp_password_config
{
    /** @var string|null */
    public $provisioningUri;
    /** @var bool */
    public $enabled = false;
    /** @var 'totp'|'email'|null */
    public $method;
    /** @var rex_ycom_user */
    public $user;

    public function __construct(rex_ycom_user $user)
    {
        $this->user = $user;
    }

    public static function forCurrentUser(): self
    {
        $user = rex_ycom_auth::getUser();
        return self::forUser($user);
    }

    public static function forUser(rex_ycom_user $user): self
    {
        return self::fromJson($user->getValue('otp_config'), $user);
    }

    public static function loadFromDb(rex_ycom_otp_method_interface $method, rex_ycom_user $user): self
    {
        // get non-cached values
        $userSql = rex_sql::factory();
        $userSql->setTable(rex::getTablePrefix() . 'ycom_user');
        $userSql->setWhere(['id' => $user->getId()]);
        $userSql->select();

        $json = (string) $userSql->getValue('otp_config');
        $config = self::fromJson($json, $user);
        $config->init($method);
        return $config;
    }

    private static function fromJson(?string $json, rex_ycom_user $user): self
    {
        if (is_string($json)) {
            $configArr = json_decode($json, true);

            if (is_array($configArr)) {
                // compat with older versions, which did not yet define a method
                if (!array_key_exists('method', $configArr)) {
                    $configArr['method'] = 'totp';
                }

                $config = new self($user);
                $config->provisioningUri = $configArr['provisioningUri'];
                $config->enabled = $configArr['enabled'];
                $config->method = $configArr['method'];
                return $config;
            }
        }

        $default = new self($user);
        $default->init(new rex_ycom_otp_method_totp());
        return $default;
    }

    private function init(rex_ycom_otp_method_interface $method): void
    {
        $this->method = $method instanceof rex_ycom_otp_method_email ? 'email' : 'totp';
        if (null === $this->provisioningUri) {
            $this->provisioningUri = $method->getProvisioningUri($this->user);
        }

        $this->save();
    }

    public function enable(): void
    {
        $this->enabled = true;

        if (null === $this->provisioningUri) {
            throw new Exception('Missing provisioning url');
        }
        if (null === $this->method) {
            throw new Exception('Missing method');
        }

        $this->save();
    }

    public function isEnabled(): bool
    {
        return $this->enabled ? true : false;
    }

    public function disable(): void
    {
        $this->enabled = false;
        $this->provisioningUri = null;
        $this->save();
    }

    public function updateMethod(rex_ycom_otp_method_interface $method): void
    {
        $this->method = $method instanceof rex_ycom_otp_method_email ? 'email' : 'totp';
        $this->provisioningUri = $method->getProvisioningUri($this->user);
        $this->save();
    }

    private function save(): void
    {
        $userSql = rex_sql::factory();
        $userSql->setTable(rex::getTablePrefix() . 'ycom_user');
        $userSql->setWhere(['id' => $this->user->getId()]);
        $userSql->setValue('otp_config', json_encode(
            [
                'provisioningUri' => $this->provisioningUri,
                'method' => $this->method,
                'enabled' => $this->enabled,
            ],
        ));
        $userSql->update();
    }
}
