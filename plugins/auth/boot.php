<?php

/**
 * @var rex_addon $this
 * @psalm-scope-this rex_addon
 */

include __DIR__.'/vendor/guzzlehttp/promises/src/functions_include.php';
include __DIR__.'/vendor/guzzlehttp/guzzle/src/functions_include.php';

rex_perm::register('ycomArticlePermissions[]', null, rex_perm::OPTIONS);

rex_extension::register('PACKAGES_INCLUDED', function (rex_extension_point $ep) {
    rex_yform::addTemplatePath($this->getPath('ytemplates'));
});

rex_extension::register('PACKAGES_INCLUDED', function (rex_extension_point $ep) {
    rex_yform::addTemplatePath($this->getPath('ytemplates'));
});

rex_extension::register('SESSION_REGENERATED', [rex_ycom_user_session::class, 'sessionRegenerated']);

if (rex::isFrontend()) {
    rex_extension::register('PACKAGES_INCLUDED', static function (rex_extension_point $ep) {
        if ($redirect = rex_ycom_auth::init()) {
            rex_response::sendCacheControl();
            rex_response::sendRedirect($redirect);
        }
    });

    /* @deprecated use EP ART_IS_PERMITTED and CAT_IS_PERMITTED instead´ */
    rex_extension::register('YREWRITE_ARTICLE_PERM', static function (rex_extension_point $ep) {
        $params = $ep->getParams();
        return rex_ycom_auth::articleIsPermitted($params['article']);
    });

    rex_extension::register(['ART_IS_PERMITTED', 'CAT_IS_PERMITTED'], static function (rex_extension_point $ep) {
        $params = $ep->getParams();
        return rex_ycom_auth::articleIsPermitted($params['element'], $ep->getSubject() ? true : false);
    });

    rex_extension::register('YCOM_AUTH_MATCHING', static function (rex_extension_point $ep) {
        $data = $ep->getSubject();
        $params = $ep->getParams();
        $Userdata = $params['Userdata'];
        $AuthType = $params['AuthType'];

        switch ($AuthType) {
            case 'oauth2':
                $data = rex_extension::registerPoint(new rex_extension_point('YCOM_AUTH_OAUTH2_MATCHING', $data, ['Userdata' => $Userdata]));
                break;
            case 'saml':
                $data = rex_extension::registerPoint(new rex_extension_point('YCOM_AUTH_SAML_MATCHING', $data, ['Userdata' => $Userdata]));
                break;
            case 'cas':
                $data = rex_extension::registerPoint(new rex_extension_point('YCOM_AUTH_CAS_MATCHING', $data, ['Userdata' => $Userdata]));
                break;
        }

        return $data;
    }, rex_extension::EARLY);
} else {
    rex_view::addCssFile($this->getAssetsUrl('styles.css'));

    rex_extension::register('PACKAGES_INCLUDED', function (rex_extension_point $ep) {
        rex_extension::register('STRUCTURE_CONTENT_SIDEBAR', function (rex_extension_point $ep) {
            $params = $ep->getParams();
            $subject = $ep->getSubject();

            $panel = include rex_path::plugin('ycom', 'auth', 'pages/content.ycom_auth.php');

            $fragment = new rex_fragment();
            $fragment->setVar('title', '<i class="fa fa-user"></i> ' . $this->i18n('ycom_page_perm'), false);
            $fragment->setVar('body', $panel, false);
            $fragment->setVar('article_id', $params['article_id'], false);
            $fragment->setVar('clang', $params['clang'], false);
            $fragment->setVar('ctype', $params['ctype'], false);
            $fragment->setVar('collapse', true);
            $fragment->setVar('collapsed', false);
            $content = $fragment->parse('core/page/section.php');

            return $subject.$content;
        });
    }, rex_extension::EARLY);

    rex_extension::register('YFORM_DATA_LIST_ACTION_BUTTONS', static function (rex_extension_point $ep) {
        $params = $ep->getParams();
        /** @var rex_yform_manager_table $table */
        $table = $params['table'];
        if ('rex_ycom_user' == $table->getTableName()) {
            if (rex::getUser() && rex::getUser()->isAdmin()) {
                $actionButtons = $ep->getSubject();
                $actionButtons['ycom_impersonate'] = [
                    'params' => [],
                    'content' => '<i class="rex-icon rex-icon-sign-in"></i> ' . rex_i18n::msg('ycom_impersonate'),
                    'attributes' => [
                        'onclick' => "return confirm(' " . rex_i18n::msg('ycom_impersonate_alert') . "')",
                    ],
                    'url' => rex_url::backendController(['page' => 'ycom/auth/sessions', 'user_id' => '___id___', 'func' => 'create_session']),
                ];
                return $actionButtons;
            }
        }
    });
}
