-- phpMyAdmin SQL Dump
-- version 2.11.1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Час створення: Жов 10 2009 р., 16:31
-- Версія сервера: 5.0.45
-- Версія PHP: 5.2.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- БД: `carumba`
--

-- --------------------------------------------------------

--
-- Структура таблиці `pm_as_autocreators`
--

CREATE TABLE IF NOT EXISTS `pm_as_autocreators` (
  `plantID` int(11) NOT NULL auto_increment,
  `plantName` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`plantID`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=5 ;

--
-- Дамп даних таблиці `pm_as_autocreators`
--

INSERT INTO `pm_as_autocreators` (`plantID`, `plantName`) VALUES
(1, 'неизвестный'),
(2, 'ВАЗ'),
(3, 'ГАЗ'),
(4, '');
