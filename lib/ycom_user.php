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

    
    /* Login */
    /** @api */
    public function getLogin() : mixed {
        return $this->getValue("login");
    }
    /** @api */
    public function setLogin(mixed $value) : self {
        $this->setValue("login", $value);
        return $this;
    }

    /* E-Mail */
    /** @api */
    public function getEmail() : mixed {
        return $this->getValue("email");
    }
    /** @api */
    public function setEmail(mixed $value) : self {
        $this->setValue("email", $value);
        return $this;
    }

    /* Passwort */
    /** @api */
    public function setPassword(mixed $value) : self {
        $this->setValue("password", $value);
        return $this;
    }

    /* Vorname */
    /** @api */
    public function getFirstname() : mixed {
        return $this->getValue("firstname");
    }
    /** @api */
    public function setFirstname(mixed $value) : self {
        $this->setValue("firstname", $value);
        return $this;
    }

    /* Name */
    /** @api */
    public function getName() : mixed {
        return $this->getValue("name");
    }
    /** @api */
    public function setName(mixed $value) : self {
        $this->setValue("name", $value);
        return $this;
    }

    /* Status */
    /** @api */
    public function getStatus() : mixed {
        return $this->getValue("status");
    }
    /** @api */
    public function setStatus(mixed $value) : self {
        $this->setValue("status", $value);
        return $this;
    }

    /* Aktivierungsschlüssel */
    /** @api */
    public function getActivationKey() : mixed {
        return $this->getValue("activation_key");
    }
    /** @api */
    public function setActivationKey(mixed $value) : self {
        $this->setValue("activation_key", $value);
        return $this;
    }

    /* Nutzungsbedingungen bestätigt */
    /** @api */
    public function getTermsofuseAccepted() : bool {
        return (bool) $this->getValue("termsofuse_accepted");
    }
    /** @api */
    public function setTermsofuseAccepted(bool $value = true) : self {
        $this->setValue("termsofuse_accepted", $value);
        return $this;
    }
            
    /* Neues Passwort muss gesetzt werden */
    /** @api */
    public function getNewPasswordRequired(bool $asBool = false) : mixed {
        if($asBool) {
            return (bool) $this->getValue("new_password_required");
        }
        return $this->getValue("new_password_required");
    }
    /** @api */
    public function setNewPasswordRequired(int $value = 1) : self {
        $this->setValue("new_password_required", $value);
        return $this;
    }
            
    /* Letzte Aktion */
    /** @api */
    public function getLastActionTime() : ?string {
        return $this->getValue("last_action_time");
    }
    /** @api */
    public function setLastActionTime(string $value) : self {
        $this->setValue("last_action_time", $value);
        return $this;
    }

    /* Letzter erfolgreicher Login */
    /** @api */
    public function getLastLoginTime() : ?string {
        return $this->getValue("last_login_time");
    }
    /** @api */
    public function setLastLoginTime(string $value) : self {
        $this->setValue("last_login_time", $value);
        return $this;
    }

    /* Kündigungszeitpunkt */
    /** @api */
    public function getTerminationTime() : ?string {
        return $this->getValue("termination_time");
    }
    /** @api */
    public function setTerminationTime(string $value) : self {
        $this->setValue("termination_time", $value);
        return $this;
    }

    /* Login Fehlversuche */
    /** @api */
    public function getLoginTries() : ?int {
        return $this->getValue("login_tries");
    }
    /** @api */
    public function setLoginTries(int $value) : self {
        $this->setValue("login_tries", $value);
        return $this;
    }

    /* Gruppen */
    /** @api */
    public function getYcomGroups() : ?rex_yform_manager_dataset {
        return $this->getRelatedDataset("ycom_groups");
    }
}
