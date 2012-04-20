<?php

// module:com_comment_basic_out
// v2.9
// --------------------------------------------------------------------------------

$c = new rex_com_comments();
$c->setCommentKey('aid-REX_ARTICLE_ID');
$c->setShowAddForm(true);
$c->setPageLink(rex_getUrl(REX_ARTICLE_ID));
if("REX_MEDIA[1]" != "")
{
  $c->setDefaultUserImage('/files/REX_MEDIA[1]');
}

$c->setArticleName($this->getValue('name'));

echo $c->getCommentsView();

?>