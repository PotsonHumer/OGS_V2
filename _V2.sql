-- phpMyAdmin SQL Dump
-- version 3.4.10.1
-- http://www.phpmyadmin.net
--
-- 主機: localhost
-- 產生日期: 2016 年 06 月 28 日 17:53
-- 伺服器版本: 5.5.47
-- PHP 版本: 5.5.9-1ubuntu4.14

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 資料庫: `ogs_v2`
--

-- --------------------------------------------------------

--
-- 表的結構 `ogs_ad`
--

CREATE TABLE IF NOT EXISTS `ogs_ad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang_id` int(11) NOT NULL COMMENT '語系 id',
  `cateID` int(11) NOT NULL COMMENT '分類 id',
  `subject` varchar(255) NOT NULL COMMENT '標題',
  `langtag` char(3) NOT NULL COMMENT '語系標籤; eng,cht,chs...etc',
  `sort` int(11) NOT NULL COMMENT '排序',
  `status` tinyint(1) NOT NULL COMMENT '狀態; 0 => 關閉 , 1 => 開啟 , 2 => 依照時間',
  `content` text NOT NULL COMMENT '內容',
  `link` text NOT NULL COMMENT '廣告連結',
  `startdate` date DEFAULT NULL COMMENT '起始時間',
  `limitdate` date DEFAULT NULL COMMENT '到期時間',
  PRIMARY KEY (`id`),
  KEY `lang_id` (`lang_id`),
  KEY `parent` (`cateID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='廣告' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_ad_cate`
--

CREATE TABLE IF NOT EXISTS `ogs_ad_cate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang_id` int(11) NOT NULL COMMENT '語系 id',
  `subject` varchar(255) NOT NULL COMMENT '標題',
  `langtag` char(3) NOT NULL COMMENT '語系標籤',
  `sort` int(11) NOT NULL COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '狀態',
  PRIMARY KEY (`id`),
  KEY `lang_id` (`lang_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='廣告分類' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_ban`
--

CREATE TABLE IF NOT EXISTS `ogs_ban` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(39) DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '禁止開始時間',
  PRIMARY KEY (`id`),
  KEY `ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='禁止登入後台 IP' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_blog`
--

CREATE TABLE IF NOT EXISTS `ogs_blog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang_id` int(11) NOT NULL COMMENT '語系 id',
  `parent` int(11) NOT NULL COMMENT '分類 id',
  `seo_id` int(11) NOT NULL COMMENT '行銷 id',
  `subject` varchar(255) NOT NULL COMMENT '標題',
  `langtag` char(3) NOT NULL COMMENT '語系標籤; eng,cht,chs...etc',
  `sort` int(11) NOT NULL COMMENT '排序',
  `status` tinyint(1) NOT NULL COMMENT '狀態; 0 => 關閉 , 1 => 開啟',
  `content` text NOT NULL COMMENT '內容',
  `showdate` date NOT NULL COMMENT ' 顯示時間',
  `hot` tinyint(1) NOT NULL DEFAULT '0' COMMENT '熱門文章',
  `score` int(1) NOT NULL DEFAULT '0' COMMENT '推薦指數',
  `view_number` int(11) NOT NULL DEFAULT '0' COMMENT '實際瀏覽數',
  `view_custom` int(11) NOT NULL DEFAULT '0' COMMENT '自訂瀏覽數',
  PRIMARY KEY (`id`),
  KEY `lang_id` (`lang_id`),
  KEY `seo_id` (`seo_id`),
  KEY `parent` (`parent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='最新消息' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_blog_cate`
--

CREATE TABLE IF NOT EXISTS `ogs_blog_cate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang_id` int(11) NOT NULL COMMENT '語系 id',
  `seo_id` int(11) NOT NULL COMMENT '行銷 id',
  `subject` varchar(255) NOT NULL COMMENT '標題',
  `langtag` char(3) NOT NULL COMMENT '語系標籤; eng,cht,chs...etc',
  `sort` int(11) NOT NULL COMMENT '排序',
  `status` tinyint(1) NOT NULL COMMENT '狀態; 0 => 關閉 , 1 => 開啟',
  PRIMARY KEY (`id`),
  KEY `lang_id` (`lang_id`),
  KEY `seo_id` (`seo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='最新消息分類' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_contact`
--

CREATE TABLE IF NOT EXISTS `ogs_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `m_id` int(11) DEFAULT NULL COMMENT '會員 id',
  `name` varchar(255) NOT NULL COMMENT '姓名',
  `gender` tinyint(1) DEFAULT NULL COMMENT '性別',
  `tel` varchar(255) NOT NULL COMMENT '電話',
  `fax` varchar(255) NOT NULL COMMENT '傳真',
  `cell` varchar(255) NOT NULL COMMENT '手機',
  `address` text NOT NULL COMMENT '地址',
  `email` text NOT NULL COMMENT '信箱',
  `content` text NOT NULL COMMENT '聯絡內容',
  `reply` text NOT NULL COMMENT '回覆內容',
  `createdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '聯絡時間',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='聯絡我們' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_contact_subject`
--

CREATE TABLE IF NOT EXISTS `ogs_contact_subject` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang_id` int(11) NOT NULL,
  `langtag` char(3) NOT NULL,
  `subject` varchar(255) NOT NULL COMMENT '主題名稱',
  `email` text NOT NULL COMMENT '管理員 E-mail',
  `sort` int(11) NOT NULL COMMENT '排序',
  PRIMARY KEY (`id`),
  KEY `sort` (`sort`),
  KEY `lang_id` (`lang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_faq`
--

CREATE TABLE IF NOT EXISTS `ogs_faq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang_id` int(11) NOT NULL,
  `parent` int(11) NOT NULL COMMENT '分類 id',
  `subject` varchar(255) NOT NULL COMMENT '標題',
  `langtag` char(3) NOT NULL COMMENT '語系標籤; eng,cht,chs...etc',
  `sort` int(11) NOT NULL COMMENT '排序',
  `status` tinyint(1) NOT NULL COMMENT '狀態; 0 => 關閉 , 1 => 開啟',
  `content` text NOT NULL COMMENT '內容',
  PRIMARY KEY (`id`),
  KEY `parent` (`parent`),
  KEY `lang_id` (`lang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='問與答' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_faq_cate`
--

CREATE TABLE IF NOT EXISTS `ogs_faq_cate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang_id` int(11) NOT NULL COMMENT '語系 id',
  `seo_id` int(11) NOT NULL COMMENT '行銷 id',
  `subject` varchar(255) NOT NULL COMMENT '標題',
  `langtag` char(3) NOT NULL COMMENT '語系標籤; eng,cht,chs...etc',
  `sort` int(11) NOT NULL COMMENT '排序',
  `status` tinyint(1) NOT NULL COMMENT '狀態; 0 => 關閉 , 1 => 開啟',
  PRIMARY KEY (`id`),
  KEY `lang_id` (`lang_id`),
  KEY `seo_id` (`seo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='問與答分類' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_feedback`
--

CREATE TABLE IF NOT EXISTS `ogs_feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `m_id` int(11) DEFAULT NULL COMMENT '會員 id',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '狀態; 0 => 關閉 , 1 => 開啟',
  `name` varchar(255) NOT NULL,
  `gender` tinyint(1) DEFAULT NULL COMMENT '性別; nulll => 未選擇 , 0 => 女性, 1 => 男性',
  `email` text NOT NULL,
  `content` text NOT NULL,
  `score` int(1) NOT NULL DEFAULT '0' COMMENT '評分 1~5',
  `createdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '留言時間',
  PRIMARY KEY (`id`),
  KEY `m_id` (`m_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='反饋留言板' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_gallery`
--

CREATE TABLE IF NOT EXISTS `ogs_gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang_id` int(11) NOT NULL COMMENT '語系 id',
  `parent` int(11) NOT NULL COMMENT '分類 id',
  `seo_id` int(11) NOT NULL COMMENT '行銷 id',
  `subject` varchar(255) NOT NULL COMMENT '標題',
  `langtag` char(3) NOT NULL COMMENT '語系標籤; eng,cht,chs...etc',
  `sort` int(11) NOT NULL COMMENT '排序',
  `status` tinyint(1) NOT NULL COMMENT '狀態; 0 => 關閉 , 1 => 開啟',
  `content` text NOT NULL COMMENT '內容',
  `dirpath` varchar(255) NOT NULL COMMENT '相簿目錄指向',
  PRIMARY KEY (`id`),
  KEY `lang_id` (`lang_id`),
  KEY `seo_id` (`seo_id`),
  KEY `parent` (`parent`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='最新消息' AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_gallery_cate`
--

CREATE TABLE IF NOT EXISTS `ogs_gallery_cate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang_id` int(11) NOT NULL COMMENT '語系 id',
  `seo_id` int(11) NOT NULL COMMENT '行銷 id',
  `subject` varchar(255) NOT NULL COMMENT '標題',
  `langtag` char(3) NOT NULL COMMENT '語系標籤; eng,cht,chs...etc',
  `sort` int(11) NOT NULL COMMENT '排序',
  `status` tinyint(1) NOT NULL COMMENT '狀態; 0 => 關閉 , 1 => 開啟',
  PRIMARY KEY (`id`),
  KEY `lang_id` (`lang_id`),
  KEY `seo_id` (`seo_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='最新消息分類' AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_images`
--

CREATE TABLE IF NOT EXISTS `ogs_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` text NOT NULL COMMENT '圖片路徑',
  `alt` text NOT NULL COMMENT '圖片描述',
  `title` text NOT NULL COMMENT '圖片抬頭',
  `sheet` varchar(255) NOT NULL COMMENT '對應資料表',
  `related` int(11) NOT NULL COMMENT '關聯資料 id',
  PRIMARY KEY (`id`),
  KEY `related` (`related`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='圖片資料表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_intro`
--

CREATE TABLE IF NOT EXISTS `ogs_intro` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang_id` int(11) NOT NULL COMMENT '語系 id',
  `seo_id` int(11) NOT NULL COMMENT '行銷 id',
  `subject` varchar(255) NOT NULL COMMENT '標題',
  `langtag` char(3) NOT NULL COMMENT '語系標籤; eng,cht,chs...etc',
  `sort` int(11) NOT NULL COMMENT '排序',
  `status` tinyint(1) NOT NULL COMMENT '狀態; 0 => 關閉 , 1 => 開啟',
  `content` text NOT NULL COMMENT '內容',
  PRIMARY KEY (`id`),
  KEY `lang_id` (`lang_id`),
  KEY `seo_id` (`seo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='介紹頁' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_lang`
--

CREATE TABLE IF NOT EXISTS `ogs_lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sheet` varchar(255) NOT NULL COMMENT '對應資料表',
  `related` text NOT NULL COMMENT '關聯資料 id (json); ex: cht => 1, chs => 2, eng => 3',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='lang id 紀錄表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_level`
--

CREATE TABLE IF NOT EXISTS `ogs_level` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '權限名稱',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '權限開關; 0 => 關閉, 1 => 開啟',
  `class` text COMMENT '授權參數 (json)',
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='管理員權限層級' AUTO_INCREMENT=2 ;

--
-- 轉存資料表中的資料 `ogs_level`
--

INSERT INTO `ogs_level` (`id`, `name`, `status`, `class`) VALUES
(1, '總管理員', 1, '{"system":"1","manager":"1","ad":"1","intro":"1","faq":"1","news":"1","products":"1","order":"1","member":"1","contact":"1","feedback":"1","blog":"1","gallery":"1","rewrite":"1"}');

-- --------------------------------------------------------

--
-- 表的結構 `ogs_manager`
--

CREATE TABLE IF NOT EXISTS `ogs_manager` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `level` int(11) DEFAULT NULL COMMENT '管理員權限層級',
  `account` text NOT NULL COMMENT '管理員帳號 (E-mail)',
  `password` varchar(32) NOT NULL COMMENT '管理員密碼 (md5)',
  `name` varchar(255) NOT NULL COMMENT '管理員名稱',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '管理員狀態',
  `ban` tinyint(1) NOT NULL DEFAULT '0' COMMENT '封鎖',
  `verify` varchar(32) NOT NULL COMMENT '管理員認證碼 (md5)',
  PRIMARY KEY (`id`),
  KEY `level` (`level`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='管理員資料表' AUTO_INCREMENT=5 ;

--
-- 轉存資料表中的資料 `ogs_manager`
--

INSERT INTO `ogs_manager` (`id`, `level`, `account`, `password`, `name`, `status`, `ban`, `verify`) VALUES
(1, 1, 'potsonhumer@gmail.com', 'c8e2f3cd8913f5332972952517b814d9', 'Potson Humer', 1, 0, '74b87337454200d4d33f80c4663dc5e5'),
(2, 1, 'jerry.icisco@gmail.com', '5dd3e025ab80c5f5694757852918e7ce', 'Jerry', 1, 0, '2944e854b075378730c97081224ae085'),
(3, 1, 'renee2111537@hotmail.com', '670b14728ad9902aecba32e22fa4f6bd', 'reneehsu', 1, 0, 'ad58590bedcaa5a860599789fb5802c8'),
(4, 1, 'wei820529@gmail.com', '670b14728ad9902aecba32e22fa4f6bd', 'Rain', 1, 0, 'adeda6bbbe65ed10ab5f92b6342a0ed2');

-- --------------------------------------------------------

--
-- 表的結構 `ogs_member`
--

CREATE TABLE IF NOT EXISTS `ogs_member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '會員狀態 0 => 關閉 , 1 => 啟動',
  `account` varchar(255) NOT NULL COMMENT '帳號',
  `password` varchar(32) NOT NULL COMMENT '密碼 (md5)',
  `name` varchar(255) NOT NULL COMMENT '名稱',
  `avatar` text NOT NULL COMMENT '大頭圖 (暫無使用)',
  `gender` tinyint(1) DEFAULT NULL COMMENT '性別; nulll => 未選擇 , 0 => 女性, 1 => 男性',
  `birth` date DEFAULT NULL COMMENT '生日',
  `company` varchar(255) NOT NULL COMMENT '公司名稱',
  `address` text NOT NULL COMMENT '地址',
  `tel` varchar(255) NOT NULL COMMENT '電話',
  `cell` varchar(255) NOT NULL COMMENT '手機',
  `createdate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '創建時間',
  `modifydate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最後修改時間',
  `verify` tinyint(1) NOT NULL DEFAULT '0' COMMENT '會員認證(E-mail); 0 => 未認證 , 1 => 已認證',
  `verify_code` varchar(32) NOT NULL COMMENT '認證碼',
  PRIMARY KEY (`id`),
  KEY `verify_code` (`verify_code`),
  KEY `account` (`account`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='OGS 會員資料表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_message`
--

CREATE TABLE IF NOT EXISTS `ogs_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dataID` int(11) DEFAULT NULL COMMENT '綁定功能的資料 id',
  `m_id` int(11) DEFAULT NULL COMMENT '會員 id',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '狀態',
  `func` varchar(255) NOT NULL COMMENT '綁定使用的功能',
  `name` varchar(255) NOT NULL,
  `gender` tinyint(1) DEFAULT NULL,
  `cell` varchar(255) NOT NULL,
  `email` text NOT NULL,
  `subject` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `createdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reply` int(11) DEFAULT NULL COMMENT '回覆標籤; 紀錄回覆的資料 id',
  PRIMARY KEY (`id`),
  KEY `m_id` (`m_id`),
  KEY `dataID` (`dataID`),
  KEY `status` (`status`),
  KEY `reply` (`reply`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='留言板功能' AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_news`
--

CREATE TABLE IF NOT EXISTS `ogs_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang_id` int(11) NOT NULL COMMENT '語系 id',
  `parent` int(11) DEFAULT NULL COMMENT '分類 id',
  `seo_id` int(11) NOT NULL COMMENT '行銷 id',
  `subject` varchar(255) NOT NULL COMMENT '標題',
  `langtag` char(3) NOT NULL COMMENT '語系標籤; eng,cht,chs...etc',
  `sort` int(11) NOT NULL COMMENT '排序',
  `status` tinyint(1) NOT NULL COMMENT '狀態; 0 => 關閉 , 1 => 開啟',
  `content` text NOT NULL COMMENT '內容',
  `showdate` date NOT NULL COMMENT ' 顯示時間',
  PRIMARY KEY (`id`),
  KEY `lang_id` (`lang_id`),
  KEY `seo_id` (`seo_id`),
  KEY `parent` (`parent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='最新消息' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_news_cate`
--

CREATE TABLE IF NOT EXISTS `ogs_news_cate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang_id` int(11) NOT NULL COMMENT '語系 id',
  `seo_id` int(11) NOT NULL COMMENT '行銷 id',
  `subject` varchar(255) NOT NULL COMMENT '標題',
  `langtag` char(3) NOT NULL COMMENT '語系標籤; eng,cht,chs...etc',
  `sort` int(11) NOT NULL COMMENT '排序',
  `status` tinyint(1) NOT NULL COMMENT '狀態; 0 => 關閉 , 1 => 開啟',
  PRIMARY KEY (`id`),
  KEY `lang_id` (`lang_id`),
  KEY `seo_id` (`seo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='最新消息分類' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_order`
--

CREATE TABLE IF NOT EXISTS `ogs_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `serial` varchar(32) NOT NULL COMMENT '訂單編號',
  `m_id` int(11) NOT NULL COMMENT '會員 id',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '訂單狀態; 0 => 新訂單 , 1 => 處理中, 2 => 出貨中, 3 => 已完成, 10 => 取消訂單, 11 => 帳號尚未認證',
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
  `createdate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modifydate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改時間',
  `last5` int(5) DEFAULT NULL COMMENT '匯款後五碼',
  `del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '刪除標籤 0 => 未刪除 , 1 => 刪除',
  PRIMARY KEY (`id`),
  KEY `serial` (`serial`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_order_item`
--

CREATE TABLE IF NOT EXISTS `ogs_order_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `serial` varchar(32) NOT NULL COMMENT '訂單編號',
  `p_id` int(11) NOT NULL COMMENT '產品 id',
  `name` varchar(255) NOT NULL COMMENT '產品名稱',
  `amount` int(11) NOT NULL COMMENT '購買數量',
  `price` int(11) NOT NULL COMMENT '購買價格',
  PRIMARY KEY (`id`),
  KEY `serial` (`serial`),
  KEY `p_id` (`p_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_products`
--

CREATE TABLE IF NOT EXISTS `ogs_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`id`),
  KEY `lang_id` (`lang_id`),
  KEY `seo_id` (`seo_id`),
  KEY `parent` (`parent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='最新消息' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_products_cate`
--

CREATE TABLE IF NOT EXISTS `ogs_products_cate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent` int(11) DEFAULT NULL COMMENT '分類 id',
  `lang_id` int(11) NOT NULL COMMENT '語系 id',
  `seo_id` int(11) NOT NULL COMMENT '行銷 id',
  `subject` varchar(255) NOT NULL COMMENT '標題',
  `langtag` char(3) NOT NULL COMMENT '語系標籤; eng,cht,chs...etc',
  `sort` int(11) NOT NULL COMMENT '排序',
  `status` tinyint(1) NOT NULL COMMENT '狀態; 0 => 關閉 , 1 => 開啟',
  PRIMARY KEY (`id`),
  KEY `lang_id` (`lang_id`),
  KEY `seo_id` (`seo_id`),
  KEY `parent` (`parent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='產品分類' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_products_color`
--

CREATE TABLE IF NOT EXISTS `ogs_products_color` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) NOT NULL,
  `thumb` text COMMENT '顏色圖示',
  `code` varchar(7) DEFAULT NULL COMMENT '顏色代碼',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='產品顏色' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_products_size`
--

CREATE TABLE IF NOT EXISTS `ogs_products_size` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `size` int(11) NOT NULL COMMENT '尺寸代碼 (我也不知道那是啥..)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='產品尺寸' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_products_stock`
--

CREATE TABLE IF NOT EXISTS `ogs_products_stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pID` int(11) NOT NULL COMMENT '對應產品 id',
  `colorID` int(11) NOT NULL COMMENT '對應顏色 id',
  `sizeID` int(11) NOT NULL COMMENT '尺寸 id',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '開啟狀態',
  `amount` int(11) NOT NULL DEFAULT '0' COMMENT '庫存數量',
  PRIMARY KEY (`id`),
  KEY `pID` (`pID`),
  KEY `colorID` (`colorID`),
  KEY `sizeID` (`sizeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='庫存功能' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_seo`
--

CREATE TABLE IF NOT EXISTS `ogs_seo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `langtag` char(3) NOT NULL COMMENT '語系標籤; eng,cht,chs...etc',
  `name` char(20) NOT NULL COMMENT '對應程式主頁 seo',
  `title` varchar(255) NOT NULL COMMENT '網頁抬頭',
  `keywords` text NOT NULL COMMENT '關鍵字',
  `description` text NOT NULL COMMENT '描述',
  `filename` varchar(255) NOT NULL COMMENT '行銷檔名',
  `h1` varchar(255) NOT NULL COMMENT '網頁標題',
  `short_desc` text NOT NULL COMMENT '行銷簡述',
  PRIMARY KEY (`id`),
  KEY `langtag` (`langtag`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='行銷資料表' AUTO_INCREMENT=18 ;

--
-- 轉存資料表中的資料 `ogs_seo`
--

INSERT INTO `ogs_seo` (`id`, `langtag`, `name`, `title`, `keywords`, `description`, `filename`, `h1`, `short_desc`) VALUES
(1, 'cht', 'index', 'TEST', '', '', '', '', ''),
(2, 'cht', 'news', '', '', '', '', '', ''),
(3, 'cht', 'products', '', '', '', '', '', ''),
(4, 'cht', 'faq', '', '', '', '', '', ''),
(5, 'cht', 'member', '', '', '', '', '', ''),
(6, 'cht', 'sitemap', '', '', '', '', '', ''),
(7, 'cht', 'contact', '', '', '', '', '', ''),
(8, 'eng', 'index', '', '', '', '', '', ''),
(9, 'eng', 'news', '', '', '', '', '', ''),
(10, 'eng', 'products', '', '', '', '', '', ''),
(11, 'eng', 'faq', '', '', '', '', '', ''),
(12, 'eng', 'member', '', '', '', '', '', ''),
(13, 'eng', 'sitemap', '', '', '', '', '', ''),
(14, 'eng', 'contact', '', '', '', '', '', ''),
(15, 'cht', 'blog', '', '', '', '', '', ''),
(16, 'cht', 'gallery', '', '', '', '', '', ''),
(17, 'cht', 'nofound', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- 表的結構 `ogs_system`
--

CREATE TABLE IF NOT EXISTS `ogs_system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '網站名稱',
  `email` text NOT NULL COMMENT '系統信箱',
  `ga` varchar(20) NOT NULL,
  `notfound` text NOT NULL COMMENT '404 畫面內文',
  `response` text NOT NULL COMMENT '表單送出畫面',
  `reCAPTCHAkey` varchar(50) NOT NULL COMMENT '驗證碼公開key',
  `reCAPTCHAsecret` varchar(50) NOT NULL COMMENT '驗證碼後端key',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='系統設定' AUTO_INCREMENT=2 ;

--
-- 轉存資料表中的資料 `ogs_system`
--

INSERT INTO `ogs_system` (`id`, `name`, `email`, `ga`, `notfound`, `response`, `reCAPTCHAkey`, `reCAPTCHAsecret`) VALUES
(1, 'Open Grid System ver.2', 'potsonhumer@gmail.com', '', '', '<p style="text-align: center;">{TAG_MSG}</p>', '', '');

-- --------------------------------------------------------

--
-- 表的結構 `ogs_system_custom`
--

CREATE TABLE IF NOT EXISTS `ogs_system_custom` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang_id` int(11) NOT NULL,
  `langtag` char(3) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `sort` int(11) NOT NULL DEFAULT '1',
  `name` varchar(50) NOT NULL COMMENT '公司抬頭',
  `tel` text NOT NULL,
  `fax` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `email` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `lang_id` (`lang_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- 表的結構 `ogs_verify`
--

CREATE TABLE IF NOT EXISTS `ogs_verify` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `manager_id` int(11) NOT NULL COMMENT '管理員 id',
  `m_id` int(11) NOT NULL COMMENT '會員 id',
  `verify_code` varchar(32) NOT NULL COMMENT '認證碼',
  `createdate` datetime NOT NULL COMMENT '創建時間',
  `used` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否使用; 0 => 未使用, 1 => 已使用',
  PRIMARY KEY (`id`),
  KEY `manager_id` (`manager_id`,`m_id`,`verify_code`,`used`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='認證碼對應表' AUTO_INCREMENT=1 ;

--
-- 匯出資料表的 Constraints
--

--
-- 資料表的 Constraints `ogs_products_stock`
--
ALTER TABLE `ogs_products_stock`
  ADD CONSTRAINT `ogs_products_stock_ibfk_3` FOREIGN KEY (`sizeID`) REFERENCES `ogs_products_size` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ogs_products_stock_ibfk_1` FOREIGN KEY (`pID`) REFERENCES `ogs_products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ogs_products_stock_ibfk_2` FOREIGN KEY (`colorID`) REFERENCES `ogs_products_color` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
