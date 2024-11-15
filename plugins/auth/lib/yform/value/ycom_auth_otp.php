<?php

class rex_yform_value_ycom_auth_otp extends rex_yform_value_abstract
{
    public function enterObject(): void
    {
        // User ist hier, weil er will oder muss
        // 1. User ist eingeloggt

        $user = rex_ycom_auth::getUser();
        if (!$user) {
            return;
        }

        $otp_article_id = (int) rex_addon::get('ycom')->getConfig('otp_article_id');
        if (0 == $otp_article_id) {
            return;
        }

        // es gibt hier folgende Seiten

        // Setup - Auswahl der Methode
        // setup, bestätigung der Methode mit Code
        // -> hier wird, wenn erfolgereich, auf die UserStartseite geleitet

        // -> wenn user enabled ist dann auf setup und es eventuell zu deaktivieren

        // Verify - Eingabe des Codes
        // -> hier wird, wenn erfolgereich, auf die UserStartseite geleitet

        $page = 'setup';
        $SessionInstance = null;
        $config = rex_ycom_otp_password_config::forCurrentUser();
        if ($config->enabled) {
            $SessionInstance = rex_ycom_user_session::getInstance()->getCurrentSession($user);
            if (1 != $SessionInstance['otp_verified']) {
                $page = 'verify';
            }
        }

        switch ($page) {
            case 'verify':
                $this->params['form_output'][$this->getId()] = $this->parse(
                    ['value.ycom_auth_otp_verify.tpl.php'],
                    [
                        'user' => $user,
                        'SessionInstance' => $SessionInstance,
                        'config' => $config,
                        'otp_article_id' => $otp_article_id,
                    ],
                );
                break;
            default:
                // setup
                $this->params['form_output'][$this->getId()] = $this->parse(
                    ['value.ycom_auth_otp_setup.tpl.php'],
                    [
                        'user' => $user,
                        'SessionInstance' => $SessionInstance,
                        'config' => $config,
                        'otp_article_id' => $otp_article_id,
                    ],
                );
        }
    }

    public function getDescription(): string
    {
        return 'ycom_auth_otp -> Beispiel: ycom_auth_otp';
    }

    /**
     * @return array<string, mixed>
     */
    public function getDefinitions(): array
    {
        return [
            'type' => 'value',
            'name' => 'ycom_auth_otp',
            'values' => [
                'name' => ['type' => 'name',    'label' => 'Feld'],
                'label' => ['type' => 'text',   'label' => 'Bezeichnung'],
            ],
            'description' => '',
            'famous' => false,
        ];
    }
}
