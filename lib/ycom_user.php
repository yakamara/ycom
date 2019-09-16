<?php

class rex_ycom_user extends \rex_yform_manager_dataset
{
    public static function getMe()
    {
        return rex_ycom_auth::getUser();
    }

    public function isInGroup($group_id)
    {
        $ycom_groups = $this->getValue('ycom_groups');

        if ($group_id == '') {
            return true;
        }
        if ($ycom_groups != '') {
            $ycom_groups_array = explode(',', $ycom_groups);
            if (in_array($group_id, $ycom_groups_array)) {
                return true;
            }
        }

        return false;
    }

    public function getPassword()
    {
        return $this->password;
    }
}
