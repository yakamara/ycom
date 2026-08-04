<?php

namespace Yakamara\YCom\Auth;

use DateTime;
use Redaxo\Core\Exception\RuntimeException;
use Redaxo\Core\Translation\I18n;
use Yakamara\YCom\User;

use function array_key_exists;
use function date;
use function sprintf;

/**
 * Brute-force protection: what happens after N failed logins for one account.
 */
final class AuthRules
{
    public const string DEFAULT_RULE = 'login_try_5_pause';

    /** @var array<string, array{info: string, trigger: string, tries?: int, action?: array<string, mixed>}> */
    private array $rules;

    public function __construct()
    {
        $this->rules = [
            'login_try_unlimited' => [
                'info' => I18n::msg('ycom_auth_config_login_tries_unlimited'),
                'trigger' => 'none',
            ],
            'login_try_5_deactivate' => [
                'info' => I18n::msg('ycom_auth_config_login_tries_deactivateafter', 5),
                'trigger' => 'min',
                'tries' => 5,
                'action' => ['type' => 'deactivate'],
            ],
            'login_try_10_deactivate' => [
                'info' => I18n::msg('ycom_auth_config_login_tries_deactivateafter', 10),
                'trigger' => 'min',
                'tries' => 10,
                'action' => ['type' => 'deactivate'],
            ],
            'login_try_20_deactivate' => [
                'info' => I18n::msg('ycom_auth_config_login_tries_deactivateafter', 20),
                'trigger' => 'min',
                'tries' => 20,
                'action' => ['type' => 'deactivate'],
            ],
            'login_try_5_pause' => [
                'info' => I18n::msg('ycom_auth_config_login_tries_pause', 5, 15),
                'trigger' => 'interval',
                'tries' => 5,
                'action' => ['type' => 'pause', 'time' => 15 * 60],
            ],
            'login_try_10_pause' => [
                'info' => I18n::msg('ycom_auth_config_login_tries_pause', 10, 15),
                'trigger' => 'interval',
                'tries' => 10,
                'action' => ['type' => 'pause', 'time' => 15 * 60],
            ],
            'login_try_10_always_pause' => [
                'info' => I18n::msg('ycom_auth_config_login_tries_always_pause', 10, 5),
                'trigger' => 'min',
                'tries' => 10,
                'action' => ['type' => 'pause', 'time' => 5 * 60],
            ],
        ];
    }

    /**
     * Whether this user may attempt a login right now.
     */
    public function check(User $user, string $ruleName = self::DEFAULT_RULE): bool
    {
        if (!array_key_exists($ruleName, $this->rules)) {
            $ruleName = self::DEFAULT_RULE;
        }

        $rule = $this->rules[$ruleName];
        $loginTries = (int) $user->getValue('login_tries');

        switch ($rule['trigger']) {
            case 'none':
                return true;
            case 'min':
                if ($rule['tries'] > $loginTries) {
                    return true;
                }
                break;
            case 'interval':
                if (0 === $loginTries || 0 !== $loginTries % $rule['tries']) {
                    return true;
                }
                break;
            default:
                throw new RuntimeException(sprintf('Unknown auth_rule trigger key "%s".', $rule['trigger']));
        }

        return match ($rule['action']['type']) {
            'deactivate' => $this->deactivate($user),
            'pause' => $this->isPauseOver($user, (int) $rule['action']['time']),
            default => throw new RuntimeException(sprintf('Unknown auth_rule action key "%s".', $rule['action']['type'])),
        };
    }

    private function deactivate(User $user): bool
    {
        $user->increaseLoginTries();
        $user->setValue('status', User::STATUS_INACTIVE_LOGINS);
        $user->save();

        return false;
    }

    private function isPauseOver(User $user, int $seconds): bool
    {
        $lastTry = (string) $user->getValue('last_login_try_time');

        if ('' === $lastTry) {
            return true;
        }

        $lastLoginTryDate = new DateTime($lastTry);
        $lastLoginTryDate->modify('+' . $seconds . ' seconds');

        return date('YmdHis') >= $lastLoginTryDate->format('YmdHis');
    }

    /** @return array<string, string> */
    public function getOptions(): array
    {
        $options = [];

        foreach ($this->rules as $key => $rule) {
            $options[$key] = $rule['info'];
        }

        return $options;
    }
}
