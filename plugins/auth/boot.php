<?php

rex_perm::register('ycomArticlePermissions[]', null, rex_perm::OPTIONS);

rex_extension::register('PACKAGES_INCLUDED', function (rex_extension_point $ep) {
    rex_yform::addTemplatePath($this->getPath('ytemplates'));
});

if (!rex::isBackend()) {
    rex_extension::register('PACKAGES_INCLUDED', function (rex_extension_point $ep) {
        if (($redirect = rex_ycom_auth::init())) {
            rex_response::sendRedirect($redirect);
        }
    });

    /* @deprecated use EP ART_IS_PERMITTED and CAT_IS_PERMITTED instead´*/
    rex_extension::register('YREWRITE_ARTICLE_PERM', function (rex_extension_point $ep) {
        $params = $ep->getParams();
        return rex_ycom_auth::articleIsPermitted($params['article']);
    });

    rex_extension::register(['ART_IS_PERMITTED','CAT_IS_PERMITTED'], function (rex_extension_point $ep) {
        $params = $ep->getParams();
        return rex_ycom_auth::articleIsPermitted($params['element'], $ep->getSubject());
    });

} else {
    rex_view::addCssFile($this->getAssetsUrl('styles.css'));

    rex_extension::register('PACKAGES_INCLUDED', function (rex_extension_point $ep) {
        rex_extension::register('STRUCTURE_CONTENT_SIDEBAR', function (rex_extension_point $ep) {
            $params = $ep->getParams();
            $subject = $ep->getSubject();

            $panel = include rex_path::plugin('ycom', 'auth', 'pages/content.com_auth.php');

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
}
