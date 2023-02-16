<?php

/**
 * @var rex_addon $this
 * @psalm-scope-this rex_addon
 */

rex_yform_manager_dataset::setModelClass(rex::getTablePrefix().'ycom_group', rex_ycom_group::class);
rex_ycom::addTable(rex::getTablePrefix().'ycom_group');

if (rex::isBackend()) {
    rex_extension::register('YCOM_ARTICLE_PERM_SELECT', static function (rex_extension_point $ep) {
        /** @var rex_yform $yform */
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

    /** @var rex_article|rex_category $article */
    $article = $ep->getParam('article');

    if (1 != $article->getValue('ycom_auth_type')) {
        return $ep->getSubject();
    }

    /** @var rex_ycom_user|null $me */
    $me = $ep->getParam('me');
    $type = (string) $article->getValue('ycom_group_type');
    $userGroups = ($me) ? $me->getGroups() : [];
    $articleGroups = (string) $article->getValue('ycom_groups');

    $groups = [];
    if ('' != $articleGroups) {
        $groups = explode(',', $articleGroups);
    }

    return rex_ycom_group::hasGroupPerm($type, $groups, $userGroups);
});
