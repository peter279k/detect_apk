-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- 主機: 127.0.0.1
-- 產生時間： 2016 �?08 ??29 ??12:40
-- 伺服器版本: 5.6.17
-- PHP 版本： 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 資料庫： `apks`
--

-- --------------------------------------------------------

--
-- 資料表結構 `apk_info`
--

CREATE TABLE IF NOT EXISTS `apk_info` (
  `apk_id` varchar(80) COLLATE utf8_unicode_ci NOT NULL COMMENT 'com.example.peter',
  `version` float NOT NULL COMMENT 'apk 版本',
  `downloads` int(11) NOT NULL COMMENT 'apk 下載次數',
  `rate` float NOT NULL COMMENT 'apk 評分',
  `rate_people` int(11) NOT NULL COMMENT '評分人數',
  `category` varchar(10) COLLATE utf8_unicode_ci NOT NULL COMMENT 'apk 類型',
  `apk_source` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT 'apk 來源 (從哪裡下載的)',
  `meta_source` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'google play' COMMENT 'google play',
  `develop_team` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT '開發者團隊名稱',
  `size` int(11) NOT NULL COMMENT 'apk 檔案大小',
  `hash` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT 'apk 檔的內容 hash 過後的值',
  PRIMARY KEY (`apk_id`,`hash`,`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='apk 資訊資料表';

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
