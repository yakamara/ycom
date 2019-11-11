<?php

rex_extension::register(['MEDIA_IS_PERMITTED'], static function (rex_extension_point $ep) {
    $ycom_ignore = $ep->getParam('ycom_ignore');
    $subject = $ep->getSubject();
    if ($ycom_ignore) {
        return $subject;
    }
    if (!$subject) {
        return false;
    }
    $rex_media = $ep->getParam('element');
    return \rex_ycom_media_auth::checkFrontendPerm($rex_media);
});

rex_extension::register(['MEDIA_MANAGER_BEFORE_SEND'], function (rex_extension_point $ep) {
    $ycom_ignore = $ep->getParam('ycom_ignore');
    if ($ycom_ignore) {
        return;
    }
    $redirect = rex_ycom_auth::init();
    if (!rex_ycom_media_auth::checkPerm($ep->getSubject(), $ep)) {
        $rules = new rex_ycom_media_auth_rules();
        $rules->check($this->getConfig('media_auth_rule'));
    }
});

rex_extension::register(['MEDIA_FORM_ADD', 'MEDIA_FORM_EDIT', 'MEDIA_ADDED', 'MEDIA_UPDATED'], static function (rex_extension_point $ep) {
    // ----- extens form

    $params = $ep->getParams();
    $prefix = 'ycom_';

    if ('MEDIA_FORM_EDIT' == $ep->getName()) {
        $params['activeItem'] = $params['media'];
        unset($params['media']);
    } elseif ('MEDIA_ADDED' == $ep->getName()) {
        $sql = rex_sql::factory();
        $sql->setQuery('SELECT id FROM ' . rex::getTablePrefix() . 'media WHERE filename=?', [$params['filename']]);
        if (1 == $sql->getRows()) {
            $params['id'] = $sql->getValue('id');
        } else {
            throw new rex_exception('Error occured during file upload!');
        }
    }

    // ----- form field

    $ycom_auth_default = rex_request($prefix.'auth_type', 'string');
    if (!array_key_exists($ycom_auth_default, rex_ycom_media_auth::$perms)) {
        $ycom_auth_default = key(rex_ycom_media_auth::$perms);
    }

    $group = rex_plugin::get('ycom', 'group')->isAvailable();

    if ($group) {
        $ycom_group_default = rex_request($prefix.'group_type', 'string');
        if (!array_key_exists($ycom_group_default, rex_ycom_group::$perms)) {
            $ycom_group_default = key(rex_ycom_group::$perms);
        }
        $ycom_groups_default = rex_request($prefix.'groups', 'array');
    }

    // ----- handle save

    if ('post' == rex_request_method() && isset($params['id'])) {
        $media = rex_sql::factory();
        $media->setTable(rex::getTablePrefix() . 'media');
        $media->setWhere('id=:mediaid', ['mediaid' => $params['id']]);

        if (isset($params['activeItem'])) {
            $params['activeItem']->setValue($prefix.'auth_type', $ycom_auth_default);
            if ($group) {
                $params['activeItem']->setValue($prefix.'group_type', $ycom_group_default);
                $params['activeItem']->setValue($prefix.'groups', implode(',', $ycom_groups_default));
            }
        }
        $media->setValue($prefix.'auth_type', $ycom_auth_default);

        if ($group) {
            $media->setValue($prefix.'group_type', $ycom_group_default);
            $media->setValue($prefix.'groups', implode(',', $ycom_groups_default));
        }
        $media->update();
    }

    // ----- renderFields

    $ycom_auth_type_sel = new rex_select();
    $ycom_auth_type_sel->setStyle('class="form-control"');
    $ycom_auth_type_sel->setSize(1);
    $ycom_auth_type_sel->setName('ycom_auth_type');
    $ycom_auth_type_sel->setAttribute('class', 'selectpicker form-control');

    foreach (rex_ycom_media_auth::$perms as $perm_key => $perm_name) {
        $ycom_auth_type_sel->addOption(rex_i18n::translate($perm_name), $perm_key);
    }

    if (isset($params['activeItem'])) {
        $ycom_auth_type_sel->setSelected($params['activeItem']->getValue($prefix.'auth_type'));
    }

    $e = [];
    $e['label'] = '<label>'.rex_i18n::msg('ycom_auth_perm').'</label>';
    $e['field'] = $ycom_auth_type_sel->get();

    $fragment = new rex_fragment();
    $fragment->setVar('elements', [$e], false);
    $field = $fragment->parse('core/form/form.php');

    // -----

    if ($group) {
        $ycom_group_type_sel = new rex_select();
        $ycom_group_type_sel->setStyle('class="form-control"');
        $ycom_group_type_sel->setSize(1);
        $ycom_group_type_sel->setName('ycom_group_type');
        $ycom_group_type_sel->setAttribute('class', 'selectpicker form-control');

        foreach (rex_ycom_group::$perms as $perm_key => $perm_name) {
            $ycom_group_type_sel->addOption(rex_i18n::translate($perm_name), $perm_key);
        }

        if (isset($params['activeItem'])) {
            $ycom_group_type_sel->setSelected($params['activeItem']->getValue($prefix.'group_type'));
        }

        $e = [];
        $e['label'] = '<label>'.rex_i18n::msg('ycom_group_type').'</label>';
        $e['field'] = $ycom_group_type_sel->get();

        $fragment = new rex_fragment();
        $fragment->setVar('elements', [$e], false);
        $field .= $fragment->parse('core/form/form.php');

        // -----

        $ycom_groups_sel = new rex_select();
        $ycom_groups_sel->setStyle('class="form-control"');
        $ycom_groups_sel->setSize(5);
        $ycom_groups_sel->setName('ycom_groups[]');
        $ycom_groups_sel->setMultiple();
        $ycom_groups_sel->setAttribute('class', 'selectpicker form-control');

        foreach (rex_ycom_group::getGroups() as $group_key => $group_name) {
            $ycom_groups_sel->addOption(rex_i18n::translate($group_name), $group_key);
        }

        if (isset($params['activeItem'])) {
            if ('' != $params['activeItem']->getValue($prefix.'groups')) {
                foreach (explode(',', $params['activeItem']->getValue($prefix.'groups')) as $id) {
                    $ycom_groups_sel->setSelected($id);
                }
            }
        }

        $e = [];
        $e['label'] = '<label>'.rex_i18n::msg('ycom_groups').'</label>';
        $e['field'] = $ycom_groups_sel->get();

        $fragment = new rex_fragment();
        $fragment->setVar('elements', [$e], false);
        $field .= $fragment->parse('core/form/form.php');
    }

    return $ep->getSubject().$field;
});
