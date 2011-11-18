DELETE FROM `rex_xform_field` where `table_name`='rex_com_user' and `f1` = 'newsletter_last_id';
DELETE FROM `rex_xform_field` where `table_name`='rex_com_user' and `f1` = 'newsletter';
INSERT INTO `rex_xform_field` (`table_name`, `prio`, `type_id`, `type_name`, `f1`, `f2`, `f3`, `f4`, `f5`, `f6`, `f7`, `f8`, `f9`, `list_hidden`, `search`) VALUES ('rex_com_user', 200, 'value', 'text', 'newsletter_last_id', 'translate:newsletter_last_id', '', '0', '', '', '', '', '', 1, 1);
INSERT INTO `rex_xform_field` (`table_name`, `prio`, `type_id`, `type_name`, `f1`, `f2`, `f3`, `f4`, `f5`, `f6`, `f7`, `f8`, `f9`, `list_hidden`, `search`) VALUES ('rex_com_user', 210, 'value', 'checkbox', 'newsletter', 'translate:newsletter', '', '0', '0', '', '', '', '', 1, 1);
