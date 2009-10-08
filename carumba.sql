-- phpMyAdmin SQL Dump
-- version 2.11.1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Час створення: Жов 08 2009 р., 22:36
-- Версія сервера: 5.0.45
-- Версія PHP: 5.2.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- БД: `carumba`
--

-- --------------------------------------------------------

--
-- Структура таблиці `adm_users_ip`
--

CREATE TABLE IF NOT EXISTS `adm_users_ip` (
  `Login` varchar(50) NOT NULL,
  `ip` varchar(30) NOT NULL,
  KEY `Login` (`Login`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблиці `cns_adminsessions`
--

CREATE TABLE IF NOT EXISTS `cns_adminsessions` (
  `hash` varchar(32) NOT NULL default '',
  `last` datetime NOT NULL default '0000-00-00 00:00:00',
  `ip` varchar(64) NOT NULL default '',
  `c` int(11) NOT NULL default '0',
  PRIMARY KEY  (`hash`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблиці `cns_config`
--

CREATE TABLE IF NOT EXISTS `cns_config` (
  `language` text,
  `mail_day` tinyint(4) default '1',
  `mail_email` varchar(32) default NULL,
  `mail_subject` varchar(64) default '[%d.%m.%Y] CNStats report',
  `mail_content` tinyint(4) default '1',
  `version` int(11) NOT NULL default '20',
  `hints` int(11) NOT NULL default '1',
  `gauge` int(11) NOT NULL default '1',
  `percents` int(11) NOT NULL default '0',
  `diagram` tinyint(4) NOT NULL default '1',
  `antialias` tinyint(4) NOT NULL default '1',
  `date_format` varchar(32) NOT NULL default '',
  `shortdate_format` varchar(32) NOT NULL default '',
  `datetime_format` varchar(32) NOT NULL default '',
  `datetimes_format` varchar(32) NOT NULL default '',
  `shortdm_format` varchar(32) NOT NULL default '',
  `show_hits` tinyint(4) NOT NULL default '1',
  `show_hosts` tinyint(4) NOT NULL default '1',
  `show_users` tinyint(4) NOT NULL default '1'
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблиці `cns_counter`
--

CREATE TABLE IF NOT EXISTS `cns_counter` (
  `hits` bigint(20) default NULL,
  `hosts` bigint(20) default NULL,
  `t_hits` bigint(20) default NULL,
  `t_hosts` bigint(20) default NULL,
  `last` tinyint(4) default '0',
  `visible` int(11) default '1',
  `t_users` int(11) default NULL,
  `users` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблиці `cns_counter_total`
--

CREATE TABLE IF NOT EXISTS `cns_counter_total` (
  `hits` int(11) default NULL,
  `hosts` int(11) default NULL,
  `date` datetime default NULL,
  `users` int(11) default NULL,
  KEY `idx` (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблиці `cns_data`
--

CREATE TABLE IF NOT EXISTS `cns_data` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `type` tinyint(4) NOT NULL default '0',
  `d1` varchar(255) NOT NULL default '',
  `d2` varchar(255) NOT NULL default '',
  `d3` varchar(255) NOT NULL default '',
  `d4` varchar(255) NOT NULL default '',
  `d5` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `type` (`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=285 ;

-- --------------------------------------------------------

--
-- Структура таблиці `cns_filters`
--

CREATE TABLE IF NOT EXISTS `cns_filters` (
  `id` int(11) NOT NULL auto_increment,
  `txt` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Структура таблиці `cns_goodies`
--

CREATE TABLE IF NOT EXISTS `cns_goodies` (
  `url` text,
  `name` text
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблиці `cns_languages`
--

CREATE TABLE IF NOT EXISTS `cns_languages` (
  `code` char(2) NOT NULL default '',
  `eng` text,
  PRIMARY KEY  (`code`),
  KEY `code_idx` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблиці `cns_log`
--

CREATE TABLE IF NOT EXISTS `cns_log` (
  `id` int(11) NOT NULL auto_increment,
  `date` datetime NOT NULL default '2001-01-01 00:00:00',
  `ip` int(11) default NULL,
  `type` smallint(6) NOT NULL default '0',
  `page` text NOT NULL,
  `proxy` int(11) default NULL,
  `agent` text NOT NULL,
  `referer` text NOT NULL,
  `uid` int(11) NOT NULL default '0',
  `type1` smallint(6) NOT NULL default '0',
  `res` varchar(10) NOT NULL default '',
  `depth` smallint(6) NOT NULL default '0',
  `cookie` smallint(6) NOT NULL default '0',
  `language` varchar(32) NOT NULL default '',
  `country` smallint(5) unsigned NOT NULL default '0',
  `city` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `idx3` (`uid`),
  KEY `idx1` (`date`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=7208403 ;

-- --------------------------------------------------------

--
-- Структура таблиці `cns_size`
--

CREATE TABLE IF NOT EXISTS `cns_size` (
  `date` date NOT NULL default '0000-00-00',
  `size` int(11) NOT NULL default '0',
  PRIMARY KEY  (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблиці `cns_subnets`
--

CREATE TABLE IF NOT EXISTS `cns_subnets` (
  `ip1` int(11) NOT NULL default '0',
  `ip2` int(11) NOT NULL default '0',
  `title` text NOT NULL,
  `id` int(11) NOT NULL default '0',
  `uniqueid` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`uniqueid`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблиці `cns_today`
--

CREATE TABLE IF NOT EXISTS `cns_today` (
  `id` int(11) NOT NULL auto_increment,
  `ip` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1036839 ;

-- --------------------------------------------------------

--
-- Структура таблиці `cns_today_proxy`
--

CREATE TABLE IF NOT EXISTS `cns_today_proxy` (
  `id` int(11) NOT NULL auto_increment,
  `ip` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=95569 ;

-- --------------------------------------------------------

--
-- Структура таблиці `cns_who_cache`
--

CREATE TABLE IF NOT EXISTS `cns_who_cache` (
  `date` datetime default NULL,
  `title` varchar(64) default NULL,
  `crc` int(11) NOT NULL default '0',
  `count` int(11) default NULL,
  KEY `crc` (`crc`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблиці `online`
--

CREATE TABLE IF NOT EXISTS `online` (
  `id` bigint(20) NOT NULL auto_increment,
  `quest` text NOT NULL,
  `answ` text NOT NULL,
  `user` varchar(100) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `phone` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Структура таблиці `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `idpage` bigint(20) NOT NULL auto_increment,
  `idparent` bigint(20) NOT NULL default '0',
  `name` varchar(240) NOT NULL default '',
  `descr` text NOT NULL,
  `url_name` varchar(80) NOT NULL default '',
  `script_name` varchar(80) NOT NULL default '',
  `view` tinyint(4) NOT NULL default '0',
  `psort` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idpage`),
  KEY `idparent` (`idparent`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=34 ;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_as_acc_to_cars`
--

CREATE TABLE IF NOT EXISTS `pm_as_acc_to_cars` (
  `accID` int(11) NOT NULL default '0',
  `carID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`accID`,`carID`),
  KEY `pm_as_acc_to_cars_FK` (`accID`),
  KEY `pm_as_acc_to_cars2_FK` (`carID`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_as_autocreators`
--

CREATE TABLE IF NOT EXISTS `pm_as_autocreators` (
  `plantID` int(11) NOT NULL auto_increment,
  `plantName` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`plantID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_as_cars`
--

CREATE TABLE IF NOT EXISTS `pm_as_cars` (
  `carID` int(11) NOT NULL auto_increment,
  `plantID` int(11) default NULL,
  `carModel` varchar(32) NOT NULL default '',
  `carName` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`carID`),
  KEY `brand_marks_FK` (`plantID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=49 ;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_as_cart`
--

CREATE TABLE IF NOT EXISTS `pm_as_cart` (
  `cartID` int(11) NOT NULL auto_increment,
  `sessionID` varchar(32) NOT NULL default '',
  `accID` int(11) NOT NULL default '0',
  `accCount` int(11) NOT NULL default '0',
  `addDate` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`cartID`),
  KEY `sessionID` (`sessionID`,`accID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=52388 ;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_as_categories`
--

CREATE TABLE IF NOT EXISTS `pm_as_categories` (
  `accCatID` int(11) NOT NULL auto_increment,
  `sID` int(11) NOT NULL default '0',
  `sortType` varchar(64) NOT NULL default '',
  `MustUseCompatibility` tinyint(4) NOT NULL default '0',
  `PicturePath` varchar(255) NOT NULL default '',
  `DescriptionTemplate` varchar(255) NOT NULL default '',
  `FilterTemplate` varchar(255) NOT NULL default 'std_select.html',
  PRIMARY KEY  (`accCatID`),
  KEY `pm_structure_FK` (`sID`),
  KEY `DescriptionTemplate` (`DescriptionTemplate`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=196 ;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_as_parts`
--

CREATE TABLE IF NOT EXISTS `pm_as_parts` (
  `accID` int(11) NOT NULL auto_increment,
  `accCatID` int(11) default NULL,
  `sID` int(11) default NULL,
  `accPlantID` int(11) default NULL,
  `deliveryID` int(11) NOT NULL default '0',
  `deliveryCode` varchar(16) NOT NULL default '',
  `measure` varchar(32) NOT NULL default '',
  `basePrice` float NOT NULL default '0',
  `salePrice` float NOT NULL default '0',
  `smallPicture` varchar(255) default NULL,
  `stdPicture` varchar(255) default NULL,
  `bigPicture` varchar(255) default NULL,
  `tplID` int(11) default '2',
  `ptID` int(11) NOT NULL default '1',
  `notAvailable` tinyint(4) NOT NULL default '0',
  `new` tinyint(4) NOT NULL,
  `xit` tinyint(4) NOT NULL,
  `main` tinyint(4) NOT NULL,
  PRIMARY KEY  (`accID`),
  KEY `producer_accs_FK` (`accPlantID`),
  KEY `cat_entry_FK` (`accCatID`),
  KEY `sID_FK` (`sID`),
  KEY `ptID` (`ptID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 PACK_KEYS=0 AUTO_INCREMENT=7629 ;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_as_parts_properties`
--

CREATE TABLE IF NOT EXISTS `pm_as_parts_properties` (
  `propID` int(11) NOT NULL auto_increment,
  `accID` int(11) default NULL,
  `propListID` int(11) default NULL,
  `propValue` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`propID`),
  KEY `properties_FK` (`accID`),
  KEY `listProperties_FK` (`propListID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=50078 ;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_as_pricetypes`
--

CREATE TABLE IF NOT EXISTS `pm_as_pricetypes` (
  `ptID` int(11) NOT NULL auto_increment,
  `ptName` varchar(32) NOT NULL default '',
  `ptPercent` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ptID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_as_producer`
--

CREATE TABLE IF NOT EXISTS `pm_as_producer` (
  `accPlantID` int(11) NOT NULL auto_increment,
  `accPlantName` varchar(64) NOT NULL default '',
  `logotype` varchar(255) default NULL,
  `logotypeb` varchar(255) NOT NULL,
  `sID` int(11) NOT NULL,
  PRIMARY KEY  (`accPlantID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 PACK_KEYS=0 AUTO_INCREMENT=377 ;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_as_prop_list`
--

CREATE TABLE IF NOT EXISTS `pm_as_prop_list` (
  `propListID` int(11) NOT NULL auto_increment,
  `accCatID` int(11) default NULL,
  `propName` varchar(64) NOT NULL default '',
  `accMeasure` varchar(32) NOT NULL default '',
  `isHidden` tinyint(4) NOT NULL default '0',
  `OrderNumber` int(11) NOT NULL default '1',
  PRIMARY KEY  (`propListID`),
  KEY `props_for_cat_FK` (`accCatID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=103 ;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_banners`
--

CREATE TABLE IF NOT EXISTS `pm_banners` (
  `banID` int(11) NOT NULL auto_increment,
  `sID` int(11) NOT NULL default '0',
  `url` varchar(255) NOT NULL default '',
  `type` varchar(10) NOT NULL default '',
  `click` int(11) NOT NULL default '0',
  `show` int(11) NOT NULL default '0',
  `link` varchar(255) NOT NULL default '',
  `text` text NOT NULL,
  `param` varchar(255) NOT NULL default '',
  `isactive` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`banID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=35 ;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_cards`
--

CREATE TABLE IF NOT EXISTS `pm_cards` (
  `cardID` int(9) NOT NULL auto_increment,
  `userID` int(11) NOT NULL default '0',
  `createDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `registerDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `inUse` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`cardID`),
  KEY `userID` (`userID`,`inUse`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=2400 ;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_comments`
--

CREATE TABLE IF NOT EXISTS `pm_comments` (
  `cID` int(11) NOT NULL auto_increment,
  `sID` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(50) NOT NULL,
  `comment` text NOT NULL,
  `public` int(1) NOT NULL default '0',
  PRIMARY KEY  (`cID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=555 ;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_comments_codes`
--

CREATE TABLE IF NOT EXISTS `pm_comments_codes` (
  `id` int(11) NOT NULL auto_increment,
  `date` int(11) NOT NULL,
  `code` varchar(4) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=4804850 ;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_config`
--

CREATE TABLE IF NOT EXISTS `pm_config` (
  `var` varchar(255) NOT NULL default '',
  `val` text NOT NULL,
  PRIMARY KEY  (`var`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_data_helpers`
--

CREATE TABLE IF NOT EXISTS `pm_data_helpers` (
  `dhID` int(11) NOT NULL auto_increment,
  `sID` int(11) NOT NULL default '0',
  `dhModule` varchar(32) NOT NULL default '',
  `dhFunc` varchar(64) NOT NULL default '',
  `dhOrderNumber` int(11) NOT NULL default '0',
  PRIMARY KEY  (`dhID`),
  KEY `Relationship_15_FK` (`sID`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_group_entries`
--

CREATE TABLE IF NOT EXISTS `pm_group_entries` (
  `geID` int(11) NOT NULL auto_increment,
  `userID` int(11) default NULL,
  `gID` int(11) default NULL,
  PRIMARY KEY  (`geID`),
  KEY `Relationship_19_FK` (`userID`),
  KEY `Relationship_20_FK` (`gID`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_links`
--

CREATE TABLE IF NOT EXISTS `pm_links` (
  `id` int(4) NOT NULL auto_increment,
  `cid` int(4) NOT NULL,
  `date` int(11) NOT NULL,
  `url` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `text` text NOT NULL,
  `referer` varchar(100) NOT NULL,
  `email` varchar(50) NOT NULL,
  `public` int(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=540 ;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_order`
--

CREATE TABLE IF NOT EXISTS `pm_order` (
  `orderID` int(11) NOT NULL auto_increment,
  `userID` int(11) NOT NULL default '0',
  `stDate` datetime default '0000-00-00 00:00:00',
  `deliveryDate` date NOT NULL default '0000-00-00',
  `prePayment` varchar(100) NOT NULL default '',
  `comment` text NOT NULL,
  `cardStID` int(11) NOT NULL default '0',
  `stID` int(11) NOT NULL default '0',
  `pda` varchar(10) NOT NULL,
  PRIMARY KEY  (`orderID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=4579 ;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_order_card_st`
--

CREATE TABLE IF NOT EXISTS `pm_order_card_st` (
  `cardStID` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`cardStID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_order_message`
--

CREATE TABLE IF NOT EXISTS `pm_order_message` (
  `orderID` int(11) default NULL,
  `message` text,
  `dateOfMes` datetime default NULL
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_order_parts`
--

CREATE TABLE IF NOT EXISTS `pm_order_parts` (
  `orderID` int(11) default NULL,
  `accID` int(11) default NULL,
  `accCount` int(11) NOT NULL default '0',
  `price` varchar(100) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_order_quick`
--

CREATE TABLE IF NOT EXISTS `pm_order_quick` (
  `orderID` int(11) default NULL,
  `userName` varchar(255) default NULL,
  `userPhone` varchar(255) default NULL,
  `userEmail` varchar(255) default NULL,
  `userAdress` text
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_order_status`
--

CREATE TABLE IF NOT EXISTS `pm_order_status` (
  `stID` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`stID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_order_status_date`
--

CREATE TABLE IF NOT EXISTS `pm_order_status_date` (
  `orderID` int(11) NOT NULL default '0',
  `stID` int(11) NOT NULL default '0',
  `stDate` datetime NOT NULL default '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_rating`
--

CREATE TABLE IF NOT EXISTS `pm_rating` (
  `rID` int(11) NOT NULL auto_increment,
  `sID` int(11) NOT NULL,
  `type` int(1) NOT NULL default '1',
  `grade` int(11) NOT NULL,
  `count` int(11) NOT NULL,
  PRIMARY KEY  (`rID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=2572 ;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_search`
--

CREATE TABLE IF NOT EXISTS `pm_search` (
  `url` varchar(255) NOT NULL default '',
  `referrer` text NOT NULL,
  `title` text NOT NULL,
  `text` mediumtext NOT NULL,
  `md5` varchar(33) NOT NULL default '',
  `lastupdate` int(10) unsigned NOT NULL default '0',
  UNIQUE KEY `url` (`url`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_sessions`
--

CREATE TABLE IF NOT EXISTS `pm_sessions` (
  `sessionID` varchar(32) NOT NULL default '',
  `userID` int(11) NOT NULL default '0',
  `CreateTime` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`sessionID`),
  KEY `userID` (`userID`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_structure`
--

CREATE TABLE IF NOT EXISTS `pm_structure` (
  `sID` int(11) NOT NULL auto_increment,
  `userID` int(11) NOT NULL default '0',
  `pms_sID` int(11) default NULL,
  `tplID` int(11) NOT NULL default '0',
  `CreateDate` date NOT NULL default '0000-00-00',
  `URLName` varchar(255) NOT NULL default '',
  `Title` text NOT NULL,
  `ShortTitle` varchar(255) NOT NULL default '',
  `MetaDesc` text NOT NULL,
  `MetaKeywords` text NOT NULL,
  `Content` mediumtext NOT NULL,
  `ModuleName` varchar(32) NOT NULL default 'Articles',
  `DataType` varchar(64) NOT NULL default '',
  `OrderNumber` int(11) NOT NULL default '0',
  `OrderField` varchar(64) NOT NULL default 'OrderNumber',
  `SortType` int(11) NOT NULL default '0',
  `LinkCSSClass` varchar(32) NOT NULL default '1',
  `CacheLifetime` time NOT NULL default '00:00:00',
  `ReviseType` smallint(6) NOT NULL default '0',
  `CanBeProcessed` tinyint(1) NOT NULL default '0',
  `CanBeHelpered` tinyint(1) NOT NULL default '0',
  `IsVersionOfParent` tinyint(1) NOT NULL default '0',
  `isDeleted` tinyint(1) NOT NULL default '0',
  `isHidden` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`sID`),
  KEY `ParentSection_FK` (`pms_sID`),
  KEY `ModuleName` (`ModuleName`),
  KEY `DataType` (`DataType`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 PACK_KEYS=0 AUTO_INCREMENT=8178 ;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_templates`
--

CREATE TABLE IF NOT EXISTS `pm_templates` (
  `tplID` int(11) NOT NULL auto_increment,
  `tplName` varchar(255) NOT NULL default '',
  `tplFilename` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`tplID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_template_containers`
--

CREATE TABLE IF NOT EXISTS `pm_template_containers` (
  `tcID` int(11) NOT NULL auto_increment,
  `tplID` int(11) NOT NULL default '0',
  `tcTplNamedID` varchar(64) NOT NULL default '',
  `tcModule` varchar(32) NOT NULL default '',
  `tcFunc` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`tcID`),
  KEY `Relationship_27_FK` (`tplID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=25 ;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_users`
--

CREATE TABLE IF NOT EXISTS `pm_users` (
  `userID` int(11) NOT NULL auto_increment,
  `groupID` int(11) default '6',
  `isUserGroup` tinyint(4) NOT NULL default '0',
  `Login` varchar(32) NOT NULL default '',
  `Password` varchar(32) NOT NULL default '',
  `FirstName` varchar(64) NOT NULL default '',
  `LastName` varchar(64) NOT NULL default '',
  `SurName` varchar(64) NOT NULL default '',
  `Email` varchar(255) NOT NULL default '',
  `cardID` int(9) NOT NULL default '0',
  `BirthDate` date NOT NULL default '0000-00-00',
  `LockDate` date NOT NULL default '0000-00-00',
  `SessionTimeout` time NOT NULL default '00:00:00',
  `MustChangePsw` tinyint(1) NOT NULL default '0',
  `NextPswChangeDate` date NOT NULL default '0000-00-00',
  `DiskQuota` int(11) NOT NULL default '0',
  `uDeleted` tinyint(1) NOT NULL default '0',
  `LoginDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `sex` char(1) NOT NULL default 'm',
  `phone` varchar(32) NOT NULL default '',
  `region` varchar(64) NOT NULL default '',
  `city` varchar(64) NOT NULL default '',
  `address` varchar(255) NOT NULL default '',
  `carID` int(11) NOT NULL default '0',
  `carType` varchar(32) NOT NULL default '',
  `subscribe` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`userID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=4653 ;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_users_cards`
--

CREATE TABLE IF NOT EXISTS `pm_users_cards` (
  `cardID` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_vinorder`
--

CREATE TABLE IF NOT EXISTS `pm_vinorder` (
  `orderID` int(11) NOT NULL auto_increment,
  `userID` int(11) NOT NULL default '0',
  `stDate` datetime default '0000-00-00 00:00:00',
  `deliveryDate` date NOT NULL default '0000-00-00',
  `prePayment` varchar(100) NOT NULL default '',
  `comment` text NOT NULL,
  `cardStID` int(11) NOT NULL default '0',
  `stID` int(11) NOT NULL default '0',
  `orderType` int(11) NOT NULL default '0',
  PRIMARY KEY  (`orderID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=428 ;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_vinorder_card_st`
--

CREATE TABLE IF NOT EXISTS `pm_vinorder_card_st` (
  `cardStID` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`cardStID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_vinorder_message`
--

CREATE TABLE IF NOT EXISTS `pm_vinorder_message` (
  `orderID` int(11) default NULL,
  `message` text,
  `dateOfMes` datetime default NULL
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_vinorder_parts`
--

CREATE TABLE IF NOT EXISTS `pm_vinorder_parts` (
  `orderID` int(11) default NULL,
  `carName` varchar(255) default NULL,
  `carModel` varchar(255) default NULL,
  `rul` varchar(255) default NULL,
  `year` varchar(255) default NULL,
  `vin` varchar(255) default NULL,
  `vengine` varchar(255) default NULL,
  `cuzov` varchar(255) default NULL,
  `kpp` varchar(255) default NULL,
  `privod` varchar(255) default NULL,
  `abs` tinyint(1) default '0',
  `condition` tinyint(1) default '0',
  `original` tinyint(1) default '0',
  `other` tinyint(1) default '0',
  `wanted` text
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_vinorder_parts_details`
--

CREATE TABLE IF NOT EXISTS `pm_vinorder_parts_details` (
  `orderID` int(11) default NULL,
  `accID` int(11) default NULL,
  `accName` varchar(50) default NULL,
  `accBuyPrice` varchar(50) default NULL,
  `accSalePrice` varchar(50) default NULL,
  `date` date default '0000-00-00'
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_vinorder_quick`
--

CREATE TABLE IF NOT EXISTS `pm_vinorder_quick` (
  `orderID` int(11) default NULL,
  `userName` varchar(255) default NULL,
  `userPhone` varchar(255) default NULL,
  `userEmail` varchar(255) default NULL,
  `userAdress` text
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_vinorder_status`
--

CREATE TABLE IF NOT EXISTS `pm_vinorder_status` (
  `stID` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`stID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_vinorder_status_date`
--

CREATE TABLE IF NOT EXISTS `pm_vinorder_status_date` (
  `orderID` int(11) NOT NULL default '0',
  `stID` int(11) NOT NULL default '0',
  `stDate` datetime NOT NULL default '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_vote`
--

CREATE TABLE IF NOT EXISTS `pm_vote` (
  `qID` int(11) NOT NULL auto_increment,
  `Question` varchar(255) default NULL,
  `Ans1` varchar(255) default NULL,
  `Ans2` varchar(255) default NULL,
  `Ans3` varchar(255) default NULL,
  `Ans4` varchar(255) default NULL,
  `Ans5` varchar(255) default NULL,
  `Ans6` varchar(255) default NULL,
  `Ans7` varchar(255) default NULL,
  `Ans8` varchar(255) default NULL,
  `Ans9` varchar(255) default NULL,
  `Ans10` varchar(255) default NULL,
  `isactive` tinyint(1) default NULL,
  `sID` int(11) NOT NULL default '2778',
  `isdefault` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`qID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

--
-- Структура таблиці `pm_vote_results`
--

CREATE TABLE IF NOT EXISTS `pm_vote_results` (
  `resId` int(11) NOT NULL auto_increment,
  `qID` int(11) default NULL,
  `aID` int(11) default NULL,
  `ip` char(20) default NULL,
  `date` datetime default NULL,
  PRIMARY KEY  (`resId`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=6049 ;

-- --------------------------------------------------------

--
-- Структура таблиці `task`
--

CREATE TABLE IF NOT EXISTS `task` (
  `id` int(4) NOT NULL auto_increment,
  `sid` int(4) default NULL,
  `date` int(11) default NULL,
  `title` varchar(100) default NULL,
  `message` text,
  `important` int(1) default NULL,
  `userID` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=160 ;
