<?php

/**
 * @var rex_addon $this
 * @psalm-scope-this rex_addon
 */

rex_yform_manager_dataset::setModelClass('rex_ycom_group', rex_ycom_group::class);
rex_ycom::addTable('rex_ycom_group');

if (rex::isBackend()) {
    rex_extension::register('YCOM_ARTICLE_PERM_SELECT', static function (rex_extension_point $ep) {
        $yform = $ep->getSubject();
        $yform->setValueField('choice', [
            'name' => 'ycom_group_type',
            'label' => rex_i18n::msg('ycom_group_type'),
            'choices' => rex_ycom_group::$perms,
        ]);
        $yform->setValueField('choice', [
            'name' => 'ycom_groups',
            'label' => rex_i18n::msg('ycom_groups'),
            'choices' => rex_ycom_group::getGroups(),
            'size' => 5,
            'multiple' => true,
        ]);
        return $yform;
    });
}

rex_extension::register('YCOM_AUTH_USER_CHECK', static function (rex_extension_point $ep) {
    if (!$ep->getSubject()) {
        return false;
    }

    $article = $ep->getParam('article');

    if (1 != $article->getValue('ycom_auth_type')) {
        return $ep->getSubject();
    }

    /** @var rex_ycom_user|null $me */
    $me = $ep->getParam('me');
    $type = $article->getValue('ycom_group_type');
    $userGroups = ($me) ? $me->getGroups() : [];

    $groups = [];
    if ('' != $article->getValue('ycom_groups')) {
        $groups = explode(',', $article->getValue('ycom_groups'));
    }

    return rex_ycom_group::hasGroupPerm($type, $groups, $userGroups);
});
