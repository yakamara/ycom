<?php

// module:com_board_basic_out

$b = new rex_com_board('aid-REX_ARTICLE_ID', $this->getValue('name'));
$b->setUrl(rex_getUrl(REX_ARTICLE_ID));
$b->setThreadsPerPage("REX_VALUE[1]");
$b->setPostsPerPage("REX_VALUE[2]");
$b->setNotificationTemplate("REX_VALUE[3]");

echo $b->getView();

?>
