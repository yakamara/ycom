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
        $config->method = $method instanceof rex_ycom_otp_method_email ? 'email' : 'totp';
        if (null === $config->getProvisioningUri()) {
            $config->setProvisioningUri($method->getProvisioningUri($user));
        }
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

        $method = new rex_ycom_otp_method_totp();

        $default = new self($user);
        $default->method = $method instanceof rex_ycom_otp_method_email ? 'email' : 'totp';
        $default->provisioningUri = $method->getProvisioningUri($user);

        return $default;
    }

    public function isEnabled(): bool
    {
        return $this->enabled ? true : false;
    }

    public function enable(): self
    {
        $this->enabled = true;
        return $this;
    }

    public function disable(): self
    {
        $this->enabled = false;
        $this->provisioningUri = null;
        return $this;
    }

    public function updateMethod(rex_ycom_otp_method_interface $method): self
    {
        $this->method = $method instanceof rex_ycom_otp_method_email ? 'email' : 'totp';
        $this->provisioningUri = $method->getProvisioningUri($this->user);
        return $this;
    }

    public function getProvisioningUri()
    {
        return $this->provisioningUri;
    }

    public function setProvisioningUri($provisioningUri): self
    {
        $this->provisioningUri = $provisioningUri;
        return $this;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function save(): void
    {
        echo '<pre>';
        debug_print_backtrace();
        echo '</pre>';

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
