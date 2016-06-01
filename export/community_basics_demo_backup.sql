## Redaxo Database Dump Version 5
## Prefix rex_
## charset utf-8

DROP TABLE IF EXISTS `rex_action`;
CREATE TABLE `rex_action` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `preview` text,
  `presave` text,
  `postsave` text,
  `previewmode` tinyint(4) DEFAULT NULL,
  `presavemode` tinyint(4) DEFAULT NULL,
  `postsavemode` tinyint(4) DEFAULT NULL,
  `createuser` varchar(255) NOT NULL,
  `createdate` datetime NOT NULL,
  `updateuser` varchar(255) NOT NULL,
  `updatedate` datetime NOT NULL,
  `revision` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `rex_article`;
CREATE TABLE `rex_article` (
  `pid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id` int(10) unsigned NOT NULL,
  `parent_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `catname` varchar(255) NOT NULL,
  `catpriority` int(10) unsigned NOT NULL,
  `startarticle` tinyint(1) NOT NULL,
  `priority` int(10) unsigned NOT NULL,
  `path` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `createdate` datetime NOT NULL,
  `updatedate` datetime NOT NULL,
  `template_id` int(10) unsigned NOT NULL,
  `clang_id` int(10) unsigned NOT NULL,
  `createuser` varchar(255) NOT NULL,
  `updateuser` varchar(255) NOT NULL,
  `revision` int(10) unsigned NOT NULL,
  `art_online_from` text,
  `art_online_to` text,
  `art_description` text,
  `art_keywords` text,
  `art_file` varchar(255) DEFAULT '',
  `art_teaser` varchar(255) DEFAULT '',
  `art_type_id` varchar(255) DEFAULT '',
  `yrewrite_url` varchar(255) NOT NULL,
  `yrewrite_canonical_url` varchar(255) NOT NULL,
  `yrewrite_priority` varchar(5) NOT NULL,
  `yrewrite_changefreq` varchar(10) NOT NULL,
  `yrewrite_title` varchar(255) NOT NULL,
  `yrewrite_description` text NOT NULL,
  `yrewrite_index` tinyint(1) NOT NULL,
  `ycom_group_type` enum('0','1','2','3') NOT NULL,
  `ycom_groups` varchar(255) NOT NULL,
  `ycom_auth_type` enum('0','1','2','3') NOT NULL,
  PRIMARY KEY (`pid`),
  UNIQUE KEY `find_articles` (`id`,`clang_id`),
  KEY `id` (`id`),
  KEY `clang_id` (`clang_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8;

LOCK TABLES `rex_article` WRITE;
/*!40000 ALTER TABLE `rex_article` DISABLE KEYS */;
INSERT INTO `rex_article` VALUES 
  (1,1,0,'Home','Home',1,1,1,'|',1,'2016-03-14 23:29:43','2016-03-14 23:23:43',1,1,'admin','admin',0,'','','','','','','','','','','','','',0,'0','','0'),
  (2,2,0,'Beispiele','Beispiele',2,1,1,'|',1,'2016-03-14 23:29:44','2016-03-14 23:23:47',1,1,'admin','admin',0,'','','','','','','','','','','','','',0,'0','','0'),
  (3,3,0,'Interner Bereich','Interner Bereich',3,1,1,'|',1,'2016-03-14 23:29:45','2016-03-22 10:24:09',1,1,'admin','admin',0,'','','','','','','','','','','weekly','','',0,'0','','1'),
  (4,4,0,'-----','-----',4,1,1,'|',0,'2016-03-14 23:29:50','2016-03-14 23:17:10',0,1,'admin','admin',0,'','','','','','','','','','','','','',0,'0','','0'),
  (5,5,0,'Keine Gruppe','Keine Gruppe',5,1,1,'|',1,'2016-03-14 23:29:47','2016-03-14 23:23:55',1,1,'admin','admin',0,'','','','','','','','','','','','','',0,'3','','1'),
  (6,6,0,'Gruppe A','Gruppe A',6,1,1,'|',1,'2016-03-14 23:29:47','2016-03-14 23:24:01',1,1,'admin','admin',0,'','','','','','','','','','','','','',0,'2','1','1'),
  (7,7,0,'Gruppe A oder B','Gruppe A oder B',7,1,1,'|',1,'2016-03-14 23:29:48','2016-03-14 23:24:05',1,1,'admin','admin',0,'','','','','','','','','','','','','',0,'2','1,2','1'),
  (8,8,0,'Gruppe B','Gruppe B',8,1,1,'|',1,'2016-03-14 23:29:48','2016-03-14 23:24:12',1,1,'admin','admin',0,'','','','','','','','','','','','','',0,'2','2','1'),
  (9,9,0,'Gruppe A und C','Gruppe A und C',9,1,1,'|',1,'2016-03-14 23:29:49','2016-03-14 23:24:18',1,1,'admin','admin',0,'','','','','','','','','','','','','',0,'1','1,3','1'),
  (10,10,0,'-----','-----',10,1,1,'|',0,'2016-03-14 23:17:42','2016-03-14 23:17:42',0,1,'admin','admin',0,'','','','','','','','','','','','','',0,'0','','0'),
  (11,11,0,'Metanavigation für User','Metanavigation für User',11,1,1,'|',0,'2016-03-14 23:17:51','2016-03-14 23:24:23',1,1,'admin','admin',0,'','','','','','','','','','','','','',0,'0','','0'),
  (12,12,11,'Login','Login',1,1,1,'|11|',1,'2016-03-14 23:29:52','2016-03-24 14:46:51',1,1,'admin','admin',0,'','','','','','','','','','','','','',0,'0','','2'),
  (13,13,11,'Registrierung','Registrierung',2,1,1,'|11|',1,'2016-03-14 23:29:53','2016-03-29 16:36:32',1,1,'admin','admin',0,'','','','','','','','','','','','','',0,'0','','2'),
  (14,14,11,'Passwort zurücksetzen','Passwort zurücksetzen',3,1,1,'|11|',1,'2016-03-14 23:29:53','2016-03-14 23:47:10',1,1,'admin','admin',0,'','','','','','','','','','','','','',0,'0','','2'),
  (15,15,11,'Logout','Logout',4,1,1,'|11|',1,'2016-03-14 23:39:17','2016-03-24 14:48:25',1,1,'admin','admin',0,'','','','','','','','','','','','','',0,'0','','1'),
  (16,16,11,'Mein Profil','Mein Profil',5,1,1,'|11|',1,'2016-03-14 23:39:18','2016-03-29 15:25:43',1,1,'admin','admin',0,'','','','','','','','','','','','','',0,'0','','1'),
  (17,17,11,'Mein Passwort ändern','Mein Passwort ändern',6,1,1,'|11|',1,'2016-03-14 23:39:18','2016-03-29 15:42:19',1,1,'admin','admin',0,'','','','','','','','','','','','','',0,'0','','1'),
  (18,18,0,'Footernavigation','Footernavigation',12,1,1,'|',0,'2016-03-14 23:37:22','2016-03-14 23:37:22',1,1,'admin','admin',0,'','','','','','','','','','','','','',0,'0','','0'),
  (19,19,18,'Kontakt','Kontakt',1,1,1,'|18|',1,'2016-03-14 23:39:23','2016-03-14 23:37:35',1,1,'admin','admin',0,'','','','','','','','','','','','','',0,'0','','0'),
  (20,20,18,'Impressum','Impressum',2,1,1,'|18|',1,'2016-03-14 23:39:24','2016-03-14 23:37:55',1,1,'admin','admin',0,'','','','','','','','','','','','','',0,'0','','0'),
  (21,21,0,'Extras','Extras',13,1,1,'|',0,'2016-03-14 23:38:11','2016-03-14 23:38:11',1,1,'admin','admin',0,'','','','','','','','','','','','','',0,'0','','0'),
  (22,22,21,'Artikel exististiert nicht','Artikel exististiert nicht',1,1,1,'|21|',1,'2016-03-23 17:08:59','2016-03-14 23:38:22',1,1,'admin','admin',0,'','','','','','','','','','','','','',0,'0','','0'),
  (23,23,21,'Gesperrter Bereich','Gesperrter Bereich',2,1,1,'|21|',1,'2016-03-23 17:09:00','2016-03-14 23:38:29',1,1,'admin','admin',0,'','','','','','','','','','','','','',0,'0','','0'),
  (24,24,13,'Bestätigung','Registrierung',0,0,2,'|11|13|',1,'2016-03-15 01:09:11','2016-03-29 16:50:22',1,1,'admin','admin',0,'','','','','','','','','','','','','',0,'0','','0'),
  (25,25,14,'Passwort wurde zurückgesetzt','Passwort zurücksetzen',0,0,2,'|11|14|',0,'2016-03-15 01:31:46','2016-03-15 01:31:46',1,1,'admin','admin',0,'','','','','','','','','','','','','',0,'0','','0'),
  (26,1,0,'Home','Home',1,1,1,'|',0,'2016-03-14 23:29:43','2016-03-14 23:23:43',1,2,'admin','admin',0,'','','','','','','','','','','','','',0,'0','','0'),
  (27,2,0,'Beispiele','Beispiele',2,1,1,'|',0,'2016-03-14 23:29:44','2016-03-14 23:23:47',1,2,'admin','admin',0,'','','','','','','','','','','','','',0,'0','','0'),
  (28,3,0,'Interner Bereich','Interner Bereich',3,1,1,'|',0,'2016-03-14 23:29:45','2016-03-21 17:35:43',1,2,'admin','admin',0,'','','','','','','','','','','weekly','','',0,'0','','1'),
  (29,4,0,'-----','-----',4,1,1,'|',0,'2016-03-14 23:29:50','2016-03-14 23:17:10',0,2,'admin','admin',0,'','','','','','','','','','','','','',0,'0','','0'),
  (30,5,0,'Keine Gruppe','Keine Gruppe',5,1,1,'|',0,'2016-03-14 23:29:47','2016-03-14 23:23:55',1,2,'admin','admin',0,'','','','','','','','','','','','','',0,'3','','1'),
  (31,6,0,'Gruppe A','Gruppe A',6,1,1,'|',0,'2016-03-14 23:29:47','2016-03-14 23:24:01',1,2,'admin','admin',0,'','','','','','','','','','','','','',0,'2','1','1'),
  (32,7,0,'Gruppe A oder B','Gruppe A oder B',7,1,1,'|',0,'2016-03-14 23:29:48','2016-03-14 23:24:05',1,2,'admin','admin',0,'','','','','','','','','','','','','',0,'2','1,2','1'),
  (33,8,0,'Gruppe B','Gruppe B',8,1,1,'|',0,'2016-03-14 23:29:48','2016-03-14 23:24:12',1,2,'admin','admin',0,'','','','','','','','','','','','','',0,'2','2','1'),
  (34,9,0,'Gruppe A und C','Gruppe A und C',9,1,1,'|',0,'2016-03-14 23:29:49','2016-03-14 23:24:18',1,2,'admin','admin',0,'','','','','','','','','','','','','',0,'1','1,3','1'),
  (35,10,0,'-----','-----',10,1,1,'|',0,'2016-03-14 23:17:42','2016-03-14 23:17:42',0,2,'admin','admin',0,'','','','','','','','','','','','','',0,'0','','0'),
  (36,11,0,'Metanavigation für User','Metanavigation für User',11,1,1,'|',0,'2016-03-14 23:17:51','2016-03-14 23:24:23',1,2,'admin','admin',0,'','','','','','','','','','','','','',0,'0','','0'),
  (37,12,11,'Login','Login',1,1,1,'|11|',0,'2016-03-14 23:29:52','2016-03-14 23:55:02',1,2,'admin','admin',0,'','','','','','','','','','','','','',0,'0','','2'),
  (38,13,11,'Registrierung','Registrierung',2,1,1,'|11|',0,'2016-03-14 23:29:53','2016-03-15 01:02:13',1,2,'admin','admin',0,'','','','','','','','','','','','','',0,'0','','2'),
  (39,14,11,'Passwort zurücksetzen','Passwort zurücksetzen',3,1,1,'|11|',0,'2016-03-14 23:29:53','2016-03-14 23:47:10',1,2,'admin','admin',0,'','','','','','','','','','','','','',0,'0','','2'),
  (40,15,11,'Logout','Logout',4,1,1,'|11|',0,'2016-03-14 23:39:17','2016-03-15 08:11:24',1,2,'admin','admin',0,'','','','','','','','','','','','','',0,'0','','1'),
  (41,16,11,'Mein Profil','Mein Profil',5,1,1,'|11|',0,'2016-03-14 23:39:18','2016-03-14 23:37:01',1,2,'admin','admin',0,'','','','','','','','','','','','','',0,'0','','1'),
  (42,17,11,'Mein Passwort ändern','Mein Passwort ändern',6,1,1,'|11|',0,'2016-03-14 23:39:18','2016-03-14 23:37:07',1,2,'admin','admin',0,'','','','','','','','','','','','','',0,'0','','1'),
  (43,18,0,'Footernavigation','Footernavigation',12,1,1,'|',0,'2016-03-14 23:37:22','2016-03-14 23:37:22',1,2,'admin','admin',0,'','','','','','','','','','','','','',0,'0','','0'),
  (44,19,18,'Kontakt','Kontakt',1,1,1,'|18|',0,'2016-03-14 23:39:23','2016-03-14 23:37:35',1,2,'admin','admin',0,'','','','','','','','','','','','','',0,'0','','0'),
  (45,20,18,'Impressum','Impressum',2,1,1,'|18|',0,'2016-03-14 23:39:24','2016-03-14 23:37:55',1,2,'admin','admin',0,'','','','','','','','','','','','','',0,'0','','0'),
  (46,21,0,'Extras','Extras',13,1,1,'|',0,'2016-03-14 23:38:11','2016-03-14 23:38:11',1,2,'admin','admin',0,'','','','','','','','','','','','','',0,'0','','0'),
  (47,22,21,'Artikel exististiert nicht','Artikel exististiert nicht',1,1,1,'|21|',0,'2016-03-14 23:38:22','2016-03-14 23:38:22',1,2,'admin','admin',0,'','','','','','','','','','','','','',0,'0','','0'),
  (48,23,21,'Gesperrter Bereich','Gesperrter Bereich',2,1,1,'|21|',0,'2016-03-14 23:38:29','2016-03-14 23:38:29',1,2,'admin','admin',0,'','','','','','','','','','','','','',0,'0','','0'),
  (49,24,13,'Bestätigung','Registrierung',0,0,2,'|11|13|',0,'2016-03-15 01:09:11','2016-03-15 01:25:46',1,2,'admin','admin',0,'','','','','','','','','','','','','',0,'0','','0'),
  (50,25,14,'Passwort wurde zurückgesetzt','Passwort zurücksetzen',0,0,2,'|11|14|',0,'2016-03-15 01:31:46','2016-03-15 01:31:46',1,2,'admin','admin',0,'','','','','','','','','','','','','',0,'0','','0');
/*!40000 ALTER TABLE `rex_article` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `rex_article_slice`;
CREATE TABLE `rex_article_slice` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `clang_id` int(10) unsigned NOT NULL,
  `ctype_id` int(10) unsigned NOT NULL,
  `priority` int(10) unsigned NOT NULL,
  `value1` text,
  `value2` text,
  `value3` text,
  `value4` text,
  `value5` text,
  `value6` text,
  `value7` text,
  `value8` text,
  `value9` text,
  `value10` text,
  `value11` text,
  `value12` text,
  `value13` text,
  `value14` text,
  `value15` text,
  `value16` text,
  `value17` text,
  `value18` text,
  `value19` text,
  `value20` text,
  `media1` varchar(255) DEFAULT NULL,
  `media2` varchar(255) DEFAULT NULL,
  `media3` varchar(255) DEFAULT NULL,
  `media4` varchar(255) DEFAULT NULL,
  `media5` varchar(255) DEFAULT NULL,
  `media6` varchar(255) DEFAULT NULL,
  `media7` varchar(255) DEFAULT NULL,
  `media8` varchar(255) DEFAULT NULL,
  `media9` varchar(255) DEFAULT NULL,
  `media10` varchar(255) DEFAULT NULL,
  `medialist1` text,
  `medialist2` text,
  `medialist3` text,
  `medialist4` text,
  `medialist5` text,
  `medialist6` text,
  `medialist7` text,
  `medialist8` text,
  `medialist9` text,
  `medialist10` text,
  `link1` varchar(10) DEFAULT NULL,
  `link2` varchar(10) DEFAULT NULL,
  `link3` varchar(10) DEFAULT NULL,
  `link4` varchar(10) DEFAULT NULL,
  `link5` varchar(10) DEFAULT NULL,
  `link6` varchar(10) DEFAULT NULL,
  `link7` varchar(10) DEFAULT NULL,
  `link8` varchar(10) DEFAULT NULL,
  `link9` varchar(10) DEFAULT NULL,
  `link10` varchar(10) DEFAULT NULL,
  `linklist1` text,
  `linklist2` text,
  `linklist3` text,
  `linklist4` text,
  `linklist5` text,
  `linklist6` text,
  `linklist7` text,
  `linklist8` text,
  `linklist9` text,
  `linklist10` text,
  `article_id` int(10) unsigned NOT NULL,
  `module_id` int(10) unsigned NOT NULL,
  `createdate` datetime NOT NULL,
  `updatedate` datetime NOT NULL,
  `createuser` varchar(255) NOT NULL,
  `updateuser` varchar(255) NOT NULL,
  `revision` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `slice_priority` (`article_id`,`priority`,`module_id`),
  KEY `clang_id` (`clang_id`),
  KEY `article_id` (`article_id`),
  KEY `find_slices` (`clang_id`,`article_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

LOCK TABLES `rex_article_slice` WRITE;
/*!40000 ALTER TABLE `rex_article_slice` DISABLE KEYS */;
INSERT INTO `rex_article_slice` VALUES 
  (1,1,1,1,'Login','h1','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',12,2,'2016-03-14 23:50:40','2016-03-14 23:50:40','admin','admin',0),
  (2,1,1,2,'','','ycom_auth_form_info|label|Bitte einloggen|Benutzer wurde ausgeloggt|Login ist fehlgeschlagen|Benutzer wurde erfolgreich eingeloggt|\r\nycom_auth_form_login|label|Benutzername / E-Mail:\r\nycom_auth_form_password|label|Passwort:\r\nycom_auth_form_stayactive|auth|eingeloggt bleiben:|0','','','','1','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',12,1,'2016-03-14 23:55:02','2016-03-24 14:46:51','admin','admin',0),
  (3,1,1,1,'Registrierung','h1','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',13,2,'2016-03-15 00:00:43','2016-03-23 17:49:48','admin','admin',0),
  (4,1,1,2,'','','generate_key|activation_key\r\nhidden|status|0\r\n\r\nfieldset|label|Login-Daten:\r\n\r\ntext|email|E-Mail:*|\r\ntext|firstname|Vorname:*\r\nvalidate|empty|firstname|Bitte geben Sie Ihren Vornamen ein.\r\n\r\ntext|name|Nachname:*\r\nvalidate|empty|name|Bitte geben Sie Ihren Namen ein.\r\n\r\nycom_auth_password|password|Ihr Passwort:*|\r\npassword|password_2|Passwort bestätigen:*||no_db\r\n\r\nhtml|required|<p class=\"form-required\">* Pflichtfelder</p>\r\n\r\ncaptcha|Bitte geben Sie den entsprechenden Sicherheitscode ein. Sollten Sie den Code nicht lesen können klicken Sie bitte auf die Grafik, um einen neuen Code zu generieren.|Sie haben den Sicherheitscode falsch eingegeben. \r\n\r\nvalidate|email|email|Bitte geben Sie die E-Mail ein.\r\nvalidate|unique|email|Diese E-Mail existiert schon|rex_ycom_user\r\nvalidate|empty|email|Bitte geben Sie Ihre e-Mail ein.\r\nvalidate|empty|password|Bitte geben Sie ein Passwort ein.\r\n\r\nvalidate|compare|password|password_2||Bitte geben Sie zweimal das gleiche Passwort ein\r\n\r\naction|copy_value|email|login\r\naction|db|rex_ycom_user\r\naction|tpl2email|access_request_de|email|','','','h1. Vielen Dank für Ihre Anmeldung\r\n\r\nSind bekommen nun eine Mail mit Ihren Daten als Bestätigung','0','','','','2','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',13,1,'2016-03-15 00:01:24','2016-03-29 16:36:32','admin','admin',0),
  (5,1,1,1,'','','hidden|status|1\r\nobjparams|submit_btn_show|0\r\nobjparams|send|1\r\n\r\nvalidate|ycom_auth_login|activation_key=rex_ycom_activation_key,id=rex_ycom_id|status=0|Zugang wurde bereits bestätigt oder ist schon fehlgeschlagen|status\r\n\r\naction|ycom_auth_db|update\r\naction|html|<b>Vielen Dank, Sie sind nun eingeloggt und haben Ihre E-Mail bestätigt</b>','','','','0','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',24,1,'2016-03-15 01:09:28','2016-03-29 16:50:22','admin','admin',0),
  (6,1,1,1,'','','ycom_auth_form_logout|label|','','','','0','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',15,1,'2016-03-15 08:11:24','2016-03-24 14:48:25','admin','admin',0),
  (7,1,1,1,'Interner Bereich','h1','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',3,2,'2016-03-21 17:34:46','2016-03-21 17:34:46','admin','admin',0),
  (8,2,1,1,'Closed Area','h1','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',3,2,'2016-03-21 17:35:43','2016-03-21 17:35:43','admin','admin',0),
  (10,1,1,1,'','','ycom_auth_load_user|userinfo|email,firstname,name\r\nobjparams|form_showformafterupdate|1\r\nshowvalue|email|E-Mail / Login:\r\ntext|firstname|Vorname:\r\nvalidate|empty|firstname|Bitte geben Sie Ihren Vornamen ein.\r\ntext|name|Nachname:\r\nvalidate|empty|name|Bitte geben Sie Ihren Namen ein.\r\naction|showtext|<div class=\"alert alert-success\">Profildaten wurden aktualisiert</div>|||1\r\naction|ycom_auth_db','','','Ihre Daten wurden aktualisiert','0','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',16,1,'2016-03-22 11:43:52','2016-03-29 15:25:43','admin','admin',0),
  (11,1,1,1,'','','ycom_auth_password|password|Neues Passwort:\r\npassword|password_2|Passwort wiederholen:||no_db\r\nvalidate|empty|password|Bitte geben Sie ein Passwort ein.\r\nvalidate|compare|password|password_2|!=|Bitte geben Sie zweimal das gleiche Passwort ein\r\naction|showtext|<div class=\"alert alert-success\">Ihre Daten wurden aktualisiert. Das neue Passwort ist ab sofort aktiv.</div>|||1\r\naction|ycom_auth_db','','','','0','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',17,1,'2016-03-22 11:44:37','2016-03-29 15:42:19','admin','admin',0);
/*!40000 ALTER TABLE `rex_article_slice` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `rex_assets_sets`;
CREATE TABLE `rex_assets_sets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `files` text NOT NULL,
  `media_query` varchar(255) NOT NULL DEFAULT '',
  `settings` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `rex_clang`;
CREATE TABLE `rex_clang` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `priority` int(10) unsigned NOT NULL,
  `revision` int(10) unsigned NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

LOCK TABLES `rex_clang` WRITE;
/*!40000 ALTER TABLE `rex_clang` DISABLE KEYS */;
INSERT INTO `rex_clang` VALUES 
  (1,'de','deutsch',1,0,1),
  (2,'en','englisch',2,0,1);
/*!40000 ALTER TABLE `rex_clang` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `rex_config`;
CREATE TABLE `rex_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `namespace` varchar(75) NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_key` (`namespace`,`key`)
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=utf8;

LOCK TABLES `rex_config` WRITE;
/*!40000 ALTER TABLE `rex_config` DISABLE KEYS */;
INSERT INTO `rex_config` VALUES 
  (3,'media_manager','jpg_quality','85'),
  (4,'phpmailer','from','\"from@example.com\"'),
  (5,'phpmailer','fromname','\"Mailer\"'),
  (6,'phpmailer','confirmto','\"\"'),
  (7,'phpmailer','bcc','\"\"'),
  (8,'phpmailer','mailer','\"mail\"'),
  (9,'phpmailer','host','\"localhost\"'),
  (10,'phpmailer','port','25'),
  (11,'phpmailer','charset','\"utf-8\"'),
  (12,'phpmailer','wordwrap','120'),
  (13,'phpmailer','encoding','\"8bit\"'),
  (14,'phpmailer','priority','3'),
  (15,'phpmailer','smtpsecure','\"\"'),
  (16,'phpmailer','smtpauth','false'),
  (17,'phpmailer','username','\"\"'),
  (18,'phpmailer','password','\"\"'),
  (58,'core','version','\"5.1.0-dev\"'),
  (88,'ycom','auth_request_name','\"rex_ycom_auth_name\"'),
  (89,'ycom','auth_request_ref','\"rex_ycom_auth_ref\"'),
  (90,'ycom','auth_request_logout','\"rex_ycom_auth_logout\"'),
  (91,'ycom','auth_request_psw','\"rex_ycom_auth_psw\"'),
  (92,'ycom','auth_request_id','\"rex_ycom_auth_id\"'),
  (93,'ycom','auth_cookie_ttl','\"14\"'),
  (104,'ycom','auth_active','1'),
  (105,'ycom','article_id_jump_ok','3'),
  (106,'ycom','article_id_jump_not_ok','12'),
  (107,'ycom','article_id_jump_logout','1'),
  (108,'ycom','article_id_jump_denied','23'),
  (109,'ycom','login_field','\"email\"'),
  (110,'ycom','auth_request_stay','\"rex_ycom_auth_stay\"'),
  (112,'core','package-config','{\"assets\":{\"install\":false,\"status\":false,\"plugins\":{\"cssuglify\":{\"install\":false,\"status\":false},\"jsuglify\":{\"install\":false,\"status\":false},\"lessphp\":{\"install\":false,\"status\":false},\"sassphp\":{\"install\":false,\"status\":false}}},\"aufgaben\":{\"install\":false,\"status\":false},\"awnav\":{\"install\":false,\"status\":false},\"backup\":{\"install\":true,\"status\":true},\"be_style\":{\"install\":true,\"status\":true,\"plugins\":{\"customizer\":{\"install\":false,\"status\":false},\"redaxo\":{\"install\":true,\"status\":true}}},\"cronjob\":{\"install\":false,\"status\":false,\"plugins\":{\"article_status\":{\"install\":false,\"status\":false},\"optimize_tables\":{\"install\":false,\"status\":false}}},\"customelements\":{\"install\":false,\"status\":false},\"debug\":{\"install\":false,\"status\":false},\"demo_base\":{\"install\":false,\"status\":false},\"eloquent\":{\"install\":false,\"status\":false},\"epiceditor\":{\"install\":false,\"status\":false},\"install\":{\"install\":true,\"status\":true},\"media_manager\":{\"install\":true,\"status\":true},\"mediapool\":{\"install\":true,\"status\":true},\"metainfo\":{\"install\":true,\"status\":true},\"phpmailer\":{\"install\":true,\"status\":true},\"rex5_multiupload\":{\"install\":false,\"status\":false},\"rex_markitup\":{\"install\":false,\"status\":false},\"rex_redactor\":{\"install\":false,\"status\":false},\"slice_ui\":{\"install\":false,\"status\":false,\"plugins\":{\"slice_footer\":{\"install\":false,\"status\":false},\"slice_group\":{\"install\":false,\"status\":false},\"slice_json_block\":{\"install\":false,\"status\":false}}},\"structure\":{\"install\":true,\"status\":true,\"plugins\":{\"content\":{\"install\":true,\"status\":true},\"version\":{\"install\":true,\"status\":false}}},\"tests\":{\"install\":false,\"status\":false},\"textile\":{\"install\":true,\"status\":true},\"treestructure\":{\"install\":false,\"status\":false},\"user_agent\":{\"install\":false,\"status\":false},\"users\":{\"install\":true,\"status\":true},\"watson\":{\"install\":false,\"status\":false},\"wildcard\":{\"install\":false,\"status\":false},\"ycom\":{\"install\":true,\"status\":true,\"plugins\":{\"auth\":{\"install\":true,\"status\":true},\"group\":{\"install\":true,\"status\":true}}},\"yform\":{\"install\":true,\"status\":true,\"plugins\":{\"email\":{\"install\":true,\"status\":true},\"geo\":{\"install\":false,\"status\":false},\"manager\":{\"install\":true,\"status\":true}}},\"yrewrite\":{\"install\":true,\"status\":true}}'),
  (113,'core','package-order','[\"be_style\",\"be_style\\/redaxo\",\"users\",\"backup\",\"install\",\"media_manager\",\"mediapool\",\"phpmailer\",\"structure\",\"textile\",\"metainfo\",\"structure\\/content\",\"ycom\",\"ycom\\/auth\",\"ycom\\/group\",\"yform\",\"yform\\/email\",\"yform\\/manager\",\"yrewrite\"]');
/*!40000 ALTER TABLE `rex_config` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `rex_cronjob`;
CREATE TABLE `rex_cronjob` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `parameters` text,
  `interval` varchar(255) DEFAULT NULL,
  `nexttime` datetime NOT NULL,
  `environment` varchar(255) NOT NULL,
  `execution_moment` tinyint(1) NOT NULL,
  `execution_start` datetime NOT NULL,
  `status` tinyint(1) NOT NULL,
  `createdate` datetime NOT NULL,
  `createuser` varchar(255) NOT NULL,
  `updatedate` datetime NOT NULL,
  `updateuser` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

LOCK TABLES `rex_cronjob` WRITE;
/*!40000 ALTER TABLE `rex_cronjob` DISABLE KEYS */;
INSERT INTO `rex_cronjob` VALUES 
  (1,'test','','rex_cronjob_phpcode','{\"rex_cronjob_phpcode_code\":\"\"}','|1|y|','2016-01-01 00:00:00','|0|1|',0,'0000-00-00 00:00:00',1,'2015-04-24 13:07:45','admin','2015-04-24 14:27:56','admin'),
  (2,'Artikel-Status','','rex_cronjob_article_status','','|1|d|','0000-00-00 00:00:00','|0|1|',1,'0000-00-00 00:00:00',0,'2015-04-24 14:44:53','admin','2015-04-24 14:44:53','admin'),
  (3,'Tabellen-Optimierung','','rex_cronjob_optimize_tables','','|1|d|','0000-00-00 00:00:00','|0|1|',0,'0000-00-00 00:00:00',0,'2015-04-24 14:46:29','admin','2015-04-24 14:46:29','admin'),
  (4,'Update Facebook','Prüft ob neue Facebookeinträge auf den angegebenen Seiten vorhanden sind.','rex_cronjob_phpcallback','{\"rex_cronjob_phpcallback_callback\":\"socialhub_facebook::cron()\"}','|1|h|','0000-00-00 00:00:00','|0|1|',0,'2016-01-12 01:00:00',1,'2016-01-12 23:05:43','admin','2016-01-12 23:05:43','admin');
/*!40000 ALTER TABLE `rex_cronjob` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `rex_media`;
CREATE TABLE `rex_media` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL,
  `attributes` text,
  `filetype` varchar(255) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `originalname` varchar(255) DEFAULT NULL,
  `filesize` varchar(255) DEFAULT NULL,
  `width` int(10) unsigned DEFAULT NULL,
  `height` int(10) unsigned DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `createdate` datetime NOT NULL,
  `updatedate` datetime NOT NULL,
  `createuser` varchar(255) NOT NULL,
  `updateuser` varchar(255) NOT NULL,
  `revision` int(10) unsigned NOT NULL,
  `med_description` text,
  `med_copyright` text,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `rex_media_category`;
CREATE TABLE `rex_media_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `parent_id` int(10) unsigned NOT NULL,
  `path` varchar(255) NOT NULL,
  `createdate` datetime NOT NULL,
  `updatedate` datetime NOT NULL,
  `createuser` varchar(255) NOT NULL,
  `updateuser` varchar(255) NOT NULL,
  `attributes` text,
  `revision` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `rex_media_manager_type`;
CREATE TABLE `rex_media_manager_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `status` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

LOCK TABLES `rex_media_manager_type` WRITE;
/*!40000 ALTER TABLE `rex_media_manager_type` DISABLE KEYS */;
INSERT INTO `rex_media_manager_type` VALUES 
  (1,1,'rex_mediapool_detail','Zur Darstellung von Bildern in der Detailansicht im Medienpool'),
  (2,1,'rex_mediapool_maximized','Zur Darstellung von Bildern im Medienpool wenn maximiert'),
  (3,1,'rex_mediapool_preview','Zur Darstellung der Vorschaubilder im Medienpool'),
  (4,1,'rex_mediabutton_preview','Zur Darstellung der Vorschaubilder in REX_MEDIA_BUTTON[]s'),
  (5,1,'rex_medialistbutton_preview','Zur Darstellung der Vorschaubilder in REX_MEDIALIST_BUTTON[]s');
/*!40000 ALTER TABLE `rex_media_manager_type` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `rex_media_manager_type_effect`;
CREATE TABLE `rex_media_manager_type_effect` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` int(10) unsigned NOT NULL,
  `effect` varchar(255) NOT NULL,
  `parameters` text NOT NULL,
  `priority` int(10) unsigned NOT NULL,
  `updatedate` datetime NOT NULL,
  `updateuser` varchar(255) NOT NULL,
  `createdate` datetime NOT NULL,
  `createuser` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

LOCK TABLES `rex_media_manager_type_effect` WRITE;
/*!40000 ALTER TABLE `rex_media_manager_type_effect` DISABLE KEYS */;
INSERT INTO `rex_media_manager_type_effect` VALUES 
  (1,1,'resize','{\"rex_effect_crop\":{\"rex_effect_crop_width\":\"\",\"rex_effect_crop_height\":\"\",\"rex_effect_crop_offset_width\":\"\",\"rex_effect_crop_offset_height\":\"\",\"rex_effect_crop_hpos\":\"center\",\"rex_effect_crop_vpos\":\"middle\"},\"rex_effect_filter_blur\":{\"rex_effect_filter_blur_amount\":\"80\",\"rex_effect_filter_blur_radius\":\"8\",\"rex_effect_filter_blur_threshold\":\"3\"},\"rex_effect_filter_sharpen\":{\"rex_effect_filter_sharpen_amount\":\"80\",\"rex_effect_filter_sharpen_radius\":\"0.5\",\"rex_effect_filter_sharpen_threshold\":\"3\"},\"rex_effect_flip\":{\"rex_effect_flip_flip\":\"X\"},\"rex_effect_header\":{\"rex_effect_header_download\":\"open_media\",\"rex_effect_header_cache\":\"no_cache\"},\"rex_effect_insert_image\":{\"rex_effect_insert_image_brandimage\":\"\",\"rex_effect_insert_image_hpos\":\"left\",\"rex_effect_insert_image_vpos\":\"top\",\"rex_effect_insert_image_padding_x\":\"-10\",\"rex_effect_insert_image_padding_y\":\"-10\"},\"rex_effect_mediapath\":{\"rex_effect_mediapath_mediapath\":\"\"},\"rex_effect_mirror\":{\"rex_effect_mirror_height\":\"\",\"rex_effect_mirror_set_transparent\":\"colored\",\"rex_effect_mirror_bg_r\":\"\",\"rex_effect_mirror_bg_g\":\"\",\"rex_effect_mirror_bg_b\":\"\"},\"rex_effect_resize\":{\"rex_effect_resize_width\":\"200\",\"rex_effect_resize_height\":\"200\",\"rex_effect_resize_style\":\"maximum\",\"rex_effect_resize_allow_enlarge\":\"not_enlarge\"},\"rex_effect_workspace\":{\"rex_effect_workspace_width\":\"\",\"rex_effect_workspace_height\":\"\",\"rex_effect_workspace_hpos\":\"left\",\"rex_effect_workspace_vpos\":\"top\",\"rex_effect_workspace_set_transparent\":\"colored\",\"rex_effect_workspace_bg_r\":\"\",\"rex_effect_workspace_bg_g\":\"\",\"rex_effect_workspace_bg_b\":\"\"}}',1,'0000-00-00 00:00:00','','0000-00-00 00:00:00',''),
  (2,2,'resize','{\"rex_effect_crop\":{\"rex_effect_crop_width\":\"\",\"rex_effect_crop_height\":\"\",\"rex_effect_crop_offset_width\":\"\",\"rex_effect_crop_offset_height\":\"\",\"rex_effect_crop_hpos\":\"center\",\"rex_effect_crop_vpos\":\"middle\"},\"rex_effect_filter_blur\":{\"rex_effect_filter_blur_amount\":\"80\",\"rex_effect_filter_blur_radius\":\"8\",\"rex_effect_filter_blur_threshold\":\"3\"},\"rex_effect_filter_sharpen\":{\"rex_effect_filter_sharpen_amount\":\"80\",\"rex_effect_filter_sharpen_radius\":\"0.5\",\"rex_effect_filter_sharpen_threshold\":\"3\"},\"rex_effect_flip\":{\"rex_effect_flip_flip\":\"X\"},\"rex_effect_header\":{\"rex_effect_header_download\":\"open_media\",\"rex_effect_header_cache\":\"no_cache\"},\"rex_effect_insert_image\":{\"rex_effect_insert_image_brandimage\":\"\",\"rex_effect_insert_image_hpos\":\"left\",\"rex_effect_insert_image_vpos\":\"top\",\"rex_effect_insert_image_padding_x\":\"-10\",\"rex_effect_insert_image_padding_y\":\"-10\"},\"rex_effect_mediapath\":{\"rex_effect_mediapath_mediapath\":\"\"},\"rex_effect_mirror\":{\"rex_effect_mirror_height\":\"\",\"rex_effect_mirror_set_transparent\":\"colored\",\"rex_effect_mirror_bg_r\":\"\",\"rex_effect_mirror_bg_g\":\"\",\"rex_effect_mirror_bg_b\":\"\"},\"rex_effect_resize\":{\"rex_effect_resize_width\":\"600\",\"rex_effect_resize_height\":\"600\",\"rex_effect_resize_style\":\"maximum\",\"rex_effect_resize_allow_enlarge\":\"not_enlarge\"},\"rex_effect_workspace\":{\"rex_effect_workspace_width\":\"\",\"rex_effect_workspace_height\":\"\",\"rex_effect_workspace_hpos\":\"left\",\"rex_effect_workspace_vpos\":\"top\",\"rex_effect_workspace_set_transparent\":\"colored\",\"rex_effect_workspace_bg_r\":\"\",\"rex_effect_workspace_bg_g\":\"\",\"rex_effect_workspace_bg_b\":\"\"}}',1,'0000-00-00 00:00:00','','0000-00-00 00:00:00',''),
  (3,3,'resize','{\"rex_effect_crop\":{\"rex_effect_crop_width\":\"\",\"rex_effect_crop_height\":\"\",\"rex_effect_crop_offset_width\":\"\",\"rex_effect_crop_offset_height\":\"\",\"rex_effect_crop_hpos\":\"center\",\"rex_effect_crop_vpos\":\"middle\"},\"rex_effect_filter_blur\":{\"rex_effect_filter_blur_amount\":\"80\",\"rex_effect_filter_blur_radius\":\"8\",\"rex_effect_filter_blur_threshold\":\"3\"},\"rex_effect_filter_sharpen\":{\"rex_effect_filter_sharpen_amount\":\"80\",\"rex_effect_filter_sharpen_radius\":\"0.5\",\"rex_effect_filter_sharpen_threshold\":\"3\"},\"rex_effect_flip\":{\"rex_effect_flip_flip\":\"X\"},\"rex_effect_header\":{\"rex_effect_header_download\":\"open_media\",\"rex_effect_header_cache\":\"no_cache\"},\"rex_effect_insert_image\":{\"rex_effect_insert_image_brandimage\":\"\",\"rex_effect_insert_image_hpos\":\"left\",\"rex_effect_insert_image_vpos\":\"top\",\"rex_effect_insert_image_padding_x\":\"-10\",\"rex_effect_insert_image_padding_y\":\"-10\"},\"rex_effect_mediapath\":{\"rex_effect_mediapath_mediapath\":\"\"},\"rex_effect_mirror\":{\"rex_effect_mirror_height\":\"\",\"rex_effect_mirror_set_transparent\":\"colored\",\"rex_effect_mirror_bg_r\":\"\",\"rex_effect_mirror_bg_g\":\"\",\"rex_effect_mirror_bg_b\":\"\"},\"rex_effect_resize\":{\"rex_effect_resize_width\":\"80\",\"rex_effect_resize_height\":\"80\",\"rex_effect_resize_style\":\"maximum\",\"rex_effect_resize_allow_enlarge\":\"not_enlarge\"},\"rex_effect_workspace\":{\"rex_effect_workspace_width\":\"\",\"rex_effect_workspace_height\":\"\",\"rex_effect_workspace_hpos\":\"left\",\"rex_effect_workspace_vpos\":\"top\",\"rex_effect_workspace_set_transparent\":\"colored\",\"rex_effect_workspace_bg_r\":\"\",\"rex_effect_workspace_bg_g\":\"\",\"rex_effect_workspace_bg_b\":\"\"}}',1,'0000-00-00 00:00:00','','0000-00-00 00:00:00',''),
  (4,4,'resize','{\"rex_effect_crop\":{\"rex_effect_crop_width\":\"\",\"rex_effect_crop_height\":\"\",\"rex_effect_crop_offset_width\":\"\",\"rex_effect_crop_offset_height\":\"\",\"rex_effect_crop_hpos\":\"center\",\"rex_effect_crop_vpos\":\"middle\"},\"rex_effect_filter_blur\":{\"rex_effect_filter_blur_amount\":\"80\",\"rex_effect_filter_blur_radius\":\"8\",\"rex_effect_filter_blur_threshold\":\"3\"},\"rex_effect_filter_sharpen\":{\"rex_effect_filter_sharpen_amount\":\"80\",\"rex_effect_filter_sharpen_radius\":\"0.5\",\"rex_effect_filter_sharpen_threshold\":\"3\"},\"rex_effect_flip\":{\"rex_effect_flip_flip\":\"X\"},\"rex_effect_header\":{\"rex_effect_header_download\":\"open_media\",\"rex_effect_header_cache\":\"no_cache\"},\"rex_effect_insert_image\":{\"rex_effect_insert_image_brandimage\":\"\",\"rex_effect_insert_image_hpos\":\"left\",\"rex_effect_insert_image_vpos\":\"top\",\"rex_effect_insert_image_padding_x\":\"-10\",\"rex_effect_insert_image_padding_y\":\"-10\"},\"rex_effect_mediapath\":{\"rex_effect_mediapath_mediapath\":\"\"},\"rex_effect_mirror\":{\"rex_effect_mirror_height\":\"\",\"rex_effect_mirror_set_transparent\":\"colored\",\"rex_effect_mirror_bg_r\":\"\",\"rex_effect_mirror_bg_g\":\"\",\"rex_effect_mirror_bg_b\":\"\"},\"rex_effect_resize\":{\"rex_effect_resize_width\":\"246\",\"rex_effect_resize_height\":\"246\",\"rex_effect_resize_style\":\"maximum\",\"rex_effect_resize_allow_enlarge\":\"not_enlarge\"},\"rex_effect_workspace\":{\"rex_effect_workspace_width\":\"\",\"rex_effect_workspace_height\":\"\",\"rex_effect_workspace_hpos\":\"left\",\"rex_effect_workspace_vpos\":\"top\",\"rex_effect_workspace_set_transparent\":\"colored\",\"rex_effect_workspace_bg_r\":\"\",\"rex_effect_workspace_bg_g\":\"\",\"rex_effect_workspace_bg_b\":\"\"}}',1,'0000-00-00 00:00:00','','0000-00-00 00:00:00',''),
  (5,5,'resize','{\"rex_effect_crop\":{\"rex_effect_crop_width\":\"\",\"rex_effect_crop_height\":\"\",\"rex_effect_crop_offset_width\":\"\",\"rex_effect_crop_offset_height\":\"\",\"rex_effect_crop_hpos\":\"center\",\"rex_effect_crop_vpos\":\"middle\"},\"rex_effect_filter_blur\":{\"rex_effect_filter_blur_amount\":\"80\",\"rex_effect_filter_blur_radius\":\"8\",\"rex_effect_filter_blur_threshold\":\"3\"},\"rex_effect_filter_sharpen\":{\"rex_effect_filter_sharpen_amount\":\"80\",\"rex_effect_filter_sharpen_radius\":\"0.5\",\"rex_effect_filter_sharpen_threshold\":\"3\"},\"rex_effect_flip\":{\"rex_effect_flip_flip\":\"X\"},\"rex_effect_header\":{\"rex_effect_header_download\":\"open_media\",\"rex_effect_header_cache\":\"no_cache\"},\"rex_effect_insert_image\":{\"rex_effect_insert_image_brandimage\":\"\",\"rex_effect_insert_image_hpos\":\"left\",\"rex_effect_insert_image_vpos\":\"top\",\"rex_effect_insert_image_padding_x\":\"-10\",\"rex_effect_insert_image_padding_y\":\"-10\"},\"rex_effect_mediapath\":{\"rex_effect_mediapath_mediapath\":\"\"},\"rex_effect_mirror\":{\"rex_effect_mirror_height\":\"\",\"rex_effect_mirror_set_transparent\":\"colored\",\"rex_effect_mirror_bg_r\":\"\",\"rex_effect_mirror_bg_g\":\"\",\"rex_effect_mirror_bg_b\":\"\"},\"rex_effect_resize\":{\"rex_effect_resize_width\":\"246\",\"rex_effect_resize_height\":\"246\",\"rex_effect_resize_style\":\"maximum\",\"rex_effect_resize_allow_enlarge\":\"not_enlarge\"},\"rex_effect_workspace\":{\"rex_effect_workspace_width\":\"\",\"rex_effect_workspace_height\":\"\",\"rex_effect_workspace_hpos\":\"left\",\"rex_effect_workspace_vpos\":\"top\",\"rex_effect_workspace_set_transparent\":\"colored\",\"rex_effect_workspace_bg_r\":\"\",\"rex_effect_workspace_bg_g\":\"\",\"rex_effect_workspace_bg_b\":\"\"}}',1,'0000-00-00 00:00:00','','0000-00-00 00:00:00','');
/*!40000 ALTER TABLE `rex_media_manager_type_effect` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `rex_media_manager_type_effects`;
CREATE TABLE `rex_media_manager_type_effects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) NOT NULL,
  `effect` varchar(255) NOT NULL,
  `parameters` text NOT NULL,
  `prior` int(11) NOT NULL,
  `updatedate` int(11) NOT NULL,
  `updateuser` varchar(255) NOT NULL,
  `createdate` int(11) NOT NULL,
  `createuser` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

LOCK TABLES `rex_media_manager_type_effects` WRITE;
/*!40000 ALTER TABLE `rex_media_manager_type_effects` DISABLE KEYS */;
INSERT INTO `rex_media_manager_type_effects` VALUES 
  (1,1,'resize','{\"rex_effect_crop\":{\"rex_effect_crop_width\":\"\",\"rex_effect_crop_height\":\"\",\"rex_effect_crop_offset_width\":\"\",\"rex_effect_crop_offset_height\":\"\",\"rex_effect_crop_hpos\":\"center\",\"rex_effect_crop_vpos\":\"middle\"},\"rex_effect_filter_blur\":{\"rex_effect_filter_blur_amount\":\"80\",\"rex_effect_filter_blur_radius\":\"8\",\"rex_effect_filter_blur_threshold\":\"3\"},\"rex_effect_filter_sharpen\":{\"rex_effect_filter_sharpen_amount\":\"80\",\"rex_effect_filter_sharpen_radius\":\"0.5\",\"rex_effect_filter_sharpen_threshold\":\"3\"},\"rex_effect_flip\":{\"rex_effect_flip_flip\":\"X\"},\"rex_effect_header\":{\"rex_effect_header_download\":\"open_media\",\"rex_effect_header_cache\":\"no_cache\"},\"rex_effect_insert_image\":{\"rex_effect_insert_image_brandimage\":\"\",\"rex_effect_insert_image_hpos\":\"left\",\"rex_effect_insert_image_vpos\":\"top\",\"rex_effect_insert_image_padding_x\":\"-10\",\"rex_effect_insert_image_padding_y\":\"-10\"},\"rex_effect_mediapath\":{\"rex_effect_mediapath_mediapath\":\"\"},\"rex_effect_mirror\":{\"rex_effect_mirror_height\":\"\",\"rex_effect_mirror_set_transparent\":\"colored\",\"rex_effect_mirror_bg_r\":\"\",\"rex_effect_mirror_bg_g\":\"\",\"rex_effect_mirror_bg_b\":\"\"},\"rex_effect_resize\":{\"rex_effect_resize_width\":\"200\",\"rex_effect_resize_height\":\"200\",\"rex_effect_resize_style\":\"maximum\",\"rex_effect_resize_allow_enlarge\":\"not_enlarge\"},\"rex_effect_workspace\":{\"rex_effect_workspace_width\":\"\",\"rex_effect_workspace_height\":\"\",\"rex_effect_workspace_hpos\":\"left\",\"rex_effect_workspace_vpos\":\"top\",\"rex_effect_workspace_set_transparent\":\"colored\",\"rex_effect_workspace_bg_r\":\"\",\"rex_effect_workspace_bg_g\":\"\",\"rex_effect_workspace_bg_b\":\"\"}}',1,1319833448,'admin',1319833448,'admin'),
  (2,2,'resize','{\"rex_effect_crop\":{\"rex_effect_crop_width\":\"\",\"rex_effect_crop_height\":\"\",\"rex_effect_crop_offset_width\":\"\",\"rex_effect_crop_offset_height\":\"\",\"rex_effect_crop_hpos\":\"center\",\"rex_effect_crop_vpos\":\"middle\"},\"rex_effect_filter_blur\":{\"rex_effect_filter_blur_amount\":\"80\",\"rex_effect_filter_blur_radius\":\"8\",\"rex_effect_filter_blur_threshold\":\"3\"},\"rex_effect_filter_sharpen\":{\"rex_effect_filter_sharpen_amount\":\"80\",\"rex_effect_filter_sharpen_radius\":\"0.5\",\"rex_effect_filter_sharpen_threshold\":\"3\"},\"rex_effect_flip\":{\"rex_effect_flip_flip\":\"X\"},\"rex_effect_header\":{\"rex_effect_header_download\":\"open_media\",\"rex_effect_header_cache\":\"no_cache\"},\"rex_effect_insert_image\":{\"rex_effect_insert_image_brandimage\":\"\",\"rex_effect_insert_image_hpos\":\"left\",\"rex_effect_insert_image_vpos\":\"top\",\"rex_effect_insert_image_padding_x\":\"-10\",\"rex_effect_insert_image_padding_y\":\"-10\"},\"rex_effect_mediapath\":{\"rex_effect_mediapath_mediapath\":\"\"},\"rex_effect_mirror\":{\"rex_effect_mirror_height\":\"\",\"rex_effect_mirror_set_transparent\":\"colored\",\"rex_effect_mirror_bg_r\":\"\",\"rex_effect_mirror_bg_g\":\"\",\"rex_effect_mirror_bg_b\":\"\"},\"rex_effect_resize\":{\"rex_effect_resize_width\":\"600\",\"rex_effect_resize_height\":\"600\",\"rex_effect_resize_style\":\"maximum\",\"rex_effect_resize_allow_enlarge\":\"not_enlarge\"},\"rex_effect_workspace\":{\"rex_effect_workspace_width\":\"\",\"rex_effect_workspace_height\":\"\",\"rex_effect_workspace_hpos\":\"left\",\"rex_effect_workspace_vpos\":\"top\",\"rex_effect_workspace_set_transparent\":\"colored\",\"rex_effect_workspace_bg_r\":\"\",\"rex_effect_workspace_bg_g\":\"\",\"rex_effect_workspace_bg_b\":\"\"}}',1,1319833472,'admin',1319833448,'admin'),
  (3,3,'resize','{\"rex_effect_crop\":{\"rex_effect_crop_width\":\"\",\"rex_effect_crop_height\":\"\",\"rex_effect_crop_offset_width\":\"\",\"rex_effect_crop_offset_height\":\"\",\"rex_effect_crop_hpos\":\"center\",\"rex_effect_crop_vpos\":\"middle\"},\"rex_effect_filter_blur\":{\"rex_effect_filter_blur_amount\":\"80\",\"rex_effect_filter_blur_radius\":\"8\",\"rex_effect_filter_blur_threshold\":\"3\"},\"rex_effect_filter_sharpen\":{\"rex_effect_filter_sharpen_amount\":\"80\",\"rex_effect_filter_sharpen_radius\":\"0.5\",\"rex_effect_filter_sharpen_threshold\":\"3\"},\"rex_effect_flip\":{\"rex_effect_flip_flip\":\"X\"},\"rex_effect_header\":{\"rex_effect_header_download\":\"open_media\",\"rex_effect_header_cache\":\"no_cache\"},\"rex_effect_insert_image\":{\"rex_effect_insert_image_brandimage\":\"\",\"rex_effect_insert_image_hpos\":\"left\",\"rex_effect_insert_image_vpos\":\"top\",\"rex_effect_insert_image_padding_x\":\"-10\",\"rex_effect_insert_image_padding_y\":\"-10\"},\"rex_effect_mediapath\":{\"rex_effect_mediapath_mediapath\":\"\"},\"rex_effect_mirror\":{\"rex_effect_mirror_height\":\"\",\"rex_effect_mirror_set_transparent\":\"colored\",\"rex_effect_mirror_bg_r\":\"\",\"rex_effect_mirror_bg_g\":\"\",\"rex_effect_mirror_bg_b\":\"\"},\"rex_effect_resize\":{\"rex_effect_resize_width\":\"80\",\"rex_effect_resize_height\":\"80\",\"rex_effect_resize_style\":\"maximum\",\"rex_effect_resize_allow_enlarge\":\"not_enlarge\"},\"rex_effect_workspace\":{\"rex_effect_workspace_width\":\"\",\"rex_effect_workspace_height\":\"\",\"rex_effect_workspace_hpos\":\"left\",\"rex_effect_workspace_vpos\":\"top\",\"rex_effect_workspace_set_transparent\":\"colored\",\"rex_effect_workspace_bg_r\":\"\",\"rex_effect_workspace_bg_g\":\"\",\"rex_effect_workspace_bg_b\":\"\"}}',1,1319833496,'admin',1319833448,'admin'),
  (4,4,'resize','{\"rex_effect_crop\":{\"rex_effect_crop_width\":\"\",\"rex_effect_crop_height\":\"\",\"rex_effect_crop_offset_width\":\"\",\"rex_effect_crop_offset_height\":\"\",\"rex_effect_crop_hpos\":\"center\",\"rex_effect_crop_vpos\":\"middle\"},\"rex_effect_filter_blur\":{\"rex_effect_filter_blur_amount\":\"80\",\"rex_effect_filter_blur_radius\":\"8\",\"rex_effect_filter_blur_threshold\":\"3\"},\"rex_effect_filter_sharpen\":{\"rex_effect_filter_sharpen_amount\":\"80\",\"rex_effect_filter_sharpen_radius\":\"0.5\",\"rex_effect_filter_sharpen_threshold\":\"3\"},\"rex_effect_flip\":{\"rex_effect_flip_flip\":\"X\"},\"rex_effect_header\":{\"rex_effect_header_download\":\"open_media\",\"rex_effect_header_cache\":\"no_cache\"},\"rex_effect_insert_image\":{\"rex_effect_insert_image_brandimage\":\"\",\"rex_effect_insert_image_hpos\":\"left\",\"rex_effect_insert_image_vpos\":\"top\",\"rex_effect_insert_image_padding_x\":\"-10\",\"rex_effect_insert_image_padding_y\":\"-10\"},\"rex_effect_mediapath\":{\"rex_effect_mediapath_mediapath\":\"\"},\"rex_effect_mirror\":{\"rex_effect_mirror_height\":\"\",\"rex_effect_mirror_set_transparent\":\"colored\",\"rex_effect_mirror_bg_r\":\"\",\"rex_effect_mirror_bg_g\":\"\",\"rex_effect_mirror_bg_b\":\"\"},\"rex_effect_resize\":{\"rex_effect_resize_width\":\"246\",\"rex_effect_resize_height\":\"246\",\"rex_effect_resize_style\":\"maximum\",\"rex_effect_resize_allow_enlarge\":\"not_enlarge\"},\"rex_effect_workspace\":{\"rex_effect_workspace_width\":\"\",\"rex_effect_workspace_height\":\"\",\"rex_effect_workspace_hpos\":\"left\",\"rex_effect_workspace_vpos\":\"top\",\"rex_effect_workspace_set_transparent\":\"colored\",\"rex_effect_workspace_bg_r\":\"\",\"rex_effect_workspace_bg_g\":\"\",\"rex_effect_workspace_bg_b\":\"\"}}',1,1319833418,'admin',1319833448,'admin'),
  (5,5,'resize','{\"rex_effect_crop\":{\"rex_effect_crop_width\":\"\",\"rex_effect_crop_height\":\"\",\"rex_effect_crop_offset_width\":\"\",\"rex_effect_crop_offset_height\":\"\",\"rex_effect_crop_hpos\":\"center\",\"rex_effect_crop_vpos\":\"middle\"},\"rex_effect_filter_blur\":{\"rex_effect_filter_blur_amount\":\"80\",\"rex_effect_filter_blur_radius\":\"8\",\"rex_effect_filter_blur_threshold\":\"3\"},\"rex_effect_filter_sharpen\":{\"rex_effect_filter_sharpen_amount\":\"80\",\"rex_effect_filter_sharpen_radius\":\"0.5\",\"rex_effect_filter_sharpen_threshold\":\"3\"},\"rex_effect_flip\":{\"rex_effect_flip_flip\":\"X\"},\"rex_effect_header\":{\"rex_effect_header_download\":\"open_media\",\"rex_effect_header_cache\":\"no_cache\"},\"rex_effect_insert_image\":{\"rex_effect_insert_image_brandimage\":\"\",\"rex_effect_insert_image_hpos\":\"left\",\"rex_effect_insert_image_vpos\":\"top\",\"rex_effect_insert_image_padding_x\":\"-10\",\"rex_effect_insert_image_padding_y\":\"-10\"},\"rex_effect_mediapath\":{\"rex_effect_mediapath_mediapath\":\"\"},\"rex_effect_mirror\":{\"rex_effect_mirror_height\":\"\",\"rex_effect_mirror_set_transparent\":\"colored\",\"rex_effect_mirror_bg_r\":\"\",\"rex_effect_mirror_bg_g\":\"\",\"rex_effect_mirror_bg_b\":\"\"},\"rex_effect_resize\":{\"rex_effect_resize_width\":\"246\",\"rex_effect_resize_height\":\"246\",\"rex_effect_resize_style\":\"maximum\",\"rex_effect_resize_allow_enlarge\":\"not_enlarge\"},\"rex_effect_workspace\":{\"rex_effect_workspace_width\":\"\",\"rex_effect_workspace_height\":\"\",\"rex_effect_workspace_hpos\":\"left\",\"rex_effect_workspace_vpos\":\"top\",\"rex_effect_workspace_set_transparent\":\"colored\",\"rex_effect_workspace_bg_r\":\"\",\"rex_effect_workspace_bg_g\":\"\",\"rex_effect_workspace_bg_b\":\"\"}}',1,1319833532,'admin',1319833448,'admin'),
  (6,6,'resize','{\"rex_effect_crop\":{\"rex_effect_crop_width\":\"\",\"rex_effect_crop_height\":\"\",\"rex_effect_crop_offset_width\":\"\",\"rex_effect_crop_offset_height\":\"\",\"rex_effect_crop_hpos\":\"center\",\"rex_effect_crop_vpos\":\"middle\"},\"rex_effect_filter_blur\":{\"rex_effect_filter_blur_amount\":\"80\",\"rex_effect_filter_blur_radius\":\"8\",\"rex_effect_filter_blur_threshold\":\"3\"},\"rex_effect_filter_sharpen\":{\"rex_effect_filter_sharpen_amount\":\"80\",\"rex_effect_filter_sharpen_radius\":\"0.5\",\"rex_effect_filter_sharpen_threshold\":\"3\"},\"rex_effect_flip\":{\"rex_effect_flip_flip\":\"X\"},\"rex_effect_header\":{\"rex_effect_header_download\":\"open_media\",\"rex_effect_header_cache\":\"no_cache\"},\"rex_effect_insert_image\":{\"rex_effect_insert_image_brandimage\":\"\",\"rex_effect_insert_image_hpos\":\"left\",\"rex_effect_insert_image_vpos\":\"top\",\"rex_effect_insert_image_padding_x\":\"-10\",\"rex_effect_insert_image_padding_y\":\"-10\"},\"rex_effect_mediapath\":{\"rex_effect_mediapath_mediapath\":\"\"},\"rex_effect_mirror\":{\"rex_effect_mirror_height\":\"\",\"rex_effect_mirror_set_transparent\":\"colored\",\"rex_effect_mirror_bg_r\":\"\",\"rex_effect_mirror_bg_g\":\"\",\"rex_effect_mirror_bg_b\":\"\"},\"rex_effect_resize\":{\"rex_effect_resize_width\":\"250\",\"rex_effect_resize_height\":\"\",\"rex_effect_resize_style\":\"maximum\",\"rex_effect_resize_allow_enlarge\":\"not_enlarge\"},\"rex_effect_workspace\":{\"rex_effect_workspace_width\":\"\",\"rex_effect_workspace_height\":\"\",\"rex_effect_workspace_hpos\":\"left\",\"rex_effect_workspace_vpos\":\"top\",\"rex_effect_workspace_set_transparent\":\"colored\",\"rex_effect_workspace_bg_r\":\"\",\"rex_effect_workspace_bg_g\":\"\",\"rex_effect_workspace_bg_b\":\"\"}}',1,1324160022,'admin',1324160022,'admin');
/*!40000 ALTER TABLE `rex_media_manager_type_effects` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `rex_media_manager_types`;
CREATE TABLE `rex_media_manager_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

LOCK TABLES `rex_media_manager_types` WRITE;
/*!40000 ALTER TABLE `rex_media_manager_types` DISABLE KEYS */;
INSERT INTO `rex_media_manager_types` VALUES 
  (1,1,'rex_mediapool_detail','Zur Darstellung von Bildern in der Detailansicht im Medienpool'),
  (2,1,'rex_mediapool_maximized','Zur Darstellung von Bildern im Medienpool wenn maximiert'),
  (3,1,'rex_mediapool_preview','Zur Darstellung der Vorschaubilder im Medienpool'),
  (4,1,'rex_mediabutton_preview','Zur Darstellung der Vorschaubilder in REX_MEDIA_BUTTON[]s'),
  (5,1,'rex_medialistbutton_preview','Zur Darstellung der Vorschaubilder in REX_MEDIALIST_BUTTON[]s'),
  (6,0,'gallery_overview','Zur Anzeige der Screenshot-Gallerie');
/*!40000 ALTER TABLE `rex_media_manager_types` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `rex_metainfo_field`;
CREATE TABLE `rex_metainfo_field` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `priority` int(10) unsigned NOT NULL,
  `attributes` text NOT NULL,
  `type_id` int(10) unsigned DEFAULT NULL,
  `default` varchar(255) NOT NULL,
  `params` text,
  `validate` text,
  `callback` text,
  `restrictions` text,
  `createuser` varchar(255) NOT NULL,
  `createdate` datetime NOT NULL,
  `updateuser` varchar(255) NOT NULL,
  `updatedate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

LOCK TABLES `rex_metainfo_field` WRITE;
/*!40000 ALTER TABLE `rex_metainfo_field` DISABLE KEYS */;
INSERT INTO `rex_metainfo_field` VALUES 
  (1,'translate:pool_file_description','med_description',1,'',2,'','','','','','','0000-00-00 00:00:00','','0000-00-00 00:00:00'),
  (2,'translate:pool_file_copyright','med_copyright',2,'',1,'','','','','','','0000-00-00 00:00:00','','0000-00-00 00:00:00'),
  (3,'translate:online_from','art_online_from',1,'',10,'','','','','','','0000-00-00 00:00:00','','0000-00-00 00:00:00'),
  (4,'translate:online_to','art_online_to',2,'',10,'','','','','','','0000-00-00 00:00:00','','0000-00-00 00:00:00'),
  (5,'translate:description','art_description',3,'',2,'','','','','','','0000-00-00 00:00:00','','0000-00-00 00:00:00'),
  (6,'translate:keywords','art_keywords',4,'',2,'','','','','','','0000-00-00 00:00:00','','0000-00-00 00:00:00'),
  (7,'translate:metadata_image','art_file',5,'',6,'','','','','','','0000-00-00 00:00:00','','0000-00-00 00:00:00'),
  (8,'translate:teaser','art_teaser',6,'',5,'','','','','','','0000-00-00 00:00:00','','0000-00-00 00:00:00'),
  (9,'translate:header_article_type','art_type_id',7,'size=1',3,'','Standard|Zugriff fuer alle','','','','','0000-00-00 00:00:00','','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `rex_metainfo_field` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `rex_metainfo_params`;
CREATE TABLE `rex_metainfo_params` (
  `field_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `prior` int(10) unsigned NOT NULL,
  `attributes` text NOT NULL,
  `type` int(10) unsigned DEFAULT NULL,
  `default` varchar(255) NOT NULL,
  `params` text,
  `validate` text,
  `callback` text,
  `restrictions` text,
  `createuser` varchar(255) NOT NULL,
  `createdate` int(11) NOT NULL,
  `updateuser` varchar(255) NOT NULL,
  `updatedate` int(11) NOT NULL,
  PRIMARY KEY (`field_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

LOCK TABLES `rex_metainfo_params` WRITE;
/*!40000 ALTER TABLE `rex_metainfo_params` DISABLE KEYS */;
INSERT INTO `rex_metainfo_params` VALUES 
  (1,'translate:pool_file_description','med_description',1,'',2,'','','','','','admin',1189343866,'admin',1189344596),
  (2,'translate:pool_file_copyright','med_copyright',2,'',1,'','','','','','admin',1189343877,'admin',1189344617),
  (3,'translate:online_from','art_online_from',1,'',10,'','','','','','admin',1189344934,'admin',1189344934),
  (4,'translate:online_to','art_online_to',2,'',10,'','','','','','admin',1189344947,'admin',1189344947),
  (5,'translate:description','art_description',3,'',2,'','','','','','admin',1189345025,'admin',1189345025),
  (6,'translate:keywords','art_keywords',4,'',2,'','','','','','admin',1189345068,'admin',1189345068),
  (7,'translate:metadata_image','art_file',5,'',6,'','','','','','admin',1189345109,'admin',1189345109),
  (8,'translate:teaser','art_teaser',6,'',5,'','','','','','admin',1189345182,'admin',1189345182),
  (9,'translate:header_article_type','art_type_id',7,'size=1',3,'','Standard|Zugriff fuer alle','','','','admin',1191963797,'admin',1191964038);
/*!40000 ALTER TABLE `rex_metainfo_params` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `rex_metainfo_type`;
CREATE TABLE `rex_metainfo_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) DEFAULT NULL,
  `dbtype` varchar(255) NOT NULL,
  `dblength` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

LOCK TABLES `rex_metainfo_type` WRITE;
/*!40000 ALTER TABLE `rex_metainfo_type` DISABLE KEYS */;
INSERT INTO `rex_metainfo_type` VALUES 
  (1,'text','text',0),
  (2,'textarea','text',0),
  (3,'select','varchar',255),
  (4,'radio','varchar',255),
  (5,'checkbox','varchar',255),
  (6,'REX_MEDIA_WIDGET','varchar',255),
  (7,'REX_MEDIALIST_WIDGET','text',0),
  (8,'REX_LINK_WIDGET','varchar',255),
  (9,'REX_LINKLIST_WIDGET','text',0),
  (10,'date','text',0),
  (11,'datetime','text',0),
  (12,'legend','text',0),
  (13,'time','text',0);
/*!40000 ALTER TABLE `rex_metainfo_type` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `rex_module`;
CREATE TABLE `rex_module` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `output` text NOT NULL,
  `input` text NOT NULL,
  `createuser` varchar(255) NOT NULL,
  `updateuser` varchar(255) NOT NULL,
  `createdate` datetime NOT NULL,
  `updatedate` datetime NOT NULL,
  `attributes` text,
  `revision` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

LOCK TABLES `rex_module` WRITE;
/*!40000 ALTER TABLE `rex_module` DISABLE KEYS */;
INSERT INTO `rex_module` VALUES 
  (1,'YForm Formbuilder','<?php\r\n\r\n/**\r\n * yform\r\n * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus\r\n * @author <a href=\"http://www.yakamara.de\">www.yakamara.de</a>\r\n */\r\n\r\n// module:yform_basic_output\r\n// v1.0.1\r\n//--------------------------------------------------------------------------------\r\n\r\n$yform = new rex_yform;\r\nif (\"REX_VALUE[7]\" == 1) { $yform->setDebug(TRUE); }\r\n$form_data = \"REX_VALUE[id=3 output=html]\";\r\n$form_data = trim(rex_yform::unhtmlentities($form_data));\r\n$yform->setObjectparams(\'form_action\', rex_getUrl(REX_ARTICLE_ID,REX_CLANG_ID));\r\n$yform->setFormData($form_data);\r\n\r\n// action - showtext\r\nif(\"REX_VALUE[id=11]\" != \"\") {\r\n    $html = \"0\"; // plaintext\r\n    if(\'REX_VALUE[11]\' == 1) $html = \"1\"; // html\r\n    if(\'REX_VALUE[11]\' == 2) $html = \"2\"; // textile\r\n\r\n    $e3 = \'\'; $e4 = \'\';\r\n    if ($html == \"0\") {\r\n        $e3 = \'<div class=\"alert alert-success\">\';\r\n        $e4 = \'</div>\';\r\n    }\r\n\r\n    $t = str_replace(\"<br />\",\"\",rex_yform::unhtmlentities(\'REX_VALUE[6]\'));\r\n    $yform->setActionField(\"showtext\",array(\r\n    $t,\r\n    $e3,\r\n    $e4,\r\n    $html // als HTML interpretieren\r\n    )\r\n    );\r\n}\r\n\r\n$form_type = \"REX_VALUE[1]\";\r\n\r\n// action - email\r\nif ($form_type == \"1\" || $form_type == \"2\") {\r\n    $mail_from = (\'REX_VALUE[2]\' != \'\') ? \'REX_VALUE[2]\' : rex::getErrorEmail();\r\n    $mail_to = (\'REX_VALUE[12]\' != \'\') ? \'REX_VALUE[12]\' : rex::getErrorEmail();\r\n    $mail_subject = \"REX_VALUE[4]\";\r\n    $mail_body = str_replace(\"<br />\",\"\",rex_yform::unhtmlentities(\'REX_VALUE[5]\'));\r\n    $yform->setActionField(\"email\", array(\r\n    $mail_from,\r\n    $mail_to,\r\n    $mail_subject,\r\n    $mail_body\r\n    )\r\n    );\r\n}\r\n\r\n// action - db\r\nif ($form_type == \"0\" || $form_type == \"2\") {\r\n    $yform->setObjectparams(\'main_table\', \'REX_VALUE[8]\');\r\n\r\n    //getdata\r\n    if (\"REX_VALUE[10]\" != \"\")\r\n        $yform->setObjectparams(\"getdata\",TRUE);\r\n\r\n    $yform->setActionField(\"db\", array(\r\n    \"REX_VALUE[8]\", // table\r\n    $yform->objparams[\"main_where\"], // where\r\n    )\r\n    );\r\n}\r\n\r\necho $yform->getForm();\r\n\r\n?>','<?php\r\n\r\n/**\r\n * yform\r\n * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus\r\n * @author <a href=\"http://www.yakamara.de\">www.yakamara.de</a>\r\n */\r\n\r\n// module:yform_basic_input\r\n// v1.0\r\n// --------------------------------------------------------------------------------\r\n\r\n// DEBUG SELECT\r\n////////////////////////////////////////////////////////////////////////////////\r\n$dbg_sel = new rex_select();\r\n$dbg_sel->setName(\'REX_INPUT_VALUE[7]\');\r\n$dbg_sel->setAttribute(\'class\', \'form-control\');\r\n$dbg_sel->addOption(\'inaktiv\',\'0\');\r\n$dbg_sel->addOption(\'aktiv\',\'1\');\r\n$dbg_sel->setSelected(\'REX_VALUE[7]\');\r\n$dbg_sel = $dbg_sel->get();\r\n\r\n\r\n// TABLE SELECT\r\n////////////////////////////////////////////////////////////////////////////////\r\n$gc = rex_sql::factory();\r\n$gc->setQuery(\'SHOW TABLES\');\r\n$tables = $gc->getArray();\r\n$tbl_sel = new rex_select();\r\n$tbl_sel->setName(\'REX_INPUT_VALUE[8]\');\r\n$tbl_sel->setAttribute(\'class\', \'form-control\');\r\n$tbl_sel->addOption(\'Keine Tabelle ausgewählt\', \'\');\r\nforeach ($tables as $key => $value) {\r\n  $tbl_sel->addOption(current($value), current($value));\r\n}\r\n$tbl_sel->setSelected(\'REX_VALUE[8]\');\r\n$tbl_sel = $tbl_sel->get();\r\n\r\n\r\n// PLACEHOLDERS\r\n////////////////////////////////////////////////////////////////////////////////\r\n$yform = new rex_yform;\r\n$form_data = \'REX_VALUE[3]\';\r\n$form_data = trim(str_replace(\'<br />\',\'\',rex_yform::unhtmlentities($form_data)));\r\n$yform->setFormData($form_data);\r\n$yform->setRedaxoVars(REX_ARTICLE_ID,REX_CLANG_ID);\r\n$placeholders = \'\';\r\nif(\'REX_VALUE[3]\'!=\'\') {\r\n  $ignores = array(\'html\',\'validate\',\'action\');\r\n  $placeholders .= \'\r\n        <div id=\"yform-js-formbuilder-placeholders\">\r\n            <h3>Platzhalter: <span>[<a href=\"#\" id=\"yform-js-formbuilder-placeholders-help-toggler\">?</a>]</span></h3>\'.PHP_EOL;\r\n  foreach($yform->objparams[\'form_elements\'] as $e) {\r\n    if(!in_array($e[0],$ignores) && isset($e[1])) {\r\n      $label = (isset($e[2]) && $e[2] != \'\') ? $e[2] . \': \' : \'\';\r\n      $placeholders .= \'<code>\' . $label . \'###\'.$e[1].\'###</code> \'.PHP_EOL;\r\n    }\r\n  }\r\n  $placeholders .= \'\r\n            <ul id=\"yform-js-formbuilder-placeholders-help\">\r\n                <li>Die Platzhalter ergeben sich aus den obenstehenden Felddefinitionen.</li>\r\n                <li>Per Klick können einzelne Platzhalter in den Mail-Body kopiert werden.</li>\r\n                <li>Aktualisierung der Platzhalter erfolgt über die Aktualisierung des Moduls.</li>\r\n            </ul>\r\n        </div>\'.PHP_EOL;\r\n}\r\n\r\n\r\n// OTHERS\r\n////////////////////////////////////////////////////////////////////////////////\r\n$row_pad = 1;\r\n\r\n$sel = \'REX_VALUE[1]\';\r\n$db_display   = ($sel==\'\' || $sel==1) ?\' style=\"display:none\"\':\'\';\r\n$mail_display = ($sel==\'\' || $sel==0) ?\' style=\"display:none\"\':\'\';\r\n\r\n?>\r\n\r\n<div id=\"yform-formbuilder\">\r\n  <fieldset class=\"form-horizontal\">\r\n    <legend>Formular</legend>\r\n    <div class=\"form-group\">\r\n      <label class=\"col-md-2 control-label text-left\">Debug Modus</label>\r\n      <div class=\"col-md-10\">\r\n        <div class=\"yform-select-style\">\r\n          <?= $dbg_sel; ?>\r\n        </div>\r\n      </div>\r\n    </div>\r\n    <div class=\"form-group\">\r\n      <label class=\"col-md-2 control-label\" for=\"yform-formbuilder-definition\">Felddefinitionen</label>\r\n      <div class=\"col-md-10\">\r\n        <textarea class=\"form-control\" style=\"font-family: monospace;\" id=\"yform-formbuilder-definition\" name=\"REX_INPUT_VALUE[3]\" rows=\"<?php echo (count(explode(\"\\r\",\'REX_VALUE[3]\'))+$row_pad);?>\">REX_VALUE[3]</textarea>\r\n      </div>\r\n    </div>\r\n    <div class=\"form-group\">\r\n      <label class=\"col-md-2 control-label\">Verfügbare Feldklassen</label>\r\n      <div class=\"col-md-10\">\r\n        <div id=\"yform-formbuilder-classes-showhelp\"><?= rex_yform::showHelp(true,true); ?></div>\r\n      </div>\r\n    </div>\r\n    <div class=\"form-group\">\r\n      <label class=\"col-md-2 control-label\">Meldung bei erfolgreichen Versand</label>\r\n      <div class=\"col-md-10\">\r\n        <label class=\"radio-inline\">\r\n          <input type=\"radio\" name=\"REX_INPUT_VALUE[11]\" value=\"0\"<?php if(\"REX_VALUE[11]\" == \'0\') echo \' checked\'; ?> /> Plaintext\r\n        </label>\r\n        <label class=\"radio-inline\">\r\n          <input type=\"radio\" name=\"REX_INPUT_VALUE[11]\" value=\"1\"<?php if(\"REX_VALUE[11]\" == \'1\') echo \' checked\'; ?> /> HTML\r\n        </label>\r\n        <label class=\"radio-inline\">\r\n          <input type=\"radio\" name=\"REX_INPUT_VALUE[11]\" value=\"2\"<?php if(\"REX_VALUE[11]\" == \'2\') echo \' checked\'; ?> /> Textile\r\n        </label>\r\n      </div>\r\n      <div class=\"col-md-offset-2 col-md-10\">\r\n        <textarea class=\"form-control\" name=\"REX_INPUT_VALUE[6]\" rows=\"<?php echo (count(explode(\"\\r\",\'REX_VALUE[6]\'))+$row_pad);?>\">REX_VALUE[6]</textarea>\r\n      </div>\r\n    </div>\r\n  </fieldset>\r\n\r\n  <fieldset class=\"form-horizontal\">\r\n    <legend>Vordefinierte Aktionen</legend>\r\n\r\n    <div class=\"form-group\">\r\n      <label class=\"col-md-2 control-label\">Bei Submit</label>\r\n      <div class=\"col-md-10\">\r\n        <div class=\"yform-select-style\">\r\n          <select class=\"form-control\" id=\"yform-js-formbuilder-action-select\" name=\"REX_INPUT_VALUE[1]\" size=\"1\">\r\n            <option value=\"\"<?php if(\"REX_VALUE[1]\" == \"\")  echo \" selected \"; ?>>Nichts machen (actions im Formular definieren)</option>\r\n            <option value=\"0\"<?php if(\"REX_VALUE[1]\" == \"0\") echo \" selected \"; ?>>Nur in Datenbank speichern</option>\r\n            <option value=\"1\"<?php if(\"REX_VALUE[1]\" == \"1\") echo \" selected \"; ?>>Nur E-Mail versenden</option>\r\n            <option value=\"2\"<?php if(\"REX_VALUE[1]\" == \"2\") echo \" selected \"; ?>>E-Mail versenden und in Datenbank speichern</option>\r\n          </select>\r\n        </div>\r\n      </div>\r\n    </div>\r\n  </fieldset>\r\n\r\n  <fieldset class=\"form-horizontal\" id=\"yform-js-formbuilder-mail-fieldset\"<?php echo $mail_display;?> >\r\n    <legend>E-Mail-Versand:</legend>\r\n\r\n    <div class=\"form-group\">\r\n      <label class=\"col-md-2 control-label\">Absender</label>\r\n      <div class=\"col-md-10\">\r\n        <input class=\"form-control\" type=\"text\" name=\"REX_INPUT_VALUE[2]\" value=\"REX_VALUE[2]\" />\r\n      </div>\r\n    </div>\r\n    <div class=\"form-group\">\r\n      <label class=\"col-md-2 control-label\">Empfänger</label>\r\n      <div class=\"col-md-10\">\r\n        <input class=\"form-control\" type=\"text\" name=\"REX_INPUT_VALUE[12]\" value=\"REX_VALUE[12]\" />\r\n      </div>\r\n    </div>\r\n    <div class=\"form-group\">\r\n      <label class=\"col-md-2 control-label\">Subject</label>\r\n      <div class=\"col-md-10\">\r\n        <input class=\"form-control\" type=\"text\" name=\"REX_INPUT_VALUE[4]\" value=\"REX_VALUE[4]\" />\r\n      </div>\r\n    </div>\r\n    <div class=\"form-group\">\r\n      <label class=\"col-md-2 control-label\">Mailbody</label>\r\n      <div class=\"col-md-10\">\r\n        <textarea class=\"form-control\" id=\"yform-js-formbuilder-mail-body\" name=\"REX_INPUT_VALUE[5]\" rows=\"<?php echo (count(explode(\"\\r\",\'REX_VALUE[5]\'))+$row_pad);?>\">REX_VALUE[5]</textarea>\r\n        <div class=\"help-block\">\r\n          <?php echo $placeholders;?>\r\n        </div>\r\n      </div>\r\n    </div>\r\n\r\n  </fieldset>\r\n\r\n  <fieldset class=\"form-horizontal\" id=\"yform-js-formbuilder-db-fieldset\"<?php echo $db_display;?> >\r\n    <legend>Datenbank Einstellungen</legend>\r\n\r\n    <div class=\"form-group\">\r\n      <label class=\"col-md-2 control-label\">Tabelle wählen <span>[<a href=\"#\" id=\"yform-js-formbuilder-db-help-toggler\">?</a>]</span></label>\r\n      <div class=\"col-md-10\">\r\n        <div class=\"yform-select-style\">\r\n          <?= $tbl_sel; ?>\r\n        </div>\r\n        <div class=\"help-block\">\r\n          <ul id=\"yform-js-formbuilder-db-help\">\r\n            <li>Hier werden die Daten des Formular hineingespeichert</li>\r\n          </ul>\r\n        </div>\r\n      </div>\r\n    </div>\r\n  </fieldset>\r\n\r\n</div>\r\n\r\n<p id=\"yform-formbuilder-info\"><?=  rex_addon::get(\'yform\')->getName() . \' \' . rex_addon::get(\'yform\')->getVersion() ?></p>\r\n\r\n<script type=\"text/javascript\">\r\n  <!--\r\n  (function($){\r\n\r\n    // AUTOGROW BY ROWS\r\n    $(\'#yform-formbuilder textarea\').keyup(function(){\r\n      var rows = $(this).val().split(/\\r?\\n|\\r/).length + <?php echo $row_pad;?>;\r\n      $(this).attr(\'rows\',rows);\r\n    });\r\n\r\n    // TOGGLERS\r\n    $(\'#yform-js-formbuilder-placeholders-help-toggler\').click(function(e){\r\n      e.preventDefault();\r\n      $(\'#yform-js-formbuilder-placeholders-help\').toggle(50);return false;\r\n    });\r\n    $(\'#yform-js-formbuilder-where-help-toggler\').click(function(e){\r\n      e.preventDefault();\r\n      $(\'#yform-js-formbuilder-where-help\').toggle(50);return false;\r\n    });\r\n    $(\'#yform-js-formbuilder-db-help-toggler\').click(function(e){\r\n      e.preventDefault();\r\n      $(\'#yform-js-formbuilder-db-help\').toggle(50);return false;\r\n    });\r\n\r\n\r\n    // INSERT PLACEHOLDERS\r\n    $(\'#yform-js-formbuilder-placeholders code\').click(function(){\r\n      newval = $(\'#yform-js-formbuilder-mail-body\').val()+\' \'+$(this).html();\r\n      $(\'#yform-js-formbuilder-mail-body\').val(newval);\r\n      $(this).addClass(\'text-muted\');\r\n    });\r\n\r\n    // TOGGLE MAIL/DB PANELS\r\n    $(\'#yform-js-formbuilder-action-select\').change(function(){\r\n      switch($(this).val()){\r\n        case \'\':\r\n          $(\'#yform-js-formbuilder-db-fieldset\').hide(0);\r\n          $(\'#yform-js-formbuilder-mail-fieldset\').hide(0);\r\n          break;\r\n        case \'1\':\r\n          $(\'#yform-js-formbuilder-db-fieldset\').hide(0);\r\n          $(\'#yform-js-formbuilder-mail-fieldset\').show(0);\r\n          break;\r\n        case \'0\':\r\n          $(\'#yform-js-formbuilder-db-fieldset\').show(0);\r\n          $(\'#yform-js-formbuilder-mail-fieldset\').hide(0);\r\n          break;\r\n        case \'2\':\r\n        case \'3\':\r\n          $(\'#yform-js-formbuilder-db-fieldset\').show(0);\r\n          $(\'#yform-js-formbuilder-mail-fieldset\').show(0);\r\n          break;\r\n      }\r\n    });\r\n\r\n  })(jQuery)\r\n  //-->\r\n</script>','','admin','0000-00-00 00:00:00','2016-03-22 11:27:18','',0),
  (2,'01 - Headline','<REX_VALUE[2]>REX_VALUE[1]</REX_VALUE[2]>','<strong>Überschrift eingeben:</strong>\r\n<br/>\r\n<textarea name=\"REX_INPUT_VALUE[1]\" rows=\"2\">REX_VALUE[1]</textarea>\r\n<br/><br/>\r\n<strong>Art: </strong>\r\n	<select name=\"REX_INPUT_VALUE[2]\">\r\n		<option value=\'h1\' <?php if (\"REX_VALUE[2]\" == \'h1\') echo \'selected\'; ?>>1. Überschrift (H1)</option>\r\n		<option value=\'h2\' <?php if (\"REX_VALUE[2]\" == \'h2\') echo \'selected\'; ?>>2. Überschrift (H2)</option>\r\n		<option value=\'h3\' <?php if (\"REX_VALUE[2]\" == \'h3\') echo \'selected\'; ?>>3. Überschrift (H3)</option>			\r\n	</select>\r\n\r\n','admin','admin','2016-03-14 23:50:29','2016-03-21 17:36:01','',0);
/*!40000 ALTER TABLE `rex_module` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `rex_module_action`;
CREATE TABLE `rex_module_action` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `module_id` int(10) unsigned NOT NULL,
  `action_id` int(10) unsigned NOT NULL,
  `revision` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `rex_template`;
CREATE TABLE `rex_template` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `content` text,
  `active` tinyint(1) DEFAULT NULL,
  `createuser` varchar(255) NOT NULL,
  `updateuser` varchar(255) NOT NULL,
  `createdate` datetime NOT NULL,
  `updatedate` datetime NOT NULL,
  `attributes` text,
  `revision` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

LOCK TABLES `rex_template` WRITE;
/*!40000 ALTER TABLE `rex_template` DISABLE KEYS */;
INSERT INTO `rex_template` VALUES 
  (1,'01 . Template','REX_TEMPLATE[2]<!DOCTYPE HTML>\r\n<html>\r\n\r\n<head>\r\n	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\r\n	<title><?php echo rex::getServerName(); ?></title>\r\n	<meta name=\"robots\" content=\"index,follow\" />\r\n\r\n	<link rel=\"icon\" href=\"/layout/css/fav.ico\" />\r\n	<link href=\"/layout/css/main.css\" type=\"text/css\" media=\"screen\" rel=\"stylesheet\" />\r\n</head>\r\n\r\n<?php\r\n\r\n// ----- Startseite wird \"markiert\", damit das CSS entsprechend reagieren kann\r\nif (rex_article::getSiteStartArticleId() == REX_ARTICLE_ID) {\r\n	echo \'<body id=\"homepage\">\';\r\n} else {\r\n	echo \'<body id=\"other\">\';\r\n}\r\n\r\n// ----- Headerbild wird von oberster Ebene vererbt\r\n$metafile = \"\";\r\n\r\n?>\r\n\r\n<div id=\"header\">\r\n\r\n<?php // Meta-Navigation\r\n	echo \'<div class=\"metanavi\">\';\r\n	$nav = rex_ycom_navigation::factory();\r\n	echo $nav->get(11,1,TRUE,TRUE); \r\n	echo \'</div>\';\r\n?>\r\n\r\n	<div class=\"userinfo\">REX_TEMPLATE[3]</div>\r\n\r\n</div><!-- header -->\r\n\r\n<div id=\"wrapper\">\r\n	<div id=\"logo\">\r\n		<a href=\"/\" title=\"<?php echo rex::getServerName(); ?> - Startseite\" >Zur Startseite</a>\r\n	</div> <!-- logo -->\r\n\r\n<?php // Navigation\r\n	echo \'<div id=\"mainnavi\">\'.\"\\r\\n\";\r\n	$nav = rex_ycom_navigation::factory();\r\n	echo $nav->get(0,1,TRUE,TRUE); \r\n	echo \"\\r\\n\";\r\n	echo \'</div>\'.\"\\r\\n\";\r\n?>\r\n\r\n	<?php\r\n		// Startseite\r\n		\r\n		if (rex_article::getSiteStartArticleId() == REX_ARTICLE_ID) {\r\n			\r\n			echo \'<div class=\"imageHome\">\';\r\n			echo \'</div>\';\r\n			echo \'<div class=\"imageHomeShadow\"></div>\'.\"\\r\\n\";\r\n\r\n			$headline1 = $this->getValue(\"art_headline1\");\r\n			$headline2 = $this->getValue(\"art_headline2\");\r\n\r\n			echo \'<div class=\"headlines\">\'.\"\\r\\n\";\r\n			echo \'<h1>\'.$headline1.\'</h1>\'.\"\\r\\n\";\r\n			echo \'<h2>\'.$headline2.\'</h2>\'.\"\\r\\n\";\r\n			echo \'</div><!-- headlines -->\'.\"\\r\\n\";\r\n			\r\n		} else {\r\n\r\n			echo \'<div class=\"imageOther\">\'.\"\\r\\n\";\r\n			if($metafile != \"\")\r\n				echo \'<img src=\"/files/\'.$metafile.\'\" width=\"930\" height=\"170\" alt=\"\" />\'.\"\\r\\n\";\r\n			echo \'</div>\'.\"\\r\\n\";\r\n			echo \'<div class=\"imageOtherShadow\"></div>\'.\"\\r\\n\";\r\n		}\r\n	?>\r\n\r\n<div id=\"content\">\r\n\r\n	<div id=\"left\">\r\n		REX_ARTICLE[ctype=1]\r\n	</div>\r\n\r\n	<div id=\"right\">\r\n		\r\n	<?php // Navigation\r\n		if (rex_article::getSiteStartArticleId() != REX_ARTICLE_ID) {\r\n			$P = explode(\"|\",$this->getValue(\"path\").$this->getValue(\"article_id\").\"|\");\r\n			$rexnav2 = rex_navigation::factory();\r\n			echo \'<div id=\"subnavi\">\';	\r\n			echo $rexnav2->get($P[1],3,TRUE,TRUE);\r\n			echo \'</div>\';\r\n		}\r\n\r\n	?>\r\n		REX_ARTICLE[ctype=2]\r\n	</div>\r\n\r\n</div> <!-- content -->\r\n\r\n</div><!-- /wrapper -->\r\n<div id=\"footer\">\r\n	<div class=\"footerleft\">\r\n	<p>&copy;  by <a href=\"http://www.redaxo.org\">www.redaxo.org</a>, <a href=\"http://www.yakamara.de\">www.yakamara.de</a></p></div>\r\n	<div class=\"footerright\">\r\n\r\n	</div>\r\n</div><!-- footer -->\r\n\r\n</body>\r\n</html>',1,'admin','admin','2016-03-24 14:49:23','2016-03-24 14:49:23','{\"ctype\":{\"1\":\"Inhalt\",\"2\":\"rechte Spalte\"},\"modules\":{\"1\":{\"all\":\"1\"},\"2\":{\"all\":\"1\"},\"3\":{\"all\":\"1\"}},\"categories\":{\"all\":\"1\"}}',0),
  (2,'02 . Header','<?php\r\n\r\nheader(\'Content-Type: text/html; charset=utf-8\');\r\n\r\n/**\r\n * Artikel/Kategorie online? Wenn nein dann auf die 404 Fehlerseite\r\n */\r\n\r\nif (REX_ARTICLE[field=\"status\"] == 0) {\r\n\r\n  // Weiterleitung für Artikel die nicht online sind\r\n  header (\'HTTP/1.0 404 Not Found\');\r\n  header(\'Location: \'.rex_getUrl(rex_article::getNotfoundArticleId()));\r\n  exit;\r\n\r\n}\r\n\r\n?>',0,'admin','admin','2016-03-23 17:09:17','2016-03-23 17:09:17','{\"ctype\":[],\"modules\":{\"1\":{\"all\":\"1\"}},\"categories\":{\"all\":\"1\"}}',0),
  (3,'03 . Userinfo','<?php\r\n\r\nif( ($user = rex_ycom_auth::getUser()) ) {\r\n\r\n  echo \'Sie sind eingeloggt als: \';\r\n\r\n  $name = $user->getValue(\"firstname\");\r\n  $name .= \" \".$user->getValue(\"name\");\r\n  echo \'<strong>\'.htmlspecialchars($name).\'</strong>\';\r\n\r\n} else {\r\n\r\n  echo \'Sie sind nicht eingeloggt.\';\r\n\r\n}\r\n\r\n?>',0,'admin','admin','2016-03-24 14:46:13','2016-03-24 14:46:13','{\"ctype\":[],\"modules\":{\"1\":{\"all\":\"1\"}},\"categories\":{\"all\":\"1\"}}',0);
/*!40000 ALTER TABLE `rex_template` ENABLE KEYS */;
UNLOCK TABLES;


DROP TABLE IF EXISTS `rex_ycom_group`;
CREATE TABLE `rex_ycom_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

LOCK TABLES `rex_ycom_group` WRITE;
/*!40000 ALTER TABLE `rex_ycom_group` DISABLE KEYS */;
INSERT INTO `rex_ycom_group` VALUES 
  (1,'Gruppe A'),
  (2,'Gruppe B'),
  (3,'Gruppe C');
/*!40000 ALTER TABLE `rex_ycom_group` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `rex_ycom_user`;
CREATE TABLE `rex_ycom_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` text NOT NULL,
  `password` text NOT NULL,
  `email` text NOT NULL,
  `status` text NOT NULL,
  `firstname` text NOT NULL,
  `name` text NOT NULL,
  `activation_key` text NOT NULL,
  `session_key` text NOT NULL,
  `last_action_time` varchar(255) NOT NULL,
  `password_hash` text NOT NULL,
  `last_login_date` varchar(255) NOT NULL,
  `last_login_time` text NOT NULL,
  `com_groups` text NOT NULL,
  `ycom_groups` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

LOCK TABLES `rex_ycom_user` WRITE;
/*!40000 ALTER TABLE `rex_ycom_user` DISABLE KEYS */;
INSERT INTO `rex_ycom_user` VALUES 
  (1,'jan.kristinus@yakamara.de','$2y$10$eZODAw36MxZcpbhlgKeRXOJFPttdJi47ui7HWRZTVZ4AyPhGijfKm','jan.kristinus@yakamara.de','1','Jan','Kristinus','24b5b7243275cf1ddebeae2445ebc930','','1459265212','$2y$10$QChKbRz.pJafTdmQlwLNIO4OQAtt4y6A1LeKelhpIU3sBadzCFZku','','1459258241','1','');
/*!40000 ALTER TABLE `rex_ycom_user` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `rex_yform_email_template`;
CREATE TABLE `rex_yform_email_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `mail_from` varchar(255) NOT NULL DEFAULT '',
  `mail_from_name` varchar(255) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `body` text NOT NULL,
  `body_html` text NOT NULL,
  `attachments` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

LOCK TABLES `rex_yform_email_template` WRITE;
/*!40000 ALTER TABLE `rex_yform_email_template` DISABLE KEYS */;
INSERT INTO `rex_yform_email_template` VALUES 
  (6,'reactivate_account_de','no_replay@domain.de','domain.de','Community Addon: Neues Passwort','http://<?php echo $_SERVER[\'HTTP_HOST\'] ?>/?rex_com_activation_key=REX_YFORM_DATA[field=\"activation_key\"] ','',''),
  (5,'access_request_de','no_replay@domain.de','domain.de','Community Addon: Registrierungsmail','<?php \r\n\r\necho rex::getServer().rex_getUrl(24,\'\', \r\n[\r\n  \'rex_ycom_activation_key\' => REX_YFORM_DATA[field=\"activation_key\"],\r\n  \'rex_ycom_id\' => REX_YFORM_DATA[field=\"ID\"]\r\n]); \r\n\r\n?>','','');
/*!40000 ALTER TABLE `rex_yform_email_template` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `rex_yform_field`;
CREATE TABLE `rex_yform_field` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table_name` varchar(100) NOT NULL,
  `prio` int(11) NOT NULL,
  `type_id` varchar(100) NOT NULL,
  `type_name` varchar(100) NOT NULL,
  `list_hidden` tinyint(1) NOT NULL,
  `search` tinyint(1) NOT NULL,
  `name` text NOT NULL,
  `label` text NOT NULL,
  `not_required` text NOT NULL,
  `options` text NOT NULL,
  `multiple` text NOT NULL,
  `default` text NOT NULL,
  `size` text NOT NULL,
  `only_empty` text NOT NULL,
  `message` text NOT NULL,
  `table` text NOT NULL,
  `hashname` text NOT NULL,
  `password_hash` text NOT NULL,
  `no_db` text NOT NULL,
  `password_label` text NOT NULL,
  `field` text NOT NULL,
  `type` text NOT NULL,
  `empty_value` text NOT NULL,
  `empty_option` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;

LOCK TABLES `rex_yform_field` WRITE;
/*!40000 ALTER TABLE `rex_yform_field` DISABLE KEYS */;
INSERT INTO `rex_yform_field` VALUES 
  (33,'rex_ycom_user',6,'validate','email',1,0,'email','','','','','','','','translate:ycom_please_enter_email','','','','','','','','',''),
  (32,'rex_ycom_user',5,'validate','empty',1,0,'email','','','','','','','','translate:ycom_please_enter_email','','','','','','','','',''),
  (30,'rex_ycom_user',7,'validate','unique',1,0,'email','','','','','','','','translate:ycom_this_email_exists_already','rex_ycom_user','','','','','','','',''),
  (29,'rex_ycom_user',2,'validate','empty',1,0,'login','','','','','','','','translate:ycom_please_enter_login','','','','','','','','',''),
  (27,'rex_ycom_user',14,'value','datestamp',1,1,'last_action_time','U','','','','','','0','','','','','','','','','',''),
  (26,'rex_ycom_user',13,'value','emptyname',1,1,'session_key','translate:session_key','','','','','','','','','','','','','','','',''),
  (25,'rex_ycom_user',12,'value','emptyname',1,1,'activation_key','translate:activation_key','','','','','','','','','','','','','','','',''),
  (24,'rex_ycom_user',11,'value','text',0,1,'name','translate:name','','','','','','','','','','','','','','','',''),
  (23,'rex_ycom_user',10,'value','text',0,1,'firstname','translate:firstname','','','','','','','','','','','','','','','',''),
  (21,'rex_ycom_user',4,'value','text',0,1,'email','translate:email','','','','','','','','','','','','','','','',''),
  (22,'rex_ycom_user',9,'value','select',0,1,'status','translate:status','','translate:ycom_account_requested=0,translate:ycom_account_active=1,translate:ycom_account_inactive=-1','0','-1','1','','','','','','','','','','',''),
  (19,'rex_ycom_user',1,'value','text',1,1,'login','translate:login','','','','','','','','','','','','','','','',''),
  (34,'rex_ycom_user',3,'validate','unique',1,0,'login','','','','','','','','translate:ycom_this_login_exists_already','rex_ycom_user','','','','','','','',''),
  (36,'rex_ycom_group',1,'value','text',0,1,'name','translate:name','','','','','','','','','','','','','','','',''),
  (37,'rex_ycom_group',2,'validate','empty',1,0,'name','','','','','','','','translate:ycom_group_yform_enter_name','','','','','','','','',''),
  (40,'rex_ycom_user',15,'value','text',0,1,'last_login_time','translate:last_login_time','','','','','','','','','','','no_db','','','','',''),
  (41,'rex_ycom_user',16,'value','be_manager_relation',0,1,'ycom_groups','translate:ycom_groups','','','','','5','','','rex_ycom_group','','','','','name','1','','1'),
  (45,'rex_ycom_user',8,'value','ycom_auth_password',1,1,'password','translate:password','','','','','','','','','','','','','','','','');
/*!40000 ALTER TABLE `rex_yform_field` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `rex_yform_table`;
CREATE TABLE `rex_yform_table` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` tinyint(1) NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `list_amount` tinyint(3) unsigned NOT NULL DEFAULT '50',
  `list_sortfield` varchar(255) NOT NULL DEFAULT 'id',
  `list_sortorder` enum('ASC','DESC') NOT NULL DEFAULT 'ASC',
  `prio` int(11) NOT NULL,
  `search` tinyint(1) NOT NULL,
  `hidden` tinyint(1) NOT NULL,
  `export` tinyint(1) NOT NULL,
  `import` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `table_name` (`table_name`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

LOCK TABLES `rex_yform_table` WRITE;
/*!40000 ALTER TABLE `rex_yform_table` DISABLE KEYS */;
INSERT INTO `rex_yform_table` VALUES 
  (2,1,'rex_ycom_user','translate:ycom_user','',100,'login','DESC',1,0,0,1,1),
  (3,1,'rex_ycom_group','translate:ycom_group_name','',200,'name','ASC',2,0,0,1,1);
/*!40000 ALTER TABLE `rex_yform_table` ENABLE KEYS */;
UNLOCK TABLES;

