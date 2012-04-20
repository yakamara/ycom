
DELETE FROM `rex_xform_table` where `table_name`='rex_com_group';
INSERT INTO `rex_xform_table` (`status`, `table_name`, `name`, `description`, `list_amount`, `prio`, `search`, `hidden`, `export`, `import`) VALUES(1, 'rex_com_group', 'translate:com_group_name', '', 50, 110, 0, 0, 0, 0);

DELETE FROM `rex_xform_field` where `table_name`='rex_com_group';
INSERT INTO `rex_xform_field` (`table_name`, `prio`, `type_id`, `type_name`, `f1`, `f2`, `f3`, `f4`, `f5`, `f6`, `f7`, `f8`, `f9`, `list_hidden`, `search`) VALUES('rex_com_group', 10, 'value', 'text', 'name', 'translate:name', '', '0', '', '', '', '', '', 0, 0);
INSERT INTO `rex_xform_field` (`table_name`, `prio`, `type_id`, `type_name`, `f1`, `f2`, `f3`, `f4`, `f5`, `f6`, `f7`, `f8`, `f9`, `list_hidden`, `search`) VALUES('rex_com_group', 20, 'validate', 'empty', 'name', 'translate:com_group_xform_enter_name', '', '', '', '', '', '', '', 1, 0);

DELETE FROM `rex_xform_field` where `table_name`='rex_com_user' and `f1`='rex_com_group';
INSERT INTO `rex_xform_field` (`table_name`, `prio`, `type_id`, `type_name`, `f1`, `f2`, `f3`, `f4`, `f5`, `f6`, `f7`, `f8`, `f9`, `list_hidden`, `search`) VALUES('rex_com_user', 150, 'value', 'be_manager_relation', 'rex_com_group', 'translate:com_group_name', 'rex_com_group', 'name', '1', '1', '', '', '', 1, 0);
