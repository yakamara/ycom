<?php

class rex_ycom_media_auth extends \rex_yform_manager_dataset
{
    public static $perms = [
        '0' => 'translate:ycom_perm_all',
        '1' => 'translate:ycom_perm_only_logged_in',
    ];

    public static function checkPerm($media)
    {
        dump($media);

        exit;

    }
}
