DROP TABLE IF EXISTS `%TABLE_PREFIX%ycom_user`;
DELETE FROM `%TABLE_PREFIX%yform_table` WHERE table_name = '%TABLE_PREFIX%ycom_user';
DELETE FROM `%TABLE_PREFIX%yform_field` WHERE table_name = '%TABLE_PREFIX%ycom_user';
