<?php

rex_yform_manager_dataset::setModelClass('rex_ycom_group', rex_ycom_group::class);
rex_ycom::addTable('rex_ycom_group');

if (rex::isBackend()) {
    rex_extension::register('YCOM_ARTICLE_PERM_SELECT', function (rex_extension_point $ep) {
        $yform = $ep->getSubject();
        $yform->setValueField('select', ['ycom_group_type', rex_i18n::msg('ycom_group_type'), rex_ycom_group::$perms, '', 0]);
        $yform->setValueField('select', ['ycom_groups', rex_i18n::msg('ycom_groups'), rex_ycom_group::getGroups(), '', 0, 'size' => 5, 'multiple' => true]);
        return $yform;
    });
}

rex_extension::register('YCOM_AUTH_USER_CHECK', function (rex_extension_point $ep) {
    if ($ep->getSubject() == false) {
        return false;
    }

    $article = $ep->getParam('article');
    $me = $ep->getParam('me');

    // if logged in perms - check group perms
    $article_group_type = (int) $article->getValue('ycom_group_type');

    if ($article_group_type < 1) {
        return true;
    }

    switch ($article_group_type) {
        // user in every group
        case 1:
            $art_groups = explode(',', $article->getValue('ycom_groups'));
            $user_groups = [];
            if (is_object($me)) {
                $user_groups = explode(',', $me->ycom_groups);
            }
            foreach ($art_groups as $ag) {
                if ($ag != '' && !in_array($ag, $user_groups)) {
                    return false;
                }
            }
            return true;

        // user in at least one group
        case 2:
            $art_groups = explode(',', $article->getValue('ycom_groups'));
            $user_groups = [];
            if (is_object($me)) {
                $user_groups = explode(',', $me->ycom_groups);
            }
            foreach ($art_groups as $ag) {
                if ($ag != '' && in_array($ag, $user_groups)) {
                    return true;
                }
            }
            return false;

        // user is not in one of the groups
        case 3:
            $user_groups = [];
            if (is_object($me)) {
                $user_groups = explode(',', $me->ycom_groups);
            }
            if (count($user_groups) == 0) {
                return true;
            }
            return false;

        default:
            return false;
    }

    return true;
});
