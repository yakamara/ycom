<?php

/**
 * Class rex_ycom_auth_rules.
 */

class rex_ycom_auth_rules
{
    public function __construct()
    {
        $this->rules = [];
        $this->rules['login_try_unlimited'] = [
            'info' => rex_i18n::msg('ycom_auth_config_login_tries_unlimited'),
            'trigger' => 'none',
        ];
        $this->rules['login_try_5_deactivate'] = [
            'info' => rex_i18n::msg('ycom_auth_config_login_tries_deactivateafter', 5),
            'trigger' => 'min',
            'tries' => 5,
            'action' => ['type' => 'deactivate'],
        ];
        $this->rules['login_try_10_deactivate'] = [
            'info' => rex_i18n::msg('ycom_auth_config_login_tries_deactivateafter', 10),
            'trigger' => 'min',
            'tries' => 10,
            'action' => ['type' => 'deactivate'],
        ];
        $this->rules['login_try_20_deactivate'] = [
            'info' => rex_i18n::msg('ycom_auth_config_login_tries_deactivateafter', 20),
            'trigger' => 'min',
            'tries' => 20,
            'action' => ['type' => 'deactivate'],
        ];
        $this->rules['login_try_5_pause'] = [
            'info' => rex_i18n::msg('ycom_auth_config_login_tries_pause', 5, 15),
            'trigger' => 'interval',
            'tries' => 5,
            'action' => ['type' => 'pause', 'time' => (15 * 60)], // 15 min pause
        ];
        $this->rules['login_try_10_pause'] = [
            'info' => rex_i18n::msg('ycom_auth_config_login_tries_pause', 10, 15),
            'trigger' => 'interval',
            'tries' => 10,
            'action' => ['type' => 'pause', 'time' => (15 * 60)], // 15 min pause
        ];
        $this->rules['login_try_10_always_pause'] = [
            'info' => rex_i18n::msg('ycom_auth_config_login_tries_always_pause', 10, 5),
            'trigger' => 'min',
            'tries' => 10,
            'action' => ['type' => 'pause', 'time' => (5 * 60)], // 5 min pause
        ];
    }

    public function check(rex_ycom_user $user, $rule_name = 'login_try_5_pause')
    {

        if (!array_key_exists($rule_name, $this->rules)) {
            $rule_name = 'login_try_5_pause';
        }

        $rule = $this->rules[$rule_name];
        $loginTries = $user->getValue('login_tries');

        switch ($rule['trigger']) {
            case 'none':
                return true;
            case 'min':
                if ($rule['tries'] > $loginTries) {
                    return true;
                }
                break;
            case 'interval':
                if ($loginTries % $rule['tries'] != 0) {
                    return true;
                }
                break;
            default:
                throw new rex_exception(sprintf('Unknown auth_rule trigger key "%s".', $rule['trigger']));
        }

        switch ($rule['action']['type']) {
            case 'deactivate':
                $user->setValue('status', -3); // to much login failures
                $user->save();
                return false;
                break;
            case 'pause':
                $lastLoginDate = new DateTime($user->getValue('last_login_time'));
                $lastLoginDate->modify('+'.$rule['action']['time'].' seconds');
                if (date('YmdHis') < $lastLoginDate->format('YmdHis')) {
                    return false;
                }
                return true;
                break;
            default:
                throw new rex_exception(sprintf('Unknown auth_rule action key "%s".', $rule['action']));
        }
    }

    public function getOptions()
    {
        $options = [];

        foreach ($this->rules as $key => $rule) {
            $options[$key] = $rule['info'];
        }

        return $options;
        /*
                $sel_logintries->addOption($this->i18n('ycom_auth_config_login_tries_unlimited'), 0);
                $sel_logintries->addOption($this->i18n('ycom_auth_config_login_tries_deactivateafter', 5), 5);
                $sel_logintries->addOption($this->i18n('ycom_auth_config_login_tries_deactivateafter', 10), 10);
                $sel_logintries->addOption($this->i18n('ycom_auth_config_login_tries_deactivateafter', 20), 20);


        login_tries noch suchen

        auth_rule noch default setzen

                            $maximal_login_tries = (int) rex_plugin::get('ycom', 'auth')->getConfig('login_tries');

                    if ($maximal_login_tries != 0 && $user->login_tries > $maximal_login_tries) {

                    } elseif (@$params['ignorePassword'] || self::checkPassword($params['loginPassword'], $user->id)) {
                        $me = $user;

                        if (!$params['loginStay']) {
                            $me->setValue('session_key', '');
                        }

                        // session fixation
                        self::regenerateSessionId();

                        $me->setValue('login_tries', 0);
                    } else {
                        ++$user->login_tries;
                        $user->save();
                    }

        */
    }
}
