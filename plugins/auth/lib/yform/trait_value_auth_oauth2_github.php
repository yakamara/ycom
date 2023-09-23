<?php

trait rex_yform_trait_value_auth_oauth2_github
{
    /**
     * @throws rex_exception
     * @return array|string[]
     */
    private function auth_loadSettings(): array
    {
        $SettingFile = $this->auth_ClassKey . '.php';
        $SettingsPath = rex_addon::get('ycom')->getDataPath($SettingFile);
        if (!file_exists($SettingsPath)) {
            throw new rex_exception($this->auth_ClassKey . '-Settings file not found [' . $SettingsPath . ']');
        }

        $settings = [];
        include $SettingsPath;
        return $settings;
    }

    private function auth_getReturnTo(): string
    {
        $returnTos = [];
        $returnTos[] = rex_request('returnTo', 'string'); // wenn returnTo übergeben wurde, diesen nehmen
        $returnTos[] = rex_getUrl(rex_config::get('ycom/auth', 'article_id_jump_ok'), '', [], '&'); // Auth Ok -> article_id_jump_ok / Current Language will be selected
        return rex_ycom_auth::getReturnTo($returnTos, ('' == $this->getElement(3)) ? [] : explode(',', $this->getElement(3)));
    }

    private function auth_FormOutput(string $url): void
    {
        if ($this->needsOutput()) {
            $this->params['form_output'][$this->getId()] = $this->parse(['value.ycom_auth_' . $this->auth_ClassKey . '.tpl.php', 'value.ycom_auth_extern.tpl.php'], [
                'url' => $url,
                'name' => '{{ ' . $this->auth_ClassKey . '_auth }}',
            ]);
        }
    }

    private function auth_redirectToFailed(string $message = ''): string
    {
        if ($this->params['debug']) {
            dump($message);
            return $message;
        }
        if ($this->auth_directLink) {
            rex_response::sendCacheControl();
            rex_response::sendRedirect(rex_getUrl(rex_config::get('ycom/auth', 'article_id_jump_not_ok')));
        }
        return '';
    }

    /**
     * @param array<int|string, mixed>  $Userdata
     * @throws rex_exception
     */
    private function auth_createOrUpdateYComUser(array $Userdata, string $returnTo): void
    {
        $defaultUserAttributes = [];
        if ('' != $this->getElement(4)) {
            if (null == $defaultUserAttributes = json_decode($this->getElement(4), true)) {
                throw new rex_exception($this->auth_ClassKey . '-DefaultUserAttributes is not a json' . $this->getElement(4));
            }
        }

        $data = [];
        $data['email'] = '';
        foreach (['User.email', 'emailAddress', 'email'] as $Key) {
            if (isset($Userdata[$Key])) {
                $data['email'] = is_array($Userdata[$Key]) ? implode(' ', $Userdata[$Key]) : $Userdata[$Key];
            }
        }

        $data['firstname'] = '';
        foreach (['login'] as $Key) {
            if (isset($Userdata[$Key])) {
                $data['firstname'] = is_array($Userdata[$Key]) ? implode(' ', $Userdata[$Key]) : $Userdata[$Key];
            }
        }

        $data['name'] = '';
        foreach (['name'] as $Key) {
            if (isset($Userdata[$Key])) {
                $data['name'] = is_array($Userdata[$Key]) ? implode(' ', $Userdata[$Key]) : $Userdata[$Key];
            }
        }

        $data['user_image'] = '';
        foreach (['avatar_url'] as $Key) {
            if (isset($Userdata[$Key])) {
                $data['user_image'] = is_array($Userdata[$Key]) ? implode(' ', $Userdata[$Key]) : $Userdata[$Key];
            }
        }

        foreach ($defaultUserAttributes as $defaultUserAttributeKey => $defaultUserAttributeValue) {
            $data[$defaultUserAttributeKey] = $defaultUserAttributeValue;
        }

        $data = rex_extension::registerPoint(new rex_extension_point('YCOM_AUTH_MATCHING', $data, ['Userdata' => $Userdata, 'AuthType' => $this->auth_ClassKey]));

        self::auth_clearUserSession();

        // not logged in - check if available
        $params = [
            'loginName' => $data['email'],
            'loginPassword' => '',
            'loginStay' => true,
            'filter' => 'status > 0',
            'ignorePassword' => true,
        ];

        $loginStatus = rex_ycom_auth::login($params);
        if (2 == $loginStatus) {
            // already logged in
            rex_ycom_user::updateUser($data);
            rex_response::sendCacheControl();
            rex_response::sendRedirect($returnTo);
        }

        // if user not found, check if exists, but no permission
        $user = rex_ycom_user::query()->where('email', $data['email'])->findOne();
        if ($user) {
            $this->auth_redirectToFailed('{{ ' . $this->auth_ClassKey . '.error.ycom_login_failed }}');
            $this->params['warning_messages'][] = ('' != $this->getElement(2)) ? $this->getElement(2) : '{{ ' . $this->auth_ClassKey . '.error.ycom_login_failed }}';
            return;
        }

        $user = rex_ycom_user::createUserByEmail($data);
        if (!$user || count($user->getMessages()) > 0) {
            if ($user && $this->params['debug']) {
                dump($user->getMessages());
            }
            $this->auth_redirectToFailed('{{ ' . $this->auth_ClassKey . '.error.ycom_create_user }}');
            $this->params['warning_messages'][] = ('' != $this->getElement(2)) ? $this->getElement(2) : '{{ ' . $this->auth_ClassKey . '.error.ycom_create_user }}';
            return;
        }

        $params = [];
        $params['loginName'] = $user->getValue('email');
        $params['ignorePassword'] = true;
        $params['loginStay'] = false;
        $params['filter'] = 'status > 0';
        $params['loginPassword'] = '';
        $loginStatus = rex_ycom_auth::login($params);

        if (2 != $loginStatus) {
            if ($this->params['debug']) {
                dump($loginStatus);
                dump($user);
            }
            $this->auth_redirectToFailed('{{ ' . $this->auth_ClassKey . '.error.ycom_login_created_user }}');
            $this->params['warning_messages'][] = ('' != $this->getElement(2)) ? $this->getElement(2) : '{{ ' . $this->auth_ClassKey . '.error.ycom_login_created_user }}';
            return;
        }

        rex_response::sendCacheControl();
        rex_response::sendRedirect($returnTo);
    }

    private function auth_clearUserSession(): void
    {
        foreach ($this->auth_SessionVars as $SessionKey) {
            rex_ycom_auth::unsetSessionVar($SessionKey);
        }
    }
}
