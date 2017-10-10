<?php

$content = rex_file::get(rex_path::addon('ycom', 'CHANGELOG.md'));

$fragment = new rex_fragment();
$fragment->setVar('title', $this->i18n('ycom_changelog_title'), '');
$fragment->setVar('body', rex_ycom::parseText($content), false);
echo $fragment->parse('core/page/section.php');
