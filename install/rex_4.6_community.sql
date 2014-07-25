## Redaxo Database Dump Version 4
## Prefix rex_
## charset utf-8

DROP TABLE IF EXISTS `rex_62_params`;
CREATE TABLE `rex_62_params` (
  `field_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `prior` int(10) unsigned NOT NULL,
  `attributes` text NOT NULL,
  `type` int(10) unsigned DEFAULT NULL,
  `default` varchar(255) NOT NULL,
  `params` text,
  `validate` text,
  `restrictions` text NOT NULL,
  `createuser` varchar(255) NOT NULL,
  `createdate` int(11) NOT NULL,
  `updateuser` varchar(255) NOT NULL,
  `updatedate` int(11) NOT NULL,
  PRIMARY KEY (`field_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

LOCK TABLES `rex_62_params` WRITE;
/*!40000 ALTER TABLE `rex_62_params` DISABLE KEYS */;
INSERT INTO `rex_62_params` VALUES 
  (1,'translate:pool_file_description','med_description',1,'',2,'','','','','admin',1189343866,'admin',1189344596),
  (2,'translate:pool_file_copyright','med_copyright',2,'',1,'','','','','admin',1189343877,'admin',1189344617),
  (3,'translate:online_from','art_online_from',1,'',10,'','','','','admin',1189344934,'admin',1189344934),
  (4,'translate:online_to','art_online_to',2,'',10,'','','','','admin',1189344947,'admin',1189344947),
  (5,'translate:description','art_description',3,'',2,'','','','','admin',1189345025,'admin',1189345025),
  (6,'translate:keywords','art_keywords',4,'',2,'','','','','admin',1189345068,'admin',1189345068),
  (7,'translate:metadata_image','art_file',5,'',6,'','','','','admin',1189345109,'admin',1189345109),
  (9,'translate:com_group_name','art_com_groups',8,'multiple=multiple',3,'','select name as label,id from rex_com_group order by label','','','admin',1406292454,'',0),
  (8,'translate:com_permtype','art_com_permtype',6,'',3,'','0:translate:com_perm_extends|1:translate:com_perm_only_logged_in|2:translate:com_perm_only_not_logged_in|3:translate:com_perm_all','','','admin',1406292454,'',0),
  (10,'translate:com_group_perm','art_com_grouptype',7,'',3,'','0:translate:com_group_forallgroups|1:translate:com_group_inallgroups|2:translate:com_group_inonegroup|3:translate:com_group_nogroups','','','admin',1406292454,'admin',1320955573);
/*!40000 ALTER TABLE `rex_62_params` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `rex_62_type`;
CREATE TABLE `rex_62_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) DEFAULT NULL,
  `dbtype` varchar(255) NOT NULL,
  `dblength` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

LOCK TABLES `rex_62_type` WRITE;
/*!40000 ALTER TABLE `rex_62_type` DISABLE KEYS */;
INSERT INTO `rex_62_type` VALUES 
  (1,'text','text',0),
  (2,'textarea','text',0),
  (3,'select','varchar',255),
  (4,'radio','varchar',255),
  (5,'checkbox','varchar',255),
  (10,'date','text',0),
  (13,'time','text',0),
  (11,'datetime','text',0),
  (12,'legend','text',0),
  (6,'REX_MEDIA_BUTTON','varchar',255),
  (7,'REX_MEDIALIST_BUTTON','text',0),
  (8,'REX_LINK_BUTTON','varchar',255),
  (9,'REX_LINKLIST_BUTTON','text',0);
/*!40000 ALTER TABLE `rex_62_type` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `rex_679_type_effects`;
CREATE TABLE `rex_679_type_effects` (
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
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

LOCK TABLES `rex_679_type_effects` WRITE;
/*!40000 ALTER TABLE `rex_679_type_effects` DISABLE KEYS */;
INSERT INTO `rex_679_type_effects` VALUES 
  (1,1,'resize','a:6:{s:15:\"rex_effect_crop\";a:5:{s:21:\"rex_effect_crop_width\";s:0:\"\";s:22:\"rex_effect_crop_height\";s:0:\"\";s:28:\"rex_effect_crop_offset_width\";s:0:\"\";s:29:\"rex_effect_crop_offset_height\";s:0:\"\";s:24:\"rex_effect_crop_position\";s:13:\"middle_center\";}s:22:\"rex_effect_filter_blur\";a:3:{s:29:\"rex_effect_filter_blur_amount\";s:2:\"80\";s:29:\"rex_effect_filter_blur_radius\";s:1:\"8\";s:32:\"rex_effect_filter_blur_threshold\";s:1:\"3\";}s:25:\"rex_effect_filter_sharpen\";a:3:{s:32:\"rex_effect_filter_sharpen_amount\";s:2:\"80\";s:32:\"rex_effect_filter_sharpen_radius\";s:3:\"0.5\";s:35:\"rex_effect_filter_sharpen_threshold\";s:1:\"3\";}s:15:\"rex_effect_flip\";a:1:{s:20:\"rex_effect_flip_flip\";s:1:\"X\";}s:23:\"rex_effect_insert_image\";a:5:{s:34:\"rex_effect_insert_image_brandimage\";s:0:\"\";s:28:\"rex_effect_insert_image_hpos\";s:5:\"right\";s:28:\"rex_effect_insert_image_vpos\";s:6:\"bottom\";s:33:\"rex_effect_insert_image_padding_x\";s:3:\"-10\";s:33:\"rex_effect_insert_image_padding_y\";s:3:\"-10\";}s:17:\"rex_effect_resize\";a:4:{s:23:\"rex_effect_resize_width\";s:3:\"200\";s:24:\"rex_effect_resize_height\";s:3:\"200\";s:23:\"rex_effect_resize_style\";s:7:\"maximum\";s:31:\"rex_effect_resize_allow_enlarge\";s:11:\"not_enlarge\";}}',1,0,'',0,''),
  (2,2,'resize','a:6:{s:15:\"rex_effect_crop\";a:5:{s:21:\"rex_effect_crop_width\";s:0:\"\";s:22:\"rex_effect_crop_height\";s:0:\"\";s:28:\"rex_effect_crop_offset_width\";s:0:\"\";s:29:\"rex_effect_crop_offset_height\";s:0:\"\";s:24:\"rex_effect_crop_position\";s:13:\"middle_center\";}s:22:\"rex_effect_filter_blur\";a:3:{s:29:\"rex_effect_filter_blur_amount\";s:2:\"80\";s:29:\"rex_effect_filter_blur_radius\";s:1:\"8\";s:32:\"rex_effect_filter_blur_threshold\";s:1:\"3\";}s:25:\"rex_effect_filter_sharpen\";a:3:{s:32:\"rex_effect_filter_sharpen_amount\";s:2:\"80\";s:32:\"rex_effect_filter_sharpen_radius\";s:3:\"0.5\";s:35:\"rex_effect_filter_sharpen_threshold\";s:1:\"3\";}s:15:\"rex_effect_flip\";a:1:{s:20:\"rex_effect_flip_flip\";s:1:\"X\";}s:23:\"rex_effect_insert_image\";a:5:{s:34:\"rex_effect_insert_image_brandimage\";s:0:\"\";s:28:\"rex_effect_insert_image_hpos\";s:5:\"right\";s:28:\"rex_effect_insert_image_vpos\";s:6:\"bottom\";s:33:\"rex_effect_insert_image_padding_x\";s:3:\"-10\";s:33:\"rex_effect_insert_image_padding_y\";s:3:\"-10\";}s:17:\"rex_effect_resize\";a:4:{s:23:\"rex_effect_resize_width\";s:3:\"600\";s:24:\"rex_effect_resize_height\";s:3:\"600\";s:23:\"rex_effect_resize_style\";s:7:\"maximum\";s:31:\"rex_effect_resize_allow_enlarge\";s:11:\"not_enlarge\";}}',1,0,'',0,''),
  (3,3,'resize','a:6:{s:15:\"rex_effect_crop\";a:5:{s:21:\"rex_effect_crop_width\";s:0:\"\";s:22:\"rex_effect_crop_height\";s:0:\"\";s:28:\"rex_effect_crop_offset_width\";s:0:\"\";s:29:\"rex_effect_crop_offset_height\";s:0:\"\";s:24:\"rex_effect_crop_position\";s:13:\"middle_center\";}s:22:\"rex_effect_filter_blur\";a:3:{s:29:\"rex_effect_filter_blur_amount\";s:2:\"80\";s:29:\"rex_effect_filter_blur_radius\";s:1:\"8\";s:32:\"rex_effect_filter_blur_threshold\";s:1:\"3\";}s:25:\"rex_effect_filter_sharpen\";a:3:{s:32:\"rex_effect_filter_sharpen_amount\";s:2:\"80\";s:32:\"rex_effect_filter_sharpen_radius\";s:3:\"0.5\";s:35:\"rex_effect_filter_sharpen_threshold\";s:1:\"3\";}s:15:\"rex_effect_flip\";a:1:{s:20:\"rex_effect_flip_flip\";s:1:\"X\";}s:23:\"rex_effect_insert_image\";a:5:{s:34:\"rex_effect_insert_image_brandimage\";s:0:\"\";s:28:\"rex_effect_insert_image_hpos\";s:5:\"right\";s:28:\"rex_effect_insert_image_vpos\";s:6:\"bottom\";s:33:\"rex_effect_insert_image_padding_x\";s:3:\"-10\";s:33:\"rex_effect_insert_image_padding_y\";s:3:\"-10\";}s:17:\"rex_effect_resize\";a:4:{s:23:\"rex_effect_resize_width\";s:2:\"80\";s:24:\"rex_effect_resize_height\";s:2:\"80\";s:23:\"rex_effect_resize_style\";s:7:\"maximum\";s:31:\"rex_effect_resize_allow_enlarge\";s:11:\"not_enlarge\";}}',1,0,'',0,''),
  (4,4,'resize','a:6:{s:15:\"rex_effect_crop\";a:5:{s:21:\"rex_effect_crop_width\";s:0:\"\";s:22:\"rex_effect_crop_height\";s:0:\"\";s:28:\"rex_effect_crop_offset_width\";s:0:\"\";s:29:\"rex_effect_crop_offset_height\";s:0:\"\";s:24:\"rex_effect_crop_position\";s:13:\"middle_center\";}s:22:\"rex_effect_filter_blur\";a:3:{s:29:\"rex_effect_filter_blur_amount\";s:2:\"80\";s:29:\"rex_effect_filter_blur_radius\";s:1:\"8\";s:32:\"rex_effect_filter_blur_threshold\";s:1:\"3\";}s:25:\"rex_effect_filter_sharpen\";a:3:{s:32:\"rex_effect_filter_sharpen_amount\";s:2:\"80\";s:32:\"rex_effect_filter_sharpen_radius\";s:3:\"0.5\";s:35:\"rex_effect_filter_sharpen_threshold\";s:1:\"3\";}s:15:\"rex_effect_flip\";a:1:{s:20:\"rex_effect_flip_flip\";s:1:\"X\";}s:23:\"rex_effect_insert_image\";a:5:{s:34:\"rex_effect_insert_image_brandimage\";s:0:\"\";s:28:\"rex_effect_insert_image_hpos\";s:5:\"right\";s:28:\"rex_effect_insert_image_vpos\";s:6:\"bottom\";s:33:\"rex_effect_insert_image_padding_x\";s:3:\"-10\";s:33:\"rex_effect_insert_image_padding_y\";s:3:\"-10\";}s:17:\"rex_effect_resize\";a:4:{s:23:\"rex_effect_resize_width\";s:3:\"246\";s:24:\"rex_effect_resize_height\";s:3:\"246\";s:23:\"rex_effect_resize_style\";s:7:\"maximum\";s:31:\"rex_effect_resize_allow_enlarge\";s:11:\"not_enlarge\";}}',1,0,'',0,''),
  (5,5,'resize','a:6:{s:15:\"rex_effect_crop\";a:5:{s:21:\"rex_effect_crop_width\";s:0:\"\";s:22:\"rex_effect_crop_height\";s:0:\"\";s:28:\"rex_effect_crop_offset_width\";s:0:\"\";s:29:\"rex_effect_crop_offset_height\";s:0:\"\";s:24:\"rex_effect_crop_position\";s:13:\"middle_center\";}s:22:\"rex_effect_filter_blur\";a:3:{s:29:\"rex_effect_filter_blur_amount\";s:2:\"80\";s:29:\"rex_effect_filter_blur_radius\";s:1:\"8\";s:32:\"rex_effect_filter_blur_threshold\";s:1:\"3\";}s:25:\"rex_effect_filter_sharpen\";a:3:{s:32:\"rex_effect_filter_sharpen_amount\";s:2:\"80\";s:32:\"rex_effect_filter_sharpen_radius\";s:3:\"0.5\";s:35:\"rex_effect_filter_sharpen_threshold\";s:1:\"3\";}s:15:\"rex_effect_flip\";a:1:{s:20:\"rex_effect_flip_flip\";s:1:\"X\";}s:23:\"rex_effect_insert_image\";a:5:{s:34:\"rex_effect_insert_image_brandimage\";s:0:\"\";s:28:\"rex_effect_insert_image_hpos\";s:5:\"right\";s:28:\"rex_effect_insert_image_vpos\";s:6:\"bottom\";s:33:\"rex_effect_insert_image_padding_x\";s:3:\"-10\";s:33:\"rex_effect_insert_image_padding_y\";s:3:\"-10\";}s:17:\"rex_effect_resize\";a:4:{s:23:\"rex_effect_resize_width\";s:3:\"246\";s:24:\"rex_effect_resize_height\";s:3:\"246\";s:23:\"rex_effect_resize_style\";s:7:\"maximum\";s:31:\"rex_effect_resize_allow_enlarge\";s:11:\"not_enlarge\";}}',1,0,'',0,'');
/*!40000 ALTER TABLE `rex_679_type_effects` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `rex_679_types`;
CREATE TABLE `rex_679_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

LOCK TABLES `rex_679_types` WRITE;
/*!40000 ALTER TABLE `rex_679_types` DISABLE KEYS */;
INSERT INTO `rex_679_types` VALUES 
  (1,1,'rex_mediapool_detail','Zur Darstellung von Bildern in der Detailansicht im Medienpool'),
  (2,1,'rex_mediapool_maximized','Zur Darstellung von Bildern im Medienpool wenn maximiert'),
  (3,1,'rex_mediapool_preview','Zur Darstellung der Vorschaubilder im Medienpool'),
  (4,1,'rex_mediabutton_preview','Zur Darstellung der Vorschaubilder in REX_MEDIA_BUTTON[]s'),
  (5,1,'rex_medialistbutton_preview','Zur Darstellung der Vorschaubilder in REX_MEDIALIST_BUTTON[]s');
/*!40000 ALTER TABLE `rex_679_types` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `rex_action`;
CREATE TABLE `rex_action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `preview` text,
  `presave` text,
  `postsave` text,
  `previewmode` tinyint(4) DEFAULT NULL,
  `presavemode` tinyint(4) DEFAULT NULL,
  `postsavemode` tinyint(4) DEFAULT NULL,
  `createuser` varchar(255) NOT NULL,
  `createdate` int(11) NOT NULL,
  `updateuser` varchar(255) NOT NULL,
  `updatedate` int(11) NOT NULL,
  `revision` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `rex_article`;
CREATE TABLE `rex_article` (
  `pid` int(11) NOT NULL AUTO_INCREMENT,
  `id` int(11) NOT NULL,
  `re_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `catname` varchar(255) NOT NULL,
  `catprior` int(11) NOT NULL,
  `attributes` text NOT NULL,
  `startpage` tinyint(1) NOT NULL,
  `prior` int(11) NOT NULL,
  `path` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `createdate` int(11) NOT NULL,
  `updatedate` int(11) NOT NULL,
  `template_id` int(11) NOT NULL,
  `clang` int(11) NOT NULL,
  `createuser` varchar(255) NOT NULL,
  `updateuser` varchar(255) NOT NULL,
  `revision` int(11) NOT NULL,
  `art_online_from` text,
  `art_online_to` text,
  `art_description` text,
  `art_keywords` text,
  `art_file` varchar(255) DEFAULT NULL,
  `art_com_permtype` varchar(255) NOT NULL,
  `art_com_grouptype` varchar(255) DEFAULT '',
  `art_com_groups` varchar(255) NOT NULL,
  `art_rexseo_legend` text,
  `art_rexseo_url` text,
  `art_rexseo_canonicalurl` text,
  `art_rexseo_title` text,
  `art_rexseo_priority` varchar(255) DEFAULT '',
  `art_headline1` text,
  `art_headline2` text,
  PRIMARY KEY (`pid`),
  UNIQUE KEY `find_articles` (`id`,`clang`),
  KEY `id` (`id`),
  KEY `clang` (`clang`),
  KEY `re_id` (`re_id`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;

LOCK TABLES `rex_article` WRITE;
/*!40000 ALTER TABLE `rex_article` DISABLE KEYS */;
INSERT INTO `rex_article` VALUES 
  (1,1,0,'Home','Home',1,'',1,1,'|',1,1318676333,1343319290,1,0,'admin','admin',0,'','','Redaxo Community Demo','','bild_sardinien.jpg','','','||','','','','','','Redaxo Community Demo','Diese Demo darf frei verwendet werden!'),
  (3,3,0,'Interner Bereich','Interner Bereich',3,'',1,1,'|',1,1318676335,1321026791,1,0,'admin','admin',0,'','','','','bild_sardinien_2.jpg','1','','||','','','','','','',''),
  (4,4,17,'Registrierung','Registrierung',2,'',1,1,'|17|',1,1318676343,1342793381,1,0,'admin','admin',0,'','','','','bild_sardinien_2.jpg','2','','||','','','','','','',''),
  (5,5,17,'Mein Profil','Mein Profil',5,'',1,1,'|17|',1,1318676344,1341504929,1,0,'admin','admin',0,'','','','','','1','','','','','','','','',''),
  (6,6,17,'Mein Passwort ändern','Mein Passwort ändern',6,'',1,1,'|17|',1,1318676345,1344114026,1,0,'admin','admin',0,'','','','','','1','','','','','','','','',''),
  (8,8,0,'----------','----------',4,'',1,1,'|',0,1318675862,1318675862,1,0,'admin','admin',0,'','','','','','','','','','','','','','',''),
  (9,9,0,'Gruppe A','Gruppe A',6,'',1,1,'|',1,1318676347,1321026084,1,0,'admin','admin',0,'','','','','bild_venedig.jpg','1','2','|1|','','','','','','',''),
  (10,10,0,'Gruppe A oder B','Gruppe A oder B',7,'',1,1,'|',1,1318676349,1321026105,1,0,'admin','admin',0,'','','','','bild_venedig.jpg','1','2','|1|2|','','','','','','',''),
  (11,11,0,'Gruppe B','Gruppe B',8,'',1,1,'|',1,1318676349,1321026124,1,0,'admin','admin',0,'','','','','bild_sardinien.jpg','1','2','|2|','','','','','','',''),
  (12,12,17,'Login','Login',1,'',1,1,'|17|',1,1318676342,1320966811,1,0,'admin','admin',0,'','','','','','2','','','','','','','','',''),
  (13,13,21,'Impressum','Impressum',3,'',1,1,'|21|',1,1320936449,1344111323,1,0,'admin','admin',0,'','','','','','','','','','','','','','',''),
  (14,14,17,'Passwort vergessen','Passwort vergessen',3,'',1,1,'|17|',1,1318680928,1343993557,1,0,'admin','admin',0,'','','','','','2','','','','','','','','',''),
  (15,15,4,'Registrierungsbestätigung','Registrierung',0,'',0,2,'|17|4|',1,1318683387,1342795047,1,0,'admin','admin',0,'','','','','','','','','','','','','','',''),
  (16,16,22,'Gesperrter Bereich','Gesperrter Bereich',2,'',1,1,'|22|',1,1320836953,1321027445,1,0,'admin','admin',0,'','','','','','','','','','','','','','',''),
  (18,17,0,'_Meta','_Metanavigation für User',11,'',1,1,'|',0,1320819267,1320955048,1,0,'admin','admin',0,'','','','','bild_sardinien_2.jpg','','','||','','','','','','',''),
  (19,18,17,'Logout','Logout',4,'',1,1,'|17|',1,1320819351,1320969530,1,0,'admin','admin',0,'','','','','','1','','||','','','','','','',''),
  (20,19,0,'----------','----------',10,'',1,1,'|',0,1320819392,1320819477,1,0,'admin','admin',0,'','','','','','','','','','','','','','',''),
  (21,20,22,'Artikel exististiert nicht','Artikel exististiert nicht',1,'',1,1,'|22|',1,1320836935,1320819831,1,0,'admin','admin',0,'','','','','','','','','','','','','','',''),
  (22,21,0,'_Footernavigation','_Footernavigation',12,'',1,1,'|',0,1320819816,1320955076,1,0,'admin','admin',0,'','','','','bild_aleppo.jpg','','','||','','','','','','',''),
  (23,22,0,'_Extras','_Extras',13,'',1,1,'|',0,1320836921,1320836921,1,0,'admin','admin',0,'','','','','','','','','','','','','','',''),
  (28,27,0,'Keine Gruppe','Keine Gruppe',5,'',1,1,'|',1,1320940724,1321027427,1,0,'admin','admin',0,'','','','','bild_aleppo.jpg','1','3','||','','','','','','',''),
  (25,24,21,'Sitemap','Sitemap',2,'',1,1,'|21|',1,1320922919,1320952864,1,0,'admin','admin',0,'','','','','','','','','','','','','','',''),
  (26,25,21,'Kontakt','Kontakt',1,'',1,1,'|21|',1,1320922918,1320966446,1,0,'admin','admin',0,'','','','','','','','','','','','','','',''),
  (27,26,0,'Beispiele','Beispiele',2,'',1,1,'|',1,1320923099,1321026487,1,0,'admin','admin',0,'','','','','bild_venedig.jpg','','','||','','','','','','',''),
  (37,33,0,'Gruppe A und C','Gruppe A und C',9,'',1,1,'|',1,1321025433,1321027700,1,0,'admin','admin',0,'','','','','bild_sardinien_2.jpg','1','1','|1|2|3|','','','','','','','');
/*!40000 ALTER TABLE `rex_article` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `rex_article_slice`;
CREATE TABLE `rex_article_slice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clang` int(11) NOT NULL,
  `ctype` int(11) NOT NULL,
  `re_article_slice_id` int(11) NOT NULL,
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
  `file1` varchar(255) DEFAULT NULL,
  `file2` varchar(255) DEFAULT NULL,
  `file3` varchar(255) DEFAULT NULL,
  `file4` varchar(255) DEFAULT NULL,
  `file5` varchar(255) DEFAULT NULL,
  `file6` varchar(255) DEFAULT NULL,
  `file7` varchar(255) DEFAULT NULL,
  `file8` varchar(255) DEFAULT NULL,
  `file9` varchar(255) DEFAULT NULL,
  `file10` varchar(255) DEFAULT NULL,
  `filelist1` text,
  `filelist2` text,
  `filelist3` text,
  `filelist4` text,
  `filelist5` text,
  `filelist6` text,
  `filelist7` text,
  `filelist8` text,
  `filelist9` text,
  `filelist10` text,
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
  `php` text,
  `html` text,
  `article_id` int(11) NOT NULL,
  `modultyp_id` int(11) NOT NULL,
  `createdate` int(11) NOT NULL,
  `updatedate` int(11) NOT NULL,
  `createuser` varchar(255) NOT NULL,
  `updateuser` varchar(255) NOT NULL,
  `next_article_slice_id` int(11) DEFAULT NULL,
  `revision` int(11) NOT NULL,
  PRIMARY KEY (`id`,`re_article_slice_id`,`article_id`,`modultyp_id`),
  KEY `id` (`id`),
  KEY `clang` (`clang`),
  KEY `re_article_slice_id` (`re_article_slice_id`),
  KEY `article_id` (`article_id`),
  KEY `find_slices` (`clang`,`article_id`)
) ENGINE=MyISAM AUTO_INCREMENT=87 DEFAULT CHARSET=utf8;

LOCK TABLES `rex_article_slice` WRITE;
/*!40000 ALTER TABLE `rex_article_slice` DISABLE KEYS */;
INSERT INTO `rex_article_slice` VALUES 
  (1,0,1,84,'Es läuft :-)','h1','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',1,1,1318675624,1320924630,'admin','admin',0,0),
  (2,0,1,0,'Mein Passwort ändern','h1','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',6,1,1318675794,1320909583,'admin','admin',0,0),
  (4,0,1,0,'Interner Bereich','h1','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',3,1,1318675828,1320909544,'admin','admin',0,0),
  (5,0,1,0,'Registrierung','h1','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',4,1,1318675837,1320909565,'admin','admin',0,0),
  (6,0,1,0,'Mein Profil','h1','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',5,1,1318675846,1320909578,'admin','admin',0,0),
  (7,0,1,0,'Login','h1','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',12,1,1318676229,1320909557,'admin','admin',0,0),
  (8,0,1,7,'','','com_auth_form_info|label|Bitte einloggen|Benutzer wurde ausgeloggt|Login ist fehlgeschlagen|Benutzer wurde erfolgreich eingeloggt|\r\ncom_auth_form_login|label|Benutzername / E-Mail:\r\ncom_auth_form_password|label|Passwort:\r\ncom_auth_form_stayactive|auth|eingeloggt bleiben:|0','','','','0','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',12,3,1318676773,1320966811,'admin','admin',0,0),
  (9,0,1,5,'','','generate_key|activation_key\r\nhidden|status|0\r\n\r\nfieldset|label|Login-Daten:\r\n\r\ntext|email|E-Mail:*|\r\ntext|firstname|Vorname:*\r\nvalidate|empty|firstname|Bitte geben Sie Ihren Vornamen ein.\r\n\r\ntext|name|Nachname:*\r\nvalidate|empty|name|Bitte geben Sie Ihren Namen ein.\r\n\r\npassword|password|Ihr Passwort:*|\r\npassword|password_2|Passwort bestätigen:*||no_db\r\n\r\nhtml|required|<p class=\"form-required\">* Pflichtfelder</p>\r\n\r\ncaptcha|Bitte geben Sie den entsprechenden Sicherheitscode ein. Sollten Sie den Code nicht lesen können klicken Sie bitte auf die Grafik, um einen neuen Code zu generieren.|Sie haben den Sicherheitscode falsch eingegeben. \r\n\r\ncheckbox|newsletter|Bitte schicken Sie mir Benachrichtigungen über Neuigkeiten.\r\n\r\nvalidate|email|email|Bitte geben Sie die E-Mail ein.\r\nvalidate|unique|email|Diese E-Mail existiert schon|rex_com_user\r\nvalidate|empty|email|Bitte geben Sie Ihre e-Mail ein.\r\nvalidate|empty|password|Bitte geben Sie ein Passwort ein.\r\nvalidate|compare|password|password_2|Bitte geben Sie zweimal das gleiche Passwort ein\r\n\r\ncom_auth_password_hash|password_hash|password\r\n\r\naction|copy_value|email|login\r\naction|db|rex_com_user\r\naction|db2email|access_request_de|email|','','','h1. Vielen Dank für Ihre Anmeldung\r\n\r\nSind bekommen nun eine Mail mit Ihren Daten als Bestätigung','0','','','','2','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',4,3,1318680267,1342793381,'admin','admin',0,0),
  (12,0,1,6,'','','com_auth_load_user|userinfo|email,firstname,name\r\nobjparams|form_showformafterupdate|1\r\nshowvalue|email|E-Mail / Login:\r\ntext|firstname|Vorname:\r\nvalidate|empty|firstname|Bitte geben Sie Ihren Vornamen ein.\r\ntext|name|Nachname:\r\nvalidate|empty|name|Bitte geben Sie Ihren Namen ein.\r\naction|com_auth_db','','','Ihre Daten wurden aktualisiert','0','','','','2','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',5,3,1318683332,1341504929,'admin','admin',0,0),
  (10,0,1,11,'','','text|email|E-Mail-Adresse*|\r\n\r\ncaptcha|Bitte geben Sie den entsprechenden Sicherheitscode ein. Sollten Sie den Code nicht lesen können klicken Sie bitte auf die Grafik, um einen neuen Code zu generieren.|Sie haben den Sicherheitscode falsch eingegeben. \r\n\r\nvalidate|notEmpty|email|Bitte geben Sie eine E-Mail ein\r\nvalidate|existintable|email|rex_com_user|email|Diese E-Mail-Adresse ist nicht bei uns registriert.\r\n\r\naction|readtable|rex_com_user|email|email\r\naction|db2email|send_password_de|email','','','Vielen Dank. Ihnen wird Ihr Passwort nun zugeschickt','0','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',14,3,1318680945,1343993557,'admin','admin',0,0),
  (11,0,1,0,'Passwort vergessen','h1','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',14,1,1318680951,1320909570,'admin','admin',0,0),
  (13,0,1,2,'','','password|password|Neues Passwort:\r\npassword|password_2|Passwort wiederholen:||no_db\r\nvalidate|empty|password|Bitte geben Sie ein Passwort ein.\r\nvalidate|compare|password|password_2|Bitte geben Sie zweimal das gleiche Passwort ein\r\naction|showtext||<div class=\"xform\"><ul class=\"form_info\"><li>Ihre Daten wurden aktualisiert. Das neue Passwort ist ab sofort aktiv.</li></ul></div>||1\r\ncom_auth_password_hash|password|password|\r\naction|com_auth_db','','','','0','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',6,3,1318683363,1344114026,'admin','admin',0,0),
  (14,0,1,0,'','','hidden|status|1\r\nobjparams|submit_btn_show|0\r\nobjparams|send|1\r\n\r\nvalidate|com_auth_login|activation_key=rex_com_activation_key,email=rex_com_email|status=0|Zugang wurde bereits bestätigt oder ist schon fehlgeschlagen|status\r\n\r\naction|com_auth_db|update\r\naction|html|<b>Vielen Dank, Sie sind nun eingeloggt und haben Ihre E-Mail bestätigt</b>','','','','0','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',15,3,1318683393,1342795047,'admin','admin',0,0),
  (15,0,1,0,'Gesperrter Bereich','h1','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',16,1,1318684185,1321027445,'admin','admin',0,0),
  (54,0,1,0,'Kontakt','h1','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',25,1,1320954013,1320954013,'admin','admin',0,0),
  (17,0,1,0,'404 Artikel exististiert nicht','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',20,1,1320819831,1320819831,'admin','admin',0,0),
  (61,0,1,0,'Gruppe A','h1','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',9,1,1320957898,1320957898,'admin','admin',0,0),
  (73,0,1,0,'','','com_auth_form_logout|label|','','','','0','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',18,3,1320969530,1320969530,'admin','admin',0,0),
  (53,0,1,52,'','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',24,8,1320952864,1320952864,'admin','admin',0,0),
  (26,0,1,1,'Diese Demo soll zeigen, wie man mit den AddOns \"Community\" und \"Xform\" einen Loginbereich erstellen kann.\r\n\r\nDie Demo enthält Beispielseiten zur Registrierung, Zum Login und Logout, eine Funktion \"Passwort vergessen\" und eine Seite, um sein Profil zu bearbeiten.','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',1,4,1320922265,1321027608,'admin','admin',0,0),
  (80,0,1,26,'Navigation','h2','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',1,1,1321027626,1321027626,'admin','admin',0,0),
  (81,0,1,80,'Durch die Verwendung der \"Rex-Navigation-Factory\" wird bei der Erstellung der Navigation automatisch geprüft, welche Seite man als User sehen darf, und nur diese werden in der Navigation ausgegeben.\r\n\r\nDie Footer-Navigation wurde realisiert, indem per Navigation-Factory alle Kategorien der Footer-Kategorie ausgelesen werden.','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',1,4,1321027861,1321030421,'admin','admin',0,0),
  (27,0,1,45,'Typografie-Beispiele','h1','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',26,1,1320923127,1321026232,'admin','admin',0,0),
  (28,0,1,27,'Curabitur blandit tempus porttitor. Nullam id dolor id nibh ultricies vehicula ut id elit. Lorem dolor sit amet, consectetur adipiscing elit. ','h2','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',26,1,1320923137,1320923177,'admin','admin',0,0),
  (30,0,1,29,'','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',26,5,1320923429,1320923429,'admin','admin',0,0),
  (29,0,1,28,'Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor. Cras mattis consectetur purus sit amet fermentum. Curabitur blandit tempus porttitor. Vestibulum id ligula porta felis euismod semper. Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum.\r\n\r\nPraesent commodo cursus magna, vel scelerisque nisl consectetur et. Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit. Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum. Etiam porta sem malesuada magna mollis euismod. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui.','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',26,4,1320923158,1320923158,'admin','admin',0,0),
  (31,0,1,30,'Nullam id dolor id nibh ultricies vehicula ut id elit. Maecenas sed diam eget risus varius blandit sit amet non magna. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Aenean lacinia bibendum nulla sed consectetur. Sed posuere consectetur est at lobortis. Donec ullamcorper nulla non metus auctor fringilla. Etiam porta sem malesuada magna mollis euismod.\r\n\r\n* Aenean lacinia bibendum nulla sed consectetur.\r\n* Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget \r\n** efluz gelru geb\r\n** qwipruzg epqruzg zeqrb\r\n* Nisi erat porttitor ligula, eget lacinia odio sem nec elit.\r\n* Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.\r\n\r\nCum sociis \"interner Link zur Sitemap\":redaxo://1  et magnis dis parturient montes, nascetur ridiculus mus. Donec ullamcorper nulla non metus auctor fringilla. Cum sociis natoque penatibus *strong* magnis dis parturient montes, \"externer Link\":http://redaxo.org ridiculus mus. Nulla vitae elit, a _italic_ pharetra augue. Curabitur blandit tempus porttitor. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Integer -stroke- erat a ante venenatis dapibus posuere velit aliquet.\r\n\r\n# Aenean lacinia bibendum nulla sed consectetur.\r\n# Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget \r\n## efluz gelru geb\r\n## qwipruzg epqruzg zeqrb\r\n# Nisi erat porttitor ligula, eget lacinia odio sem nec elit.\r\n# Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.\r\n\r\n|_. Dies|_. ist|_. eine|_. Tabelle|\r\n|Commodo|Venenatis|Inceptos|Cursus|\r\n|Dapibus|Sem|Commodo|Euismod|\r\n|Adipiscing|Magna Ornare|Aenean|Dapibus|','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',26,4,1320923608,1321026487,'admin','admin',0,0),
  (32,0,2,0,'Hilfe','h2','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',1,1,1320923729,1320923729,'admin','admin',0,0),
  (33,0,2,34,'','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',1,5,1320923732,1320923732,'admin','admin',0,0),
  (34,0,2,32,'Fragen zu dieser Demo könnt Ihr selbstverständlich wie immer im \"Redaxo Forum\":http://www.redaxo.org/de/forum/ stellen!','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',1,4,1320923773,1321029042,'admin','admin',0,0),
  (84,0,2,83,'Eine ausführliche Dokumentation zur Community-Demo findet Ihr im \"WIKI von Redaxo\":http://www.redaxo.org/de/wiki/index.php?n=R4.CommunityAddOn','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',1,4,1321029158,1321034454,'admin','admin',0,0),
  (37,0,2,33,'Mach mit!','h2','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',1,1,1320923928,1320923928,'admin','admin',0,0),
  (38,0,2,37,'Selbstverständlich freuen wir uns wenn Ihr Euch mit an der Entwicklung der Demo beteiligt. \"Hier geht es zu github.com\":https://github.com/dergel/redaxo4_community!\r\n\r\nUnter diesem Github-Link findet man auch immer die aktuellste Version des Community-AddOns.','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',1,4,1320923960,1343319290,'admin','admin',0,0),
  (82,0,2,38,'','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',1,5,1321029116,1321029116,'admin','admin',0,0),
  (83,0,2,82,'Dokumentation im WIKI','h2','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',1,1,1321029129,1321030789,'admin','admin',0,0),
  (45,0,2,47,'http://www.facebook.com/REDAXO ','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',26,6,1320938942,1320938942,'admin','admin',0,0),
  (47,0,2,0,'Yeah!','h2','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',26,1,1320939086,1320939086,'admin','admin',0,0),
  (48,0,1,51,'Impressum','h1','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',13,1,1320939993,1320939993,'admin','admin',0,0),
  (60,0,1,0,'Keine Gruppe','h1','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',27,1,1320957885,1320957885,'admin','admin',0,0),
  (49,0,2,0,'','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',13,5,1320940187,1320940187,'admin','admin',0,0),
  (50,0,2,49,'Eigene Verwendung','h2','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',13,1,1320940202,1320940261,'admin','admin',0,0),
  (51,0,2,50,'Bei Verwendung dieses Templates muss natürlich das Impressum angepasst werden!','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',13,4,1320940254,1320940254,'admin','admin',0,0),
  (52,0,1,0,'Sitemap','h1','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',24,1,1320952858,1320952858,'admin','admin',0,0),
  (55,0,1,54,'','','text|name|Name\r\ntext|email|E-Mail\r\nvalidate|empty|email|Bitte geben Sie die E-Mail ein\r\nvalidate|email|email|Ihr E-Mail ist nicht korrekt\r\ntextarea|comment|Kommentar\r\ncaptcha|Bitte geben Sie den entsprechenden Sicherheitscode ein. Sollten Sie den Code nicht lesen können klicken Sie bitte auf die Grafik, um einen neuen Code zu generieren.|Sie haben den Sicherheitscode falsch eingegeben. \r\n\r\naction|db2email|contact_de|','','','h3. Vielen Dank für Ihren Kommentar.','0','','','','2','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',25,3,1320954106,1320966446,'admin','admin',0,0),
  (56,0,1,0,'Gruppe A oder B','h1','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',10,1,1320957507,1320957917,'admin','admin',0,0),
  (62,0,1,0,'Gruppe B','h1','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',11,1,1320957933,1320957933,'admin','admin',0,0),
  (59,0,1,48,'h2. redaxo c/o - Yakamara Media GmbH & Co. KG\r\n\r\nAnsprechpartner: Jan Kristinus\r\nKaiserstrasse 69\r\n60329 Frankfurt\r\n\r\nTel.: +49 (0)69 900.20.60.30\r\n\r\nE-Mail: info[at]redaxo.org\r\nwww: \"http://www.redaxo.org\":http://www.redaxo.org\r\n','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',13,4,1320957626,1344111002,'admin','admin',0,0),
  (69,0,1,59,'','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',13,5,1320964603,1320964603,'admin','admin',0,0),
  (70,0,1,69,'h2. Community Paket\r\n\r\n* Jan Kristinus / Umsetzung / \"Yakamara Media GmbH & Co. KG\":http://www.yakamara.de\r\n* Oliver Kreischer / Design / Umsetzung / \"Oliver Kreischer\":http://www.kreischer.de\r\n* Peter Bickel / Texte / \"Polarpixel\":http://www.polarpixel.de','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',13,4,1320964620,1344110969,'admin','admin',0,0),
  (71,0,1,70,'','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',13,5,1320964624,1320964624,'admin','admin',0,0),
  (72,0,1,71,'h2. AddOns\r\n\r\n* Xform AddOn / Jan Kristinus, \"Yakamara Media GmbH & Co. KG\":http://www.yakamara.de\r\n* Community AddOn\r\n** Jan Kristinus \"Yakamara Media GmbH & Co. KG\":http://www.yakamara.de\r\n** Markus Lorch \"IT-Kult\":http://www.it-kult.de','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',13,4,1320964636,1344111323,'admin','admin',0,0),
  (74,0,1,61,'Diese Seite bekommt man zu sehen, wenn man der Gruppe A zugewiesen ist.','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',9,4,1321026084,1321026084,'admin','admin',0,0),
  (75,0,1,56,'Diese Seite bekommt man zu sehen, wenn man der Gruppe A oder B zugewiesen ist.','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',10,4,1321026105,1321026105,'admin','admin',0,0),
  (76,0,1,62,'Diese Seite bekommt man zu sehen, wenn man der Gruppe B zugewiesen ist.','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',11,4,1321026124,1321026124,'admin','admin',0,0),
  (77,0,1,78,'Diese Seite bekommt man zu sehen, wenn man der Gruppe A UND C zugewiesen ist.','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',33,4,1321026178,1321027384,'admin','admin',0,0),
  (78,0,1,0,'Gruppe A und C','h1','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',33,1,1321026189,1321027379,'admin','admin',0,0),
  (79,0,1,4,'Diese Seite bekommt immer man zu sehen, wenn man eingeloggt ist. Es ist also egal, welcher Gruppe man zugewiesen ist.','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',3,4,1321026791,1321026791,'admin','admin',0,0),
  (85,0,1,86,'Die Demo zeigt außerdem, wie man Usergruppen anlegen und wie man per Metafeldern steuern kann, welche Gruppen welche Artikel sehen dürfen. Man kann zum Beispiel regeln, ob die Seiten nur für eingeloggte, nur für nicht eingeloggte oder für alle User aufrufbar sind.\r\n\r\nAußerdem kann man bestimmen, ob ein User in bestimmten Gruppen oder in jeder Gruppe sein kann oder muss.','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',1,4,1321029659,1321030223,'admin','admin',0,0),
  (86,0,1,81,'Gruppenberechtigung','h2','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',1,1,1321030236,1321030254,'admin','admin',0,0);
/*!40000 ALTER TABLE `rex_article_slice` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `rex_clang`;
CREATE TABLE `rex_clang` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `revision` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `rex_clang` WRITE;
/*!40000 ALTER TABLE `rex_clang` DISABLE KEYS */;
INSERT INTO `rex_clang` VALUES 
  (0,'deutsch',0);
/*!40000 ALTER TABLE `rex_clang` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `rex_com_comment`;
CREATE TABLE `rex_com_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ukey` text NOT NULL,
  `reply_to` text NOT NULL,
  `info_email` varchar(255) NOT NULL,
  `ckey` text NOT NULL,
  `status` varchar(255) NOT NULL,
  `update_datetime` varchar(255) NOT NULL,
  `create_datetime` varchar(255) NOT NULL,
  `www` text NOT NULL,
  `user_id` text NOT NULL,
  `email` text NOT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

LOCK TABLES `rex_com_comment` WRITE;
/*!40000 ALTER TABLE `rex_com_comment` DISABLE KEYS */;
INSERT INTO `rex_com_comment` VALUES 
  (1,'8fb9546c40d688fb9ca1d5644731ce1e8c4cca41','','0','','0','2014-07-25 13:47:38','2014-07-25 13:47:38','','1','','dsdsd');
/*!40000 ALTER TABLE `rex_com_comment` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `rex_com_group`;
CREATE TABLE `rex_com_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

LOCK TABLES `rex_com_group` WRITE;
/*!40000 ALTER TABLE `rex_com_group` DISABLE KEYS */;
INSERT INTO `rex_com_group` VALUES 
  (1,'Gruppe A'),
  (2,'Gruppe B'),
  (3,'Gruppe C');
/*!40000 ALTER TABLE `rex_com_group` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `rex_com_user`;
CREATE TABLE `rex_com_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` text,
  `password` text,
  `email` text,
  `status` text,
  `firstname` text,
  `name` text,
  `activation_key` text,
  `session_key` text,
  `rex_com_group` text,
  `newsletter_last_id` text,
  `newsletter` varchar(255) DEFAULT NULL,
  `password_hash` text NOT NULL,
  `last_action_time` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

LOCK TABLES `rex_com_user` WRITE;
/*!40000 ALTER TABLE `rex_com_user` DISABLE KEYS */;
INSERT INTO `rex_com_user` VALUES 
  (2,'jan.kristinus@yakamara.de','d819b82566e9b601d87e168d0dffe31cee1a9229','jan.kristinus@yakamara.de','1','Jan','Kristinus','ea3d6de54e3ae692963d8b2982b516f5','','1','','0','','1406293224');
/*!40000 ALTER TABLE `rex_com_user` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `rex_file`;
CREATE TABLE `rex_file` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `re_file_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `attributes` text,
  `filetype` varchar(255) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `originalname` varchar(255) DEFAULT NULL,
  `filesize` varchar(255) DEFAULT NULL,
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `createdate` int(11) NOT NULL,
  `updatedate` int(11) NOT NULL,
  `createuser` varchar(255) NOT NULL,
  `updateuser` varchar(255) NOT NULL,
  `revision` int(11) NOT NULL,
  `med_description` text,
  `med_copyright` text,
  `med_com_auth_media_legend` varchar(255) NOT NULL,
  `med_com_auth_media_comusers` varchar(255) NOT NULL,
  `med_com_groups` varchar(255) NOT NULL,
  PRIMARY KEY (`file_id`),
  KEY `re_file_id` (`re_file_id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

LOCK TABLES `rex_file` WRITE;
/*!40000 ALTER TABLE `rex_file` DISABLE KEYS */;
INSERT INTO `rex_file` VALUES 
  (2,0,1,'','image/jpeg','bild_aleppo.jpg','bild_aleppo.jpg','68402',930,170,'',1320954837,1320957306,'admin','admin',0,'','','','',''),
  (3,0,1,'','image/jpeg','bild_sardinien_2.jpg','bild_sardinien_2.jpg','86184',930,170,'',1320954844,1320957306,'admin','admin',0,'','','','',''),
  (4,0,1,'','image/jpeg','bild_sardinien.jpg','bild_sardinien.jpg','95664',930,230,'',1320954850,1344073452,'admin','admin',0,'','','','||','||'),
  (5,0,1,'','image/jpeg','bild_venedig.jpg','bild_venedig.jpg','90169',930,170,'',1320954856,1320957306,'admin','admin',0,'','','','','');
/*!40000 ALTER TABLE `rex_file` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `rex_file_category`;
CREATE TABLE `rex_file_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `re_id` int(11) NOT NULL,
  `path` varchar(255) NOT NULL,
  `createdate` int(11) NOT NULL,
  `updatedate` int(11) NOT NULL,
  `createuser` varchar(255) NOT NULL,
  `updateuser` varchar(255) NOT NULL,
  `attributes` text,
  `revision` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `re_id` (`re_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

LOCK TABLES `rex_file_category` WRITE;
/*!40000 ALTER TABLE `rex_file_category` DISABLE KEYS */;
INSERT INTO `rex_file_category` VALUES 
  (1,'01 Header',0,'|',1320954827,1320954827,'admin','admin','',0);
/*!40000 ALTER TABLE `rex_file_category` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `rex_module`;
CREATE TABLE `rex_module` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL,
  `ausgabe` text NOT NULL,
  `eingabe` text NOT NULL,
  `createuser` varchar(255) NOT NULL,
  `updateuser` varchar(255) NOT NULL,
  `createdate` int(11) NOT NULL,
  `updatedate` int(11) NOT NULL,
  `attributes` text,
  `revision` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

LOCK TABLES `rex_module` WRITE;
/*!40000 ALTER TABLE `rex_module` DISABLE KEYS */;
INSERT INTO `rex_module` VALUES 
  (1,'01 - Headline',0,'<REX_VALUE[2]>REX_VALUE[1]</REX_VALUE[2]>\r\n','<strong>Überschrift eingeben:</strong>\r\n<br/>\r\n<textarea name=\"VALUE[1]\" cols=\"100\" rows=\"2\" >REX_HTML_VALUE[1]</textarea>\r\n<br/><br/>\r\n<strong>Art: </strong>\r\n	<select name=\"VALUE[2]\">\r\n		<option value=\'h1\' <?php if (\"REX_VALUE[2]\" == \'h1\') echo \'selected\'; ?>>1. Überschrift (H1)</option>\r\n		<option value=\'h2\' <?php if (\"REX_VALUE[2]\" == \'h2\') echo \'selected\'; ?>>2. Überschrift (H2)</option>\r\n		<option value=\'h3\' <?php if (\"REX_VALUE[2]\" == \'h3\') echo \'selected\'; ?>>3. Überschrift (H3)</option>			\r\n	</select>\r\n\r\n','admin','admin',1318672102,1320875369,'',0),
  (3,'06 - XForm Formbuilder',0,'<?php\n\n// module:xform_basic_out\n// v0.2\n//--------------------------------------------------------------------------------\n\n$xform = new rex_xform;\nif (\"REX_VALUE[7]\" == 1) { $xform->setDebug(TRUE); }\n$form_data = \'REX_VALUE[3]\';\n$form_data = trim(str_replace(\"<br />\",\"\",rex_xform::unhtmlentities($form_data)));\n$xform->setFormData($form_data);\n$xform->setRedaxoVars(REX_ARTICLE_ID,REX_CLANG_ID); \n\n?>REX_PHP_VALUE[9]<?php\n\n// action - showtext\nif(\"REX_IS_VALUE[6]\" == \"true\")\n{\n  $html = \"0\"; // plaintext\n  if(\'REX_VALUE[11]\' == 1) $html = \"1\"; // html\n  if(\'REX_VALUE[11]\' == 2) $html = \"2\"; // textile\n$var = <<<EOT\nREX_HTML_VALUE[6]\nEOT;\n  $xform->setActionField(\"showtext\",array(\n      $var,\n      \'<div class=\"rex-message\"><div class=\"rex-info\"><p>\',\n      \'</p></div></div>\',\n      $html // als HTML interpretieren\n    )\n  );\n}\n\n$form_type = \"REX_VALUE[1]\";\n\n// action - email\nif ($form_type == \"1\" || $form_type == \"2\" || $form_type == \"3\")\n{\n  $mail_from = $REX[\'ERROR_EMAIL\'];\n  if(\"REX_VALUE[2]\" != \"\") $mail_from = \"REX_VALUE[2]\";\n  $mail_to = $REX[\'ERROR_EMAIL\'];\n  if(\"REX_VALUE[12]\" != \"\") $mail_to = \"REX_VALUE[12]\";\n  $mail_subject = \"REX_VALUE[4]\";\n  $mail_body = str_replace(\"<br />\",\"\",rex_xform::unhtmlentities(\'REX_VALUE[5]\'));\n  $xform->setActionField(\"email\", array(\n      $mail_from,\n      $mail_to,\n      $mail_subject,\n      $mail_body\n    )\n  );\n}\n\n// action - db\nif ($form_type == \"0\" || $form_type == \"2\" || $form_type == \"3\")\n{\n  $xform->setObjectparams(\'main_table\', \'REX_VALUE[8]\');\n  \n  //getdata\n  if (\"REX_VALUE[10]\" != \"\")\n    $xform->setObjectparams(\"getdata\",TRUE);\n  \n  $xform->setActionField(\"db\", array(\n      \"REX_VALUE[8]\", // table\n      $xform->objparams[\"main_where\"], // where\n      )\n    );\n}\n\necho $xform->getForm();\n\n?>','<?php\n\n// module:xform_basic_in\n// v0.2.1\n// --------------------------------------------------------------------------------\n\n// DEBUG SELECT\n////////////////////////////////////////////////////////////////////////////////\n$dbg_sel = new rex_select();\n$dbg_sel->setName(\'VALUE[7]\');\n$dbg_sel->setSize(1);\n$dbg_sel->addOption(\'inaktiv\',\'0\');\n$dbg_sel->addOption(\'aktiv\',\'1\');\n$dbg_sel->setSelected(\'REX_VALUE[7]\');\n$dbg_sel = $dbg_sel->get();\n\n\n// TABLE SELECT\n////////////////////////////////////////////////////////////////////////////////\n$gc = rex_sql::factory();\n$gc->setQuery(\'SHOW TABLES\');\n$tables = $gc->getArray();\n$tbl_sel = new rex_select;\n$tbl_sel->setName(\'VALUE[8]\');\n$tbl_sel->setSize(1);\n$tbl_sel->addOption(\'Keine Tabelle ausgewählt\', \'\');\nforeach ($tables as $key => $value)\n{\n  $tbl_sel->addOption(current($value), current($value));\n}\n$tbl_sel->setSelected(\'REX_VALUE[8]\');\n$tbl_sel = $tbl_sel->get();\n\n\n// PLACEHOLDERS\n////////////////////////////////////////////////////////////////////////////////\n$xform = new rex_xform;\n$form_data = \'REX_VALUE[3]\';\n$form_data = trim(str_replace(\'<br />\',\'\',rex_xform::unhtmlentities($form_data)));\n$xform->setFormData($form_data);\n$xform->setRedaxoVars(REX_ARTICLE_ID,REX_CLANG_ID);\n$placeholders = \'\';\nif(\'REX_VALUE[3]\'!=\'\')\n{\n$ignores = array(\'html\',\'validate\',\'action\');\n  $placeholders .= \'  <strong class=\"hint\">Platzhalter: <span>[<a href=\"#\" id=\"xform-placeholders-help-toggler\">?</a>]</span></strong>\n  <p id=\"xform-placeholders\">\'.PHP_EOL;\nforeach($xform->objparams[\'form_elements\'] as $e)\n{\n  if(!in_array($e[0],$ignores) && isset($e[1]))\n  {\n      $placeholders .= \'<span>###\'.$e[1].\'###</span> \'.PHP_EOL;\n  }\n}\n  $placeholders .= \'  </p>\'.PHP_EOL;\n}\n\n\n// OTHERS\n////////////////////////////////////////////////////////////////////////////////\n$row_pad = 1;\n\n$sel = \'REX_VALUE[1]\';\n$db_display   = ($sel==\'\' || $sel==1) ?\'style=\"display:none\"\':\'\';\n$mail_display = ($sel==\'\' || $sel==0) ?\'style=\"display:none\"\':\'\';\n\n?>\n\n<style type=\"text/css\" media=\"screen\">\n  /*BAISC MODUL STYLE*/\n  #xform-modul                       {margin:0;padding:0;line-height:25px;}\n  #xform-modul fieldset              {background:#E4E1D1;margin:-20px 0 0 0;padding: 4px 10px 10px 10px;-moz-border-radius:6px;-webkit-border-radius:6px;border-radius:6px;}\n  #xform-modul fieldset legend       {display:block !important;position:relative !important;height:auto !important;top:0 !important;left:0 !important;width:100% !important;margin:0 0 0 0 !important;padding:30px 0 0 0px !important;background:transparent !important;border-bottom:1px solid #B1B1B1 !important;color:gray;font-size:14px;font-weight:bold;}\n  #xform-modul fieldset legend em    {font-size:10px;font-weight:normal;font-style:normal;}\n  #xform-modul fieldset strong.label,\n  #xform-modul fieldset label        {display:inline-block !important;width:150px !important;font-weight:bold;}\n  #xform-modul fieldset label span   {font-weight:normal;}\n  #xform-modul input,\n  #xform-modul select                {width:460px;border:auto;margin:0 !important;padding:0 !important;}\n  #xform-modul input[type=\"checkbox\"]{width:auto;}\n  #xform-modul hr                    {border:0;height:0;margin:4px 0 4px 0;padding:0;border-top:1px solid #B1B1B1 !important;clear:left;}\n  #xform-modul a.blank               {background:url(\"../files/addons/be_style/plugins/agk_skin/popup.gif\") no-repeat 100% 0;padding-right:17px;}\n  #xform-modul #modulinfo            {font-size:10px;text-align:right;}\n  /*XFORM MODUL*/\n  #xform-modul textarea              {min-height:50px;font-family:monospace;font-size:12px;}\n  #xform-modul pre                   {clear:left;}\n  #xform-modul strong span           {font-weight:normal;}\n  #xform-modul .help                 {display:none;color:#2C8EC0;line-height:12px;}\n  #xform-modul .area-wrapper         {background:white;border:1px solid #737373;margin-bottom:10px;width:100%;}\n  #xform-modul .fullwidth            {width:100% !important;}\n  #xform-modul #thx-markup           {width:auto !important;}\n  #xform-modul #thx-markup input     {width:auto !important;}\n  #xform-modul #xform-placeholders-help,\n  #xform-modul #xform-where-help     {display:none;}\n  #xform-modul #xform-placeholders,\n  #xform-modul #xform-classes-showhelp {background:white;border:1px solid #737373;margin-bottom:10px;width:100%;}\n  #xform-modul #xform-placeholders {padding:4px 10px;float:none;width:auto;}\n  #xform-modul #xform-placeholders span:hover {color:red;cursor:pointer;}\n  #xform-modul em.hint               {color:silver;margin:0;padding:0 0 0 10px;}\n  /*SHOWHELP OVERRIDES*/\n  #xform-modul ul.xform.root         {border:0;outline:0;margin:4px 0;padding:0;width:100%;background:transparent;}\n  #xform-modul ul.xform              {font-size:1.1em;line-height:1.4em;}\n</style>\n\n\n<div id=\"xform-modul\">\n<fieldset>\n  <legend>Formular</legend>\n\n  <label>DebugModus:</label>\n  <?php echo $dbg_sel;?>\n\n  <hr />\n\n  <label class=\"fullwidth\">Felddefinitionen:</label>\n  <textarea name=\"VALUE[3]\" id=\"xform-form-definition\" class=\"fullwidth\" rows=\"<?php echo (count(explode(\"\\r\",\'REX_VALUE[3]\'))+$row_pad);?>\">REX_VALUE[3]</textarea>\n\n  <strong class=\"label\">Verfügbare Feld-Klassen:</strong>\n  <div id=\"xform-classes-showhelp\">\n    <?php echo rex_xform::showHelp(true,true); ?>\n  </div><!-- #xform-classes-showhelp -->\n\n  <div id=\"thx-markup\"><strong>Meldung bei erfolgreichen Versand:</strong> (\n    <input type=\"radio\" name=\"VALUE[11]\" value=\"0\" <?php if(\"REX_VALUE[11]\" == \"0\") echo \'checked=\"checked\"\'; ?>> Plaintext\n    <input type=\"radio\" name=\"VALUE[11]\" value=\"1\" <?php if(\"REX_VALUE[11]\" == \"1\") echo \'checked=\"checked\"\'; ?>> HTML\n    <input type=\"radio\" name=\"VALUE[11]\" value=\"2\" <?php if(\"REX_VALUE[11]\" == \"2\") echo \'checked=\"checked\"\'; ?>> Textile)\n  </div><!-- #thx-markup -->\n  <textarea name=\"VALUE[6]\" id=\"xform-thx-message\" class=\"fullwidth\" rows=\"<?php echo (count(explode(\"\\r\",\'REX_VALUE[6]\'))+$row_pad);?>\">REX_VALUE[6]</textarea>\n\n</fieldset>\n\n\n<fieldset>\n  <legend>Vordefinierte Aktionen</legend>\n\n  <label>Bei Submit:</label>\n  <select name=\"VALUE[1]\" id=\"xform-action-select\" style=\"width:auto;\">\n    <option value=\"\"  <?php if(\"REX_VALUE[1]\" == \"\")  echo \" selected \"; ?>>Nichts machen (actions im Formular definieren)</option>\n    <option value=\"0\" <?php if(\"REX_VALUE[1]\" == \"0\") echo \" selected \"; ?>>Nur in Datenbank speichern oder aktualisieren wenn \"main_where\" gesetzt ist</option>\n    <option value=\"1\" <?php if(\"REX_VALUE[1]\" == \"1\") echo \" selected \"; ?>>Nur E-Mail versenden</option>\n    <option value=\"2\" <?php if(\"REX_VALUE[1]\" == \"2\") echo \" selected \"; ?>>E-Mail versenden und in Datenbank speichern</option>\n    <!--  <option value=\"3\" <?php if(\"REX_VALUE[1]\" == \"3\") echo \" selected \"; ?>>E-Mail versenden und Datenbank abfragen</option> -->\n  </select>\n\n</fieldset>\n\n\n<fieldset id=\"xform-mail-fieldset\" <?php echo $mail_display;?> >\n  <legend>Emailversand:</legend>\n\n  <label>Absender:</label>\n  <input type=\"text\" name=\"VALUE[2]\" value=\"REX_VALUE[2]\" />\n\n  <label>Empfänger:</label>\n  <input type=\"text\" name=\"VALUE[12]\" value=\"REX_VALUE[12]\" />\n\n  <label>Subject:</label>\n  <input type=\"text\" name=\"VALUE[4]\" value=\"REX_VALUE[4]\" />\n  <label class=\"fullwidth\">Mailbody:</label>\n  <textarea id=\"xform-mail-body\" class=\"fullwidth\" name=\"VALUE[5]\" rows=\"<?php echo (count(explode(\"\\r\",\'REX_VALUE[5]\'))+$row_pad);?>\">REX_VALUE[5]</textarea>\n\n    <?php echo $placeholders;?>\n\n  <ul class=\"help\" id=\"xform-placeholders-help\">\n    <li>Die Platzhalter ergeben sich aus den obenstehenden Felddefinitionen.</li>\n    <li>Per click können einzelne Platzhalter in den Mail-Body kopiert werden.</li>\n    <li>Aktualisierung der Platzhalter erfolgt über die Aktualisierung des Moduls.</li>\n  </ul>\n\n\n</fieldset>\n\n\n<fieldset id=\"xform-db-fieldset\" <?php echo $db_display;?> >\n  <legend>Datenbank Einstellungen</legend>\n\n  <label>Tabelle wählen <span>[<a href=\"#\" id=\"xform-db-help-toggler\">?</a>]</span></label>\n  <?php echo $tbl_sel;?>\n  <ul class=\"help\" id=\"xform-db-select-help\">\n    <li>Diese Tabelle gilt auch bei Uniqueabfragen (Pflichtfeld=2) siehe oben</li>\n  </ul>\n\n  <hr />\n\n  <label for=\"getdatapre\">Daten initial aus DB holen</label>\n  <input id=\"getdatapre\" type=\"checkbox\" value=\"1\" name=\"VALUE[10]\" <?php if(\"REX_VALUE[10]\" != \"\") echo \'checked=\"checked\"\'; ?> />\n\n  <div id=\"db_data\">\n    <hr />\n    <label>Where Klausel: <span>[<a href=\"#\" id=\"xform-xform-where-help-toggler\">?</a>]</span></label>\n    <textarea name=\"VALUE[9]\" cols=\"30\" id=\"xform-db-where\" class=\"fullwidth\"rows=\"<?php echo (count(explode(\"\\r\",\'REX_VALUE[9]\'))+$row_pad);?>\">REX_VALUE[9]</textarea>\n    <ul class=\"help\" id=\"xform-where-help\">\n      <li>PHP erlaubt. Beispiel: <em>$xform-&gt;setObjectparams(\"main_where\",$where);</em></li>\n      <li>Die Benutzereingaben aus dem Formular können mittels Platzhaltern (Schema: ###<em>FELDNAME</em>###) in der WHERE Klausel verwendet werden - Beispiel: text|myname|Name|1 -> Platzhalter: ###myname###</li>\n    </ul>\n  </div><!-- #db_data -->\n\n  </fieldset>\n\n  <p id=\"modulinfo\">XForm Formbuilder v0.2.1</p>\n\n</div><!-- #xform-modul -->\n\n<script type=\"text/javascript\">\n<!--\n(function($){\n\n  // FIX WEBKIT CSS QUIRKS\n  if ($.browser.webkit) {\n    $(\'#xform-modul textarea\').css(\'min-height\',\'70px\');\n    $(\'#xform-modul textarea\').css(\'width\',\'701px\');\n    $(\'#xform-modul fieldset\').css(\'width\',\'705px\');\n  }\n\n  // AUTOGROW BY ROWS\n  $(\'#xform-modul textarea\').keyup(function(){\n    var rows = $(this).val().split(/\\r?\\n|\\r/).length + <?php echo $row_pad;?>;\n    $(this).attr(\'rows\',rows);\n  });\n\n  // TOGGLERS\n  $(\'#xform-placeholders-help-toggler\').click(function(){\n    $(\'#xform-placeholders-help\').toggle(50);return false;\n  });\n  $(\'#xform-xform-where-help-toggler\').click(function(){\n    $(\'#xform-where-help\').toggle(50);return false;\n  });\n  $(\'#xform-db-help-toggler\').click(function(){\n    $(\'#xform-db-select-help\').toggle(50);return false;\n  });\n\n\n  // INSERT PLACEHOLDERS\n  $(\'#xform-placeholders span\').click(function(){\n    newval = $(\'#xform-mail-body\').val()+\' \'+$(this).html();\n    $(\'#xform-mail-body\').val(newval);\n  });\n\n  // TOGGLE MAIL/DB PANELS\n  $(\'#xform-action-select\').change(function(){\n    switch($(this).val()){\n      case \'\':\n        $(\'#xform-db-fieldset\').hide(0);\n        $(\'#xform-mail-fieldset\').hide(0);\n        break;\n      case \'1\':\n        $(\'#xform-db-fieldset\').hide(0);\n        $(\'#xform-mail-fieldset\').show(0);\n        break;\n      case \'0\':\n        $(\'#xform-db-fieldset\').show(0);\n        $(\'#xform-mail-fieldset\').hide(0);\n        break;\n      case \'2\':\n      case \'3\':\n        $(\'#xform-db-fieldset\').show(0);\n        $(\'#xform-mail-fieldset\').show(0);\n        break;\n    }\n  });\n\n})(jQuery)\n//-->\n</script>','','admin',0,1321026132,'',0),
  (4,'02 - Text',0,'<?php\r\nif(OOAddon::isAvailable(\'textile\'))\r\n{\r\n  $textile = \'\';\r\n  if(REX_IS_VALUE[1])\r\n  {\r\n    $textile = htmlspecialchars_decode(\"REX_VALUE[1]\");\r\n    $textile = str_replace(\"<br />\",\"\",$textile);\r\n    $textile = rex_a79_textile($textile);\r\n    print \'<div class=\"txt-img\">\'. $textile . \'</div>\';\r\n  }\r\n}else\r\n{\r\n  echo rex_warning(\'Dieses Modul benötigt das \"textile\" Addon!\');\r\n}\r\n?> ','<?php\r\nif(OOAddon::isAvailable(\'textile\'))\r\n{\r\n?>\r\n<strong>Fliesstext</strong>:<br />\r\n<textarea name=\"VALUE[1]\" cols=\"120\" rows=\"10\" class=\"inp100\">REX_VALUE[1]</textarea>\r\n<?php\r\n\r\nif(OOAddon::isAvailable(\'markitup\')) \r\n{\r\n  a287_markitup::markitup(\'textarea.inp100\');\r\n}\r\n\r\necho \'<br />\';\r\nrex_a79_help_overview();\r\n\r\n}else {\r\n  echo rex_warning(\'Dieses Modul benötigt das \"textile\" Addon!\');\r\n}\r\n\r\n?>','admin','admin',1320819192,1321026597,'',0),
  (5,'03 - Trenner',0,'<span class=\"divider\"></span>','Keine Eingabe nötig','admin','admin',1320819210,1320923443,'',0),
  (6,'05 - Facebook-Likebox',0,'<iframe src=\"//www.facebook.com/plugins/likebox.php?href=<?php\r\n\r\n$url = \"REX_VALUE[1]\";\r\necho urlencode($url);\r\n// http%3A%2F%2Fwww.facebook.com%2Fplatform\r\n\r\n?>&amp;width=245&amp;height=850&amp;colorscheme=light&amp;show_faces=true&amp;border_color&amp;stream=true&amp;header=true&amp;appId=\" scrolling=\"no\" frameborder=\"0\" style=\"border:none; overflow:hidden; width:245px; height:850px;\" allowTransparency=\"true\"></iframe>','Bitte komplette Facebook eingeben: z.B. http://www.facebook.com/REDAXO\r\n<br /><input type=\"text\" name=\"VALUE[1]\" value=\"REX_VALUE[1]\" />','admin','admin',1320821236,1321026123,'',0),
  (8,'04 - Sitemap',0,'<?php\r\n\r\n$nav = rex_navigation::factory();\r\necho \'<div class=\"sitemap\">\';\r\necho $nav->get(0,2,TRUE,TRUE);\r\necho \'</div>\';\r\n\r\n\r\necho \'<h2>Footernavigation</h2><div class=\"sitemap\">\';\r\necho $nav->get(21,-1,FALSE,TRUE);\r\necho \'</div>\';\r\n\r\n\r\necho \'<h2>Headernavigation</h2><div class=\"sitemap\">\';\r\necho $nav->get(17,-1,FALSE,TRUE);\r\necho \'</div>\';\r\n\r\n?>','Keine Eingabe nötig','admin','admin',1320952839,1321026117,'',0);
/*!40000 ALTER TABLE `rex_module` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `rex_module_action`;
CREATE TABLE `rex_module_action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_id` int(11) NOT NULL,
  `action_id` int(11) NOT NULL,
  `revision` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `rex_template`;
CREATE TABLE `rex_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `content` text,
  `active` tinyint(1) DEFAULT NULL,
  `createuser` varchar(255) NOT NULL,
  `updateuser` varchar(255) NOT NULL,
  `createdate` int(11) NOT NULL,
  `updatedate` int(11) NOT NULL,
  `attributes` text,
  `revision` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

LOCK TABLES `rex_template` WRITE;
/*!40000 ALTER TABLE `rex_template` DISABLE KEYS */;
INSERT INTO `rex_template` VALUES 
  (1,'','01 . Template','REX_TEMPLATE[4]REX_TEMPLATE[2]<!DOCTYPE HTML>\r\n<html>\r\n\r\n<head>\r\n	<base href=\"<?php echo $REX[\'SERVER\']; ?>\" />\r\n	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\r\n	<title><?php echo $REX[\'SERVERNAME\']; ?></title>\r\n	<meta name=\"robots\" content=\"index,follow\" />\r\n\r\n	<link rel=\"icon\" href=\"<?php echo $REX[\"HTDOCS_PATH\"]; ?>layout/css/fav.ico\" />\r\n	<link href=\"<?php echo $REX[\"HTDOCS_PATH\"]; ?>layout/css/main.css\" type=\"text/css\" media=\"screen\" rel=\"stylesheet\" />\r\n</head>\r\n\r\n<?php\r\n\r\n// ----- Startseite wird \"markiert\", damit das CSS entsprechend reagieren kann\r\nif ($REX[\'START_ARTICLE_ID\'] == REX_ARTICLE_ID) {\r\n	echo \'<body id=\"homepage\">\';\r\n} else {\r\n	echo \'<body id=\"other\">\';\r\n}\r\n\r\n// ----- Headerbild wird von oberster Ebene vererbt\r\n$metafile = \"\";\r\n$path = explode(\"|\",substr($this->getValue(\"path\").$this->getValue(\"article_id\"),1));\r\nforeach($path as $p)\r\n{\r\n  if($a = OOArticle::getArticleById($p)) {\r\n    if($a->getValue(\"art_file\") != \"\")\r\n      $metafile = $a->getValue(\"art_file\");\r\n  }\r\n}\r\n\r\n?>\r\n\r\n<div id=\"header\">\r\n\r\n<?php // Meta-Navigation\r\n	echo \'<div class=\"metanavi\">\';\r\n	$nav = rex_navigation::factory();\r\n	echo $nav->get(17,1,TRUE,TRUE); \r\n	echo \'</div>\';\r\n?>\r\n\r\n	<div class=\"userinfo\">REX_TEMPLATE[3]</div>\r\n\r\n</div><!-- header -->\r\n\r\n<div id=\"wrapper\">\r\n	<div id=\"logo\">\r\n		<a href=\"/\" title=\"<?php echo $REX[\"SERVERNAME\"]; ?> - Startseite\" >Zur Startseite</a>\r\n	</div> <!-- logo -->\r\n\r\n<?php // Navigation\r\n	echo \'<div id=\"mainnavi\">\'.\"\\r\\n\";\r\n	$nav = rex_navigation::factory();\r\n	echo $nav->get(0,1,TRUE,TRUE); \r\n	echo \"\\r\\n\";\r\n	echo \'</div>\'.\"\\r\\n\";\r\n?>\r\n\r\n	<?php\r\n		// Startseite\r\n		\r\n		if ($REX[\'START_ARTICLE_ID\'] == REX_ARTICLE_ID) {\r\n			\r\n			echo \'<div class=\"imageHome\">\';\r\n			if($metafile != \"\")\r\n				echo  \'<img src=\"\'.$REX[\"HTDOCS_PATH\"].\'files/\'.$metafile.\'\" alt=\"\" width=\"930\" height=\"230\" />\';\r\n			echo \'</div>\';\r\n			echo \'<div class=\"imageHomeShadow\"></div>\'.\"\\r\\n\";\r\n\r\n			$headline1 = $this->getValue(\"art_headline1\");\r\n			$headline2 = $this->getValue(\"art_headline2\");\r\n\r\n			echo \'<div class=\"headlines\">\'.\"\\r\\n\";\r\n			echo \'<h1>\'.$headline1.\'</h1>\'.\"\\r\\n\";\r\n			echo \'<h2>\'.$headline2.\'</h2>\'.\"\\r\\n\";\r\n			echo \'</div><!-- headlines -->\'.\"\\r\\n\";\r\n			\r\n		} else {\r\n\r\n			echo \'<div class=\"imageOther\">\'.\"\\r\\n\";\r\n			if($metafile != \"\")\r\n				echo \'<img src=\"\'.$REX[\"HTDOCS_PATH\"].\'files/\'.$metafile.\'\" width=\"930\" height=\"170\" alt=\"\" />\'.\"\\r\\n\";\r\n			echo \'</div>\'.\"\\r\\n\";\r\n			echo \'<div class=\"imageOtherShadow\"></div>\'.\"\\r\\n\";\r\n		}\r\n	?>\r\n\r\n<div id=\"content\">\r\n\r\n	<div id=\"left\">\r\n		REX_ARTICLE[ctype=1]\r\n	</div>\r\n\r\n	<div id=\"right\">\r\n		\r\n	<?php // Navigation\r\n		if ($REX[\'START_ARTICLE_ID\'] != REX_ARTICLE_ID) {\r\n			$P = explode(\"|\",$this->getValue(\"path\").$this->getValue(\"article_id\").\"|\");\r\n			$rexnav2 = rex_navigation::factory();\r\n			echo \'<div id=\"subnavi\">\';	\r\n			echo $rexnav2->get($P[1],3,TRUE,TRUE);\r\n			echo \'</div>\';\r\n		}\r\n\r\n	?>\r\n		REX_ARTICLE[ctype=2]\r\n	</div>\r\n\r\n</div> <!-- content -->\r\n\r\n</div><!-- /wrapper -->\r\n<div id=\"footer\">\r\n	<div class=\"footerleft\">\r\n	<p>&copy;  by <a href=\"http://www.redaxo.org\">www.redaxo.org</a>, <a href=\"http://www.yakamara.de\">www.yakamara.de</a></p></div>\r\n	<div class=\"footerright\">\r\n\r\n<?php // Meta-Navigation\r\n	echo \'<div class=\"metanavi\">\';\r\n	$nav = rex_navigation::factory();\r\n	echo $nav->get(21,1,TRUE,TRUE); \r\n	echo \'</div>\';\r\n?>\r\n\r\n	</div>\r\n</div><!-- footer -->\r\n\r\n</body>\r\n</html>',1,'admin','admin',1362350583,1362350583,'a:3:{s:10:\"categories\";a:1:{s:3:\"all\";s:1:\"1\";}s:5:\"ctype\";a:2:{i:1;s:6:\"Inhalt\";i:2;s:13:\"rechte Spalte\";}s:7:\"modules\";a:3:{i:1;a:1:{s:3:\"all\";s:1:\"1\";}i:2;a:1:{s:3:\"all\";s:1:\"1\";}i:3;a:1:{s:3:\"all\";s:1:\"1\";}}}',0),
  (2,'','02 - Header','<?php\r\n\r\nheader(\'Content-Type: text/html; charset=utf-8\');\r\n\r\n/**\r\n * Artikel/Kategorie online? Wenn nein dann auf die Startseite\r\n */\r\n\r\nif ($this->getValue(\'status\') == 0)\r\n{\r\n  // Weiterleitung für Artikel\r\n  header (\'HTTP/1.0 404 Not Found\');\r\n  header(\'Location: \'.$REX[\'SERVER\'].rex_getUrl($REX[\'NOTFOUND_ARTICLE_ID\']));\r\n  exit;\r\n}\r\n\r\n?>',0,'admin','admin',1321029300,1321029300,'a:3:{s:10:\"categories\";a:1:{s:3:\"all\";s:1:\"1\";}s:5:\"ctype\";a:0:{}s:7:\"modules\";a:1:{i:1;a:1:{s:3:\"all\";s:1:\"1\";}}}',0),
  (3,'','03 - Userinfo','<?php\r\n\r\nif(rex_com_auth::getUser()) {\r\n\r\n  echo \'Sie sind eingeloggt als: \';\r\n\r\n  $name = rex_com_auth::getUser()->getValue(\"firstname\");\r\n  $name .= \" \".rex_com_auth::getUser()->getValue(\"name\");\r\n  echo \'<strong>\'.htmlspecialchars($name).\'</strong>\';\r\n\r\n}else {\r\n\r\n  echo \'Sie sind nicht eingeloggt.\';\r\n\r\n}\r\n\r\n?>',0,'admin','admin',1341503406,1341503406,'a:3:{s:10:\"categories\";a:1:{s:3:\"all\";s:1:\"1\";}s:5:\"ctype\";a:0:{}s:7:\"modules\";a:1:{i:1;a:1:{s:3:\"all\";s:1:\"1\";}}}',0),
  (4,'','04 . Community-Installationshilfe','<?php\r\n\r\n$err = TRUE;\r\n\r\n$addons = array(\r\n		\"textile\" => array(), \r\n		\"phpmailer\" => array(), \r\n		\"xform\" => array(\"email\", \"manager\", \"setup\"),\r\n//		\"rexseo\" => array(),\r\n		\"community\" => array(\"auth\",\"group\")\r\n	);\r\n\r\n$m = array();\r\nforeach($addons as $addon => $plugins) {\r\n  if(!OOAddon::isAvailable($addon)) {\r\n    $m[] = \'Bitte im REDAXO das `\'.$addon.\'` AddOn installieren UND aktivieren\';\r\n  }elseif(count($plugins)>0)\r\n  {\r\n  	 foreach($plugins as $plugin) {\r\n      if(!OOPlugin::isAvailable($addon,$plugin)) {\r\n        $m[] = \'Bitte in REDAXO das `\'.$plugin.\'` Plugin [\'.$addon.\'] installieren UND aktivieren\';\r\n      }\r\n  	 }\r\n  }\r\n}\r\n\r\nif(count($m)>0) {\r\n  echo implode(\"<br />\",$m);\r\n  exit;\r\n}\r\n\r\n?>',0,'admin','admin',1341487064,1341487064,'a:3:{s:10:\"categories\";a:1:{s:3:\"all\";s:1:\"1\";}s:5:\"ctype\";a:0:{}s:7:\"modules\";a:1:{i:1;a:1:{s:3:\"all\";s:1:\"1\";}}}',0);
/*!40000 ALTER TABLE `rex_template` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `rex_xform_email_template`;
CREATE TABLE `rex_xform_email_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `mail_from` varchar(255) NOT NULL DEFAULT '',
  `mail_from_name` varchar(255) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `body` text NOT NULL,
  `body_html` text NOT NULL,
  `attachments` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

LOCK TABLES `rex_xform_email_template` WRITE;
/*!40000 ALTER TABLE `rex_xform_email_template` DISABLE KEYS */;
INSERT INTO `rex_xform_email_template` VALUES 
  (1,'access_request_de','no_reply@redaxo.org','REDAXO Community Demo','REDAXO Community Demo: Bitte bestätigen Sie Ihre Anmeldung !','Login/E-Mail: ###email###\r\nName: ###firstname###\r\nNachname: ###name###\r\nE-Mail: ###email###\r\n\r\n###REX_SERVER###index.php?article_id=15&rex_com_activation_key=***activation_key***&rex_com_user_id=1&rex_com_email=***email***','',''),
  (2,'send_password_de','no_reply@redaxo.org','REDAXO Community Demo','REDAXO Community Demo: Passwort vergessen !','Guten Tag ###firstname### ###name###, \r\n\r\nsie haben für \"###REX_SERVERNAME###\" Ihr Passwort angefordert. Hier erhalten Sie Ihre Login Daten: \r\n\r\nLogin: ###email###\r\nPasswort: ###password###\r\n\r\nSollten Sie diese E-Mail nicht angefordert haben bzw. Ihr Passwort\r\nnicht vergessen haben, können Sie diese E-Mail löschen.','',''),
  (3,'contact_de','no_reply@redaxo.org','REDAXO Community Demo','REDAXO Community Demo: Das Kontaktformular wurde ausgefüllt','Name: ###name###\r\nE-Mail: ###email###\r\n\r\nKommentar:\r\n###comment###','','');
/*!40000 ALTER TABLE `rex_xform_email_template` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `rex_xform_field`;
CREATE TABLE `rex_xform_field` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table_name` varchar(100) NOT NULL,
  `prio` int(11) NOT NULL,
  `type_id` varchar(100) NOT NULL,
  `type_name` varchar(100) NOT NULL,
  `list_hidden` tinyint(1) NOT NULL,
  `search` tinyint(1) NOT NULL,
  `name` text NOT NULL,
  `label` text NOT NULL,
  `options` text NOT NULL,
  `multiple` text NOT NULL,
  `default` text NOT NULL,
  `size` text NOT NULL,
  `only_empty` text NOT NULL,
  `message` text NOT NULL,
  `table` text NOT NULL,
  `hashname` text NOT NULL,
  `field` text NOT NULL,
  `type` text NOT NULL,
  `empty_option` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

LOCK TABLES `rex_xform_field` WRITE;
/*!40000 ALTER TABLE `rex_xform_field` DISABLE KEYS */;
INSERT INTO `rex_xform_field` VALUES 
  (1,'rex_com_user',100,'value','text',0,1,'login','translate:login','','','','','','','','','','',''),
  (2,'rex_com_user',200,'value','text',1,0,'password','translate:password','','','','','','','','','','',''),
  (3,'rex_com_user',300,'value','text',0,1,'email','translate:email','','','','','','','','','','',''),
  (4,'rex_com_user',400,'value','select',0,1,'status','translate:status','translate:com_account_requested=0,translate:com_account_active=1,translate:com_account_inactive=-1','0','-1','1','','','','','','',''),
  (5,'rex_com_user',500,'value','text',0,1,'firstname','translate:firstname','','','','','','','','','','',''),
  (6,'rex_com_user',600,'value','text',0,1,'name','translate:name','','','','','','','','','','',''),
  (7,'rex_com_user',700,'value','text',1,1,'activation_key','translate:activation_key','','','','','','','','','','',''),
  (8,'rex_com_user',800,'value','text',1,1,'session_key','translate:session_key','','','','','','','','','','',''),
  (9,'rex_com_user',900,'value','datestamp',1,1,'last_action_time','U','','','','','0','','','','','',''),
  (10,'rex_com_user',110,'validate','empty',1,0,'login','','','','','','','translate:com_please_enter_login','','','','',''),
  (11,'rex_com_user',120,'validate','unique',1,0,'login','','','','','','','translate:com_this_login_exists_already','rex_com_user','','','',''),
  (12,'rex_com_user',210,'validate','empty',1,0,'password','','','','','','','translate:com_please_enter_password','','','','',''),
  (13,'rex_com_user',310,'validate','empty',1,0,'email','','','','','','','translate:com_please_enter_email','','','','',''),
  (14,'rex_com_user',320,'validate','email',1,0,'email','','','','','','','translate:com_please_enter_email','','','','',''),
  (15,'rex_com_user',330,'validate','unique',1,0,'email','','','','','','','translate:com_this_email_exists_already','rex_com_user','','','',''),
  (16,'rex_com_user',250,'value','com_auth_password_hash',0,1,'password_hash','','','','','','','','','password','','',''),
  (17,'rex_com_group',100,'value','text',0,0,'name','translate:name','','','','','','','','','','',''),
  (18,'rex_com_group',110,'validate','empty',1,0,'name','','','','','','','translate:com_group_xform_enter_name','','','','',''),
  (19,'rex_com_user',50,'value','be_manager_relation',1,0,'rex_com_group','translate:rex_com_group','','','','5','','','rex_com_group','','name','1','1'),
  (20,'rex_com_user',1500,'value','text',1,1,'newsletter_last_id','translate:newsletter_last_id','','','','','','','','','','',''),
  (21,'rex_com_user',1510,'value','checkbox',1,1,'newsletter','translate:newsletter','','','0','','','','','','','','');
/*!40000 ALTER TABLE `rex_xform_field` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `rex_xform_relation`;
CREATE TABLE `rex_xform_relation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `source_table` varchar(100) NOT NULL,
  `source_name` varchar(100) NOT NULL,
  `source_id` int(11) NOT NULL,
  `target_table` varchar(100) NOT NULL,
  `target_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `rex_xform_table`;
CREATE TABLE `rex_xform_table` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` tinyint(1) NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `list_amount` tinyint(3) unsigned NOT NULL DEFAULT '50',
  `prio` int(11) NOT NULL,
  `search` tinyint(1) NOT NULL,
  `hidden` tinyint(1) NOT NULL,
  `export` tinyint(1) NOT NULL,
  `import` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `table_name` (`table_name`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

LOCK TABLES `rex_xform_table` WRITE;
/*!40000 ALTER TABLE `rex_xform_table` DISABLE KEYS */;
INSERT INTO `rex_xform_table` VALUES 
  (1,1,'rex_com_user','translate:com_user','',100,0,0,0,1,1),
  (2,1,'rex_com_group','translate:com_group_name','',50,0,0,0,1,1);
/*!40000 ALTER TABLE `rex_xform_table` ENABLE KEYS */;
UNLOCK TABLES;

