-- phpMyAdmin SQL Dump
-- version 3.4.10.1
-- http://www.phpmyadmin.net
--
-- 主機: localhost
-- 產生日期: 2016 年 02 月 02 日 17:01
-- 伺服器版本: 5.0.22
-- PHP 版本: 5.2.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 資料庫: `chuanglifa`
--

-- --------------------------------------------------------

--
-- 表的結構 `ogs_ad`
--

CREATE TABLE IF NOT EXISTS `ogs_ad` (
  `id` int(11) NOT NULL auto_increment,
  `lang_id` int(11) NOT NULL COMMENT '語系 id',
  `parent` int(11) NOT NULL COMMENT '分類 id',
  `subject` varchar(255) NOT NULL COMMENT '標題',
  `langtag` char(3) NOT NULL COMMENT '語系標籤; eng,cht,chs...etc',
  `sort` int(11) NOT NULL COMMENT '排序',
  `status` tinyint(1) NOT NULL COMMENT '狀態; 0 => 關閉 , 1 => 開啟 , 2 => 依照時間',
  `content` text NOT NULL COMMENT '內容',
  `link` text NOT NULL COMMENT '廣告連結',
  `startdate` date default NULL COMMENT '起始時間',
  `limitdate` date default NULL COMMENT '到期時間',
  PRIMARY KEY  (`id`),
  KEY `lang_id` (`lang_id`),
  KEY `parent` (`parent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='廣告' AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_ad_cate`
--

CREATE TABLE IF NOT EXISTS `ogs_ad_cate` (
  `id` int(11) NOT NULL auto_increment,
  `lang_id` int(11) NOT NULL COMMENT '語系 id',
  `subject` varchar(255) NOT NULL COMMENT '標題',
  `langtag` char(3) NOT NULL COMMENT '語系標籤',
  `sort` int(11) NOT NULL COMMENT '排序',
  `status` tinyint(1) NOT NULL default '0' COMMENT '狀態',
  PRIMARY KEY  (`id`),
  KEY `lang_id` (`lang_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='廣告分類' AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_ban`
--

CREATE TABLE IF NOT EXISTS `ogs_ban` (
  `id` int(11) NOT NULL auto_increment,
  `ip` varchar(39) default NULL,
  `time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT '禁止開始時間',
  PRIMARY KEY  (`id`),
  KEY `ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='禁止登入後台 IP' AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_contact`
--

CREATE TABLE IF NOT EXISTS `ogs_contact` (
  `id` int(11) NOT NULL auto_increment,
  `m_id` int(11) default NULL COMMENT '會員 id',
  `name` varchar(255) NOT NULL COMMENT '姓名',
  `gender` tinyint(1) default NULL COMMENT '性別',
  `tel` varchar(255) NOT NULL COMMENT '電話',
  `fax` varchar(255) NOT NULL COMMENT '傳真',
  `cell` varchar(255) NOT NULL COMMENT '手機',
  `address` text NOT NULL COMMENT '地址',
  `email` text NOT NULL COMMENT '信箱',
  `content` text NOT NULL COMMENT '聯絡內容',
  `reply` text NOT NULL COMMENT '回覆內容',
  `createdate` timestamp NOT NULL default CURRENT_TIMESTAMP COMMENT '聯絡時間',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='聯絡我們' AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_faq`
--

CREATE TABLE IF NOT EXISTS `ogs_faq` (
  `id` int(11) NOT NULL auto_increment,
  `lang_id` int(11) NOT NULL,
  `parent` int(11) NOT NULL COMMENT '分類 id',
  `subject` varchar(255) NOT NULL COMMENT '標題',
  `langtag` char(3) NOT NULL COMMENT '語系標籤; eng,cht,chs...etc',
  `sort` int(11) NOT NULL COMMENT '排序',
  `status` tinyint(1) NOT NULL COMMENT '狀態; 0 => 關閉 , 1 => 開啟',
  `content` text NOT NULL COMMENT '內容',
  PRIMARY KEY  (`id`),
  KEY `parent` (`parent`),
  KEY `lang_id` (`lang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='問與答' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_faq_cate`
--

CREATE TABLE IF NOT EXISTS `ogs_faq_cate` (
  `id` int(11) NOT NULL auto_increment,
  `lang_id` int(11) NOT NULL COMMENT '語系 id',
  `seo_id` int(11) NOT NULL COMMENT '行銷 id',
  `subject` varchar(255) NOT NULL COMMENT '標題',
  `langtag` char(3) NOT NULL COMMENT '語系標籤; eng,cht,chs...etc',
  `sort` int(11) NOT NULL COMMENT '排序',
  `status` tinyint(1) NOT NULL COMMENT '狀態; 0 => 關閉 , 1 => 開啟',
  PRIMARY KEY  (`id`),
  KEY `lang_id` (`lang_id`),
  KEY `seo_id` (`seo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='問與答分類' AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_grant`
--

CREATE TABLE IF NOT EXISTS `ogs_grant` (
  `id` int(11) NOT NULL auto_increment,
  `m_id` int(11) NOT NULL COMMENT '會員 id',
  `granted` int(11) NOT NULL COMMENT '已發送獎金',
  `date` datetime NOT NULL COMMENT '發送時間',
  PRIMARY KEY  (`id`),
  KEY `m_id` (`m_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_images`
--

CREATE TABLE IF NOT EXISTS `ogs_images` (
  `id` int(11) NOT NULL auto_increment,
  `path` text NOT NULL COMMENT '圖片路徑',
  `alt` text NOT NULL COMMENT '圖片描述',
  `title` text NOT NULL COMMENT '圖片抬頭',
  `sheet` varchar(255) NOT NULL COMMENT '對應資料表',
  `related` int(11) NOT NULL COMMENT '關聯資料 id',
  PRIMARY KEY  (`id`),
  KEY `related` (`related`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='圖片資料表' AUTO_INCREMENT=23 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_intro`
--

CREATE TABLE IF NOT EXISTS `ogs_intro` (
  `id` int(11) NOT NULL auto_increment,
  `lang_id` int(11) NOT NULL COMMENT '語系 id',
  `seo_id` int(11) NOT NULL COMMENT '行銷 id',
  `subject` varchar(255) NOT NULL COMMENT '標題',
  `langtag` char(3) NOT NULL COMMENT '語系標籤; eng,cht,chs...etc',
  `sort` int(11) NOT NULL COMMENT '排序',
  `status` tinyint(1) NOT NULL COMMENT '狀態; 0 => 關閉 , 1 => 開啟',
  `content` text NOT NULL COMMENT '內容',
  PRIMARY KEY  (`id`),
  KEY `lang_id` (`lang_id`),
  KEY `seo_id` (`seo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='介紹頁' AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_lang`
--

CREATE TABLE IF NOT EXISTS `ogs_lang` (
  `id` int(11) NOT NULL auto_increment,
  `sheet` varchar(255) NOT NULL COMMENT '對應資料表',
  `related` text NOT NULL COMMENT '關聯資料 id (json); ex: cht => 1, chs => 2, eng => 3',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='lang id 紀錄表' AUTO_INCREMENT=42 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_level`
--

CREATE TABLE IF NOT EXISTS `ogs_level` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL COMMENT '權限名稱',
  `status` tinyint(1) NOT NULL default '0' COMMENT '權限開關; 0 => 關閉, 1 => 開啟',
  `class` text COMMENT '授權參數 (json)',
  PRIMARY KEY  (`id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理員權限層級' AUTO_INCREMENT=2 ;

--
-- 轉存資料表中的資料 `ogs_level`
--

INSERT INTO `ogs_level` (`id`, `name`, `status`, `class`) VALUES
(1, '總管理員', 1, '{"system":"1","manager":"1","ad":"1","intro":"1","faq":"1","news":"1","products":"1","order":"1","member":"1","contact":"1"}');

-- --------------------------------------------------------

--
-- 表的結構 `ogs_manager`
--

CREATE TABLE IF NOT EXISTS `ogs_manager` (
  `id` int(11) NOT NULL auto_increment,
  `level` int(11) default NULL COMMENT '管理員權限層級',
  `account` text NOT NULL COMMENT '管理員帳號 (E-mail)',
  `password` varchar(32) NOT NULL COMMENT '管理員密碼 (md5)',
  `name` varchar(255) NOT NULL COMMENT '管理員名稱',
  `status` tinyint(1) NOT NULL default '1' COMMENT '管理員狀態',
  `ban` tinyint(1) NOT NULL default '0' COMMENT '封鎖',
  `verify` varchar(32) NOT NULL COMMENT '管理員認證碼 (md5)',
  PRIMARY KEY  (`id`),
  KEY `level` (`level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理員資料表' AUTO_INCREMENT=2 ;

--
-- 轉存資料表中的資料 `ogs_manager`
--

INSERT INTO `ogs_manager` (`id`, `level`, `account`, `password`, `name`, `status`, `ban`, `verify`) VALUES
(1, 1, 'potsonhumer@gmail.com', 'c8e2f3cd8913f5332972952517b814d9', 'Potson Humer', 1, 0, '74b87337454200d4d33f80c4663dc5e5');

-- --------------------------------------------------------

--
-- 表的結構 `ogs_member`
--

CREATE TABLE IF NOT EXISTS `ogs_member` (
  `id` int(11) NOT NULL auto_increment,
  `status` tinyint(1) NOT NULL default '0' COMMENT '會員狀態 0 => 關閉 , 1 => 啟動',
  `account` varchar(255) NOT NULL COMMENT '帳號',
  `password` varchar(32) NOT NULL COMMENT '密碼 (md5)',
  `name` varchar(255) NOT NULL COMMENT '名稱',
  `avatar` text NOT NULL COMMENT '大頭圖 (暫無使用)',
  `gender` tinyint(1) default NULL COMMENT '性別; nulll => 未選擇 , 0 => 女性, 1 => 男性',
  `birth` date default NULL COMMENT '生日',
  `company` varchar(255) NOT NULL COMMENT '公司名稱',
  `address` text NOT NULL COMMENT '地址',
  `tel` varchar(255) NOT NULL COMMENT '電話',
  `cell` varchar(255) NOT NULL COMMENT '手機',
  `createdate` timestamp NOT NULL default '0000-00-00 00:00:00' COMMENT '創建時間',
  `modifydate` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT '最後修改時間',
  `verify` tinyint(1) NOT NULL default '0' COMMENT '會員認證(E-mail); 0 => 未認證 , 1 => 已認證',
  `verify_code` varchar(32) NOT NULL COMMENT '認證碼',
  PRIMARY KEY  (`id`),
  KEY `verify_code` (`verify_code`),
  KEY `account` (`account`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='OGS 會員資料表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_news`
--

CREATE TABLE IF NOT EXISTS `ogs_news` (
  `id` int(11) NOT NULL auto_increment,
  `lang_id` int(11) NOT NULL COMMENT '語系 id',
  `parent` int(11) NOT NULL COMMENT '分類 id',
  `seo_id` int(11) NOT NULL COMMENT '行銷 id',
  `subject` varchar(255) NOT NULL COMMENT '標題',
  `langtag` char(3) NOT NULL COMMENT '語系標籤; eng,cht,chs...etc',
  `sort` int(11) NOT NULL COMMENT '排序',
  `status` tinyint(1) NOT NULL COMMENT '狀態; 0 => 關閉 , 1 => 開啟',
  `content` text NOT NULL COMMENT '內容',
  `showdate` date NOT NULL COMMENT ' 顯示時間',
  PRIMARY KEY  (`id`),
  KEY `lang_id` (`lang_id`),
  KEY `seo_id` (`seo_id`),
  KEY `parent` (`parent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='最新消息' AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_news_cate`
--

CREATE TABLE IF NOT EXISTS `ogs_news_cate` (
  `id` int(11) NOT NULL auto_increment,
  `lang_id` int(11) NOT NULL COMMENT '語系 id',
  `seo_id` int(11) NOT NULL COMMENT '行銷 id',
  `subject` varchar(255) NOT NULL COMMENT '標題',
  `langtag` char(3) NOT NULL COMMENT '語系標籤; eng,cht,chs...etc',
  `sort` int(11) NOT NULL COMMENT '排序',
  `status` tinyint(1) NOT NULL COMMENT '狀態; 0 => 關閉 , 1 => 開啟',
  PRIMARY KEY  (`id`),
  KEY `lang_id` (`lang_id`),
  KEY `seo_id` (`seo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='最新消息分類' AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_order`
--

CREATE TABLE IF NOT EXISTS `ogs_order` (
  `id` int(11) NOT NULL auto_increment,
  `serial` varchar(32) NOT NULL COMMENT '訂單編號',
  `m_id` int(11) NOT NULL COMMENT '會員 id',
  `status` int(11) NOT NULL default '0' COMMENT '訂單狀態; 0 => 新訂單 , 1 => 處理中, 2 => 出貨中, 3 => 已完成, 10 => 取消訂單, 11 => 帳號尚未認證',
  `payment_type` int(11) NOT NULL COMMENT '付款方式; 0 => 匯款, 1 => 貨到付款',
  `name` varchar(255) NOT NULL COMMENT '訂購人名稱',
  `tel` varchar(30) NOT NULL COMMENT '訂購人電話',
  `cell` varchar(30) NOT NULL COMMENT '訂購人手機',
  `address` text NOT NULL COMMENT '訂購人地址',
  `email` text NOT NULL COMMENT '訂購人E-mail',
  `add_name` varchar(255) NOT NULL COMMENT '收件人名稱',
  `add_tel` int(11) NOT NULL COMMENT '收件人電話',
  `add_cell` int(11) NOT NULL COMMENT '收件人手機',
  `add_address` text NOT NULL COMMENT '收件人地址',
  `add_email` text NOT NULL COMMENT '收件人E-mail',
  `info` text NOT NULL COMMENT '備註',
  `subtotal` int(11) NOT NULL COMMENT '產品小計',
  `ship` int(11) NOT NULL COMMENT '運費',
  `total` int(11) NOT NULL COMMENT '總價',
  `createdate` timestamp NOT NULL default '0000-00-00 00:00:00',
  `modifydate` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT '修改時間',
  `last5` int(5) default NULL COMMENT '匯款後五碼',
  `del` tinyint(1) NOT NULL default '0' COMMENT '刪除標籤 0 => 未刪除 , 1 => 刪除',
  PRIMARY KEY  (`id`),
  KEY `serial` (`serial`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_order_item`
--

CREATE TABLE IF NOT EXISTS `ogs_order_item` (
  `id` int(11) NOT NULL auto_increment,
  `serial` varchar(32) NOT NULL COMMENT '訂單編號',
  `p_id` int(11) NOT NULL COMMENT '產品 id',
  `name` varchar(255) NOT NULL COMMENT '產品名稱',
  `amount` int(11) NOT NULL COMMENT '購買數量',
  `price` int(11) NOT NULL COMMENT '購買價格',
  PRIMARY KEY  (`id`),
  KEY `serial` (`serial`),
  KEY `p_id` (`p_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_products`
--

CREATE TABLE IF NOT EXISTS `ogs_products` (
  `id` int(11) NOT NULL auto_increment,
  `lang_id` int(11) NOT NULL COMMENT '語系 id',
  `parent` int(11) NOT NULL COMMENT '分類 id',
  `seo_id` int(11) NOT NULL COMMENT '行銷 id',
  `subject` varchar(255) NOT NULL COMMENT '標題',
  `langtag` char(3) NOT NULL COMMENT '語系標籤; eng,cht,chs...etc',
  `sort` int(11) NOT NULL COMMENT '排序',
  `status` tinyint(1) NOT NULL COMMENT '狀態; 0 => 關閉 , 1 => 開啟',
  `info` text NOT NULL COMMENT '簡述',
  `content` text NOT NULL COMMENT '內容',
  `price` int(11) NOT NULL COMMENT '售價',
  `discount` int(11) NOT NULL COMMENT '特價',
  PRIMARY KEY  (`id`),
  KEY `lang_id` (`lang_id`),
  KEY `seo_id` (`seo_id`),
  KEY `parent` (`parent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='最新消息' AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_products_cate`
--

CREATE TABLE IF NOT EXISTS `ogs_products_cate` (
  `id` int(11) NOT NULL auto_increment,
  `parent` int(11) default NULL COMMENT '分類 id',
  `lang_id` int(11) NOT NULL COMMENT '語系 id',
  `seo_id` int(11) NOT NULL COMMENT '行銷 id',
  `subject` varchar(255) NOT NULL COMMENT '標題',
  `langtag` char(3) NOT NULL COMMENT '語系標籤; eng,cht,chs...etc',
  `sort` int(11) NOT NULL COMMENT '排序',
  `status` tinyint(1) NOT NULL COMMENT '狀態; 0 => 關閉 , 1 => 開啟',
  PRIMARY KEY  (`id`),
  KEY `lang_id` (`lang_id`),
  KEY `seo_id` (`seo_id`),
  KEY `parent` (`parent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='產品分類' AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_seo`
--

CREATE TABLE IF NOT EXISTS `ogs_seo` (
  `id` int(11) NOT NULL auto_increment,
  `langtag` char(3) NOT NULL COMMENT '語系標籤; eng,cht,chs...etc',
  `name` char(20) NOT NULL COMMENT '對應程式主頁 seo',
  `title` varchar(255) NOT NULL COMMENT '網頁抬頭',
  `keywords` text NOT NULL COMMENT '關鍵字',
  `description` text NOT NULL COMMENT '描述',
  `filename` varchar(255) NOT NULL COMMENT '行銷檔名',
  `h1` varchar(255) NOT NULL COMMENT '網頁標題',
  `short_desc` text NOT NULL COMMENT '行銷簡述',
  PRIMARY KEY  (`id`),
  KEY `langtag` (`langtag`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='行銷資料表' AUTO_INCREMENT=441 ;

--
-- 轉存資料表中的資料 `ogs_seo`
--

INSERT INTO `ogs_seo` (`id`, `langtag`, `name`, `title`, `keywords`, `description`, `filename`, `h1`, `short_desc`) VALUES
(434, 'cht', 'index', '', '', '', '', '', ''),
(435, 'cht', 'news', '', '', '', '', '', ''),
(436, 'cht', 'products', '', '', '', '', '', ''),
(437, 'cht', 'faq', '', '', '', '', '', ''),
(438, 'cht', 'member', '', '', '', '', '', ''),
(439, 'cht', 'sitemap', '', '', '', '', '', ''),
(440, 'cht', 'contact', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- 表的結構 `ogs_system`
--

CREATE TABLE IF NOT EXISTS `ogs_system` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL COMMENT '網站名稱',
  `email` text NOT NULL COMMENT '系統信箱',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系統設定' AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_verify`
--

CREATE TABLE IF NOT EXISTS `ogs_verify` (
  `id` int(11) NOT NULL auto_increment,
  `manager_id` int(11) NOT NULL COMMENT '管理員 id',
  `m_id` int(11) NOT NULL COMMENT '會員 id',
  `verify_code` varchar(32) NOT NULL COMMENT '認證碼',
  `createdate` datetime NOT NULL COMMENT '創建時間',
  `used` tinyint(1) NOT NULL default '0' COMMENT '是否使用; 0 => 未使用, 1 => 已使用',
  PRIMARY KEY  (`id`),
  KEY `manager_id` (`manager_id`,`m_id`,`verify_code`,`used`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='認證碼對應表' AUTO_INCREMENT=74 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
