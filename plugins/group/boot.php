<?php

rex_yform_manager_dataset::setModelClass('rex_ycom_group', rex_ycom_group::class);
rex_ycom::addTable('rex_ycom_group');

if (rex::isBackend()) {
    rex_extension::register('YCOM_ARTICLE_PERM_SELECT', static function (rex_extension_point $ep) {
        $yform = $ep->getSubject();
        $yform->setValueField('select', ['ycom_group_type', rex_i18n::msg('ycom_group_type'), rex_ycom_group::$perms, '', 0]);
        $yform->setValueField('select', ['ycom_groups', rex_i18n::msg('ycom_groups'), rex_ycom_group::getGroups(), '', 0, 'size' => 5, 'multiple' => true]);
        return $yform;
    });
}

rex_extension::register('YCOM_AUTH_USER_CHECK', static function (rex_extension_point $ep) {
    if (false == $ep->getSubject()) {
        return false;
    }

    $article = $ep->getParam('article');

    if (1 != $article->getValue('ycom_auth_type')) {
        return $ep->getSubject();
    }

    $me = $ep->getParam('me');
    $type = $article->getValue('ycom_group_type');

    $userGroups = [];
    if (is_object($me) && '' != $me->ycom_groups) {
        $userGroups = explode(',', $me->ycom_groups);
    }

    $groups = [];
    if ('' != $article->getValue('ycom_groups')) {
        $groups = explode(',', $article->getValue('ycom_groups'));
    }

    return rex_ycom_group::hasGroupPerm($type, $groups, $userGroups);
});
