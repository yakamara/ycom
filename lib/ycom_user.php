<?php

class rex_ycom_user extends \rex_yform_manager_dataset
{

    static function getMe()
    {
        return rex_ycom_auth::getUser();
    }

}
