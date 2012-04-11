
DELETE FROM `rex_xform_field` where `table_name`='rex_com_user' and `type_name`='com_auth_password_hash';

INSERT INTO `rex_xform_field` (`table_name`, `prio`, `type_id`, `type_name`, `f1`, `f2`, `f3`, `f4`, `f5`, `f6`, `f7`, `f8`, `f9`, `list_hidden`, `search`) VALUES ('rex_com_user', 55, 'value', 'com_auth_password_hash', 'password_hash', 'password', '', '', '', '', '', '', '', 1, 0);
