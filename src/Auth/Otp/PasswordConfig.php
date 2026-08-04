<?php

namespace Yakamara\YCom\Auth\Otp;

use Redaxo\Core\Core;
use Redaxo\Core\Database\Sql;
use Redaxo\Core\Exception\RuntimeException;
use Yakamara\YCom\Auth\Auth;
use Yakamara\YCom\User;

use function array_key_exists;
use function is_array;
use function is_string;
use function json_decode;
use function json_encode;

/**
 * The per-user OTP state, stored as JSON in `rex_ycom_user.otp_config`.
 */
final class PasswordConfig
{
    public ?string $provisioningUri = null;
    public bool $enabled = false;
    /** @var 'totp'|'email'|null */
    public ?string $method = null;

    public function __construct(
        public readonly User $user,
    ) {}

    public static function forCurrentUser(): self
    {
        $user = Auth::getUser();

        if (null === $user) {
            throw new RuntimeException('No frontend user is logged in.');
        }

        return self::forUser($user);
    }

    public static function forUser(User $user): self
    {
        return self::fromJson((string) $user->getValue('otp_config'), $user);
    }

    /**
     * Loads the config straight from the database, bypassing the dataset cache, and makes sure it
     * carries a secret for `$method` — used while setting OTP up.
     */
    public static function loadFromDb(MethodInterface $method, User $user): self
    {
        $sql = Sql::factory();
        $sql->setTable(Core::getTable('ycom_user'));
        $sql->setWhere(['id' => $user->getId()]);
        $sql->select();

        $config = self::fromJson((string) $sql->getValue('otp_config'), $user);
        $config->method = $method instanceof EmailMethod ? 'email' : 'totp';

        if (null === $config->getProvisioningUri()) {
            $config->setProvisioningUri($method->getProvisioningUri($user));
        }

        return $config;
    }

    private static function fromJson(?string $json, User $user): self
    {
        if (is_string($json) && '' !== $json) {
            $configArr = json_decode($json, true);

            if (is_array($configArr)) {
                // Configurations written before the email method existed carry no `method`.
                $config = new self($user);
                $config->provisioningUri = $configArr['provisioningUri'] ?? null;
                $config->enabled = (bool) ($configArr['enabled'] ?? false);
                $config->method = array_key_exists('method', $configArr) ? $configArr['method'] : 'totp';

                return $config;
            }
        }

        $method = new TotpMethod();

        $default = new self($user);
        $default->method = 'totp';
        $default->provisioningUri = $method->getProvisioningUri($user);

        return $default;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
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

    public function updateMethod(MethodInterface $method): self
    {
        $this->method = $method instanceof EmailMethod ? 'email' : 'totp';
        $this->provisioningUri = $method->getProvisioningUri($this->user);

        return $this;
    }

    public function getProvisioningUri(): ?string
    {
        return $this->provisioningUri;
    }

    public function setProvisioningUri(?string $provisioningUri): self
    {
        $this->provisioningUri = $provisioningUri;

        return $this;
    }

    /** @return 'totp'|'email'|null */
    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function save(): void
    {
        // REDAXO 5 printed a debug_print_backtrace() here on every save, which leaked file paths
        // into the OTP setup page of the live frontend.
        Sql::factory()
            ->setTable(Core::getTable('ycom_user'))
            ->setWhere(['id' => $this->user->getId()])
            ->setValue('otp_config', (string) json_encode([
                'provisioningUri' => $this->provisioningUri,
                'method' => $this->method,
                'enabled' => $this->enabled,
            ]))
            ->update();

        // The dataset cache still holds the previous JSON.
        $this->user->invalidateData();
    }
}
