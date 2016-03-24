<?php

$content = rex_file::get(rex_path::addon('ycom','README.md'));

$fragment = new rex_fragment();
$fragment->setVar('title', $this->i18n('ycom_info_title'),'');
$fragment->setVar('body', rex_ycom::parseText($content), false);
echo $fragment->parse('core/page/section.php');


