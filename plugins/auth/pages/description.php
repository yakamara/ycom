<?php
$content = 'Folgt...<br/>';	

$fragment = new rex_fragment();
$fragment->setVar('title', $this->i18n('ycom_auth_info_title'));
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');

