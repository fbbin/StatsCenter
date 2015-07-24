-- phpMyAdmin SQL Dump
-- version 4.0.10
-- http://www.phpmyadmin.net
--
-- 主机: 127.0.0.1
-- 生成日期: 2015-06-11 18:33:21
-- 服务器版本: 5.5.43-0ubuntu0.14.04.1
-- PHP 版本: 5.6.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `mostat`
--

-- --------------------------------------------------------

--
-- 表的结构 `group`
--

CREATE TABLE IF NOT EXISTS `group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` varchar(64) NOT NULL COMMENT '组长',
  `name` varchar(128) NOT NULL COMMENT '权限组名称',
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `interface`
--

CREATE TABLE IF NOT EXISTS `interface` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `alias` varchar(64) NOT NULL COMMENT '接口别名',
  `api_key` varchar(64) NOT NULL,
  `owner_uid` int(11) NOT NULL,
  `owner_name` varchar(64) NOT NULL,
  `backup_uids` varchar(128) NOT NULL,
  `backup_name` varchar(255) NOT NULL,
  `alert_uids` varchar(255) NOT NULL COMMENT '报警uids',
  `enable_alert` tinyint(1) NOT NULL COMMENT '是否开启弹窗 1 开启 2 关闭',
  `alert_types` varchar(50) NOT NULL COMMENT '报警类型 1 弹窗 2 短信 3 邮件',
  `alert_int` int(11) NOT NULL,
  `intro` varchar(128) NOT NULL,
  `succ_hold` tinyint(1) NOT NULL,
  `wave_hold` tinyint(1) NOT NULL,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `module_id` int(11) NOT NULL COMMENT '模块id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5002241 ;

-- --------------------------------------------------------

--
-- 表的结构 `logs`
--

CREATE TABLE IF NOT EXISTS `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_id` int(11) NOT NULL,
  `interface_id` int(11) NOT NULL,
  `special_id` varchar(64) NOT NULL,
  `time` int(10) unsigned NOT NULL,
  `user_id` int(11) NOT NULL,
  `client_ip` varchar(16) NOT NULL,
  `level` tinyint(4) NOT NULL,
  `txt` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `log_interface`
--

CREATE TABLE IF NOT EXISTS `log_interface` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_id` int(11) NOT NULL COMMENT '模块id',
  `name` varchar(64) NOT NULL,
  `alias` varchar(64) NOT NULL COMMENT '别名',
  `owner_uid` int(11) NOT NULL,
  `owner_name` varchar(64) NOT NULL,
  `backup_uids` varchar(128) NOT NULL,
  `backup_name` varchar(255) NOT NULL,
  `intro` varchar(128) NOT NULL,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `module`
--

CREATE TABLE IF NOT EXISTS `module` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `owner_uid` int(11) NOT NULL,
  `backup_uids` varchar(255) NOT NULL,
  `owner_name` varchar(128) NOT NULL,
  `backup_name` varchar(128) NOT NULL,
  `intro` varchar(128) NOT NULL,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `gid` int(11) NOT NULL COMMENT '权限组ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1000293 ;

-- --------------------------------------------------------

--
-- 表的结构 `project`
--

CREATE TABLE IF NOT EXISTS `project` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `intro` varchar(255) NOT NULL,
  `add_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- 表的结构 `stats`
--

CREATE TABLE IF NOT EXISTS `stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `interface_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `time_key` int(11) NOT NULL,
  `date_key` date NOT NULL,
  `total_count` int(11) NOT NULL,
  `fail_count` int(11) NOT NULL,
  `total_time` double NOT NULL,
  `total_fail_time` double NOT NULL,
  `avg_time` int(11) NOT NULL,
  `avg_fail_time` int(11) NOT NULL,
  `max_time` int(11) NOT NULL DEFAULT '0' COMMENT '最长时间',
  `min_time` int(11) NOT NULL DEFAULT '0' COMMENT '最小时间',
  `fail_server` text NOT NULL,
  `succ_server` text NOT NULL,
  `total_server` text NOT NULL,
  `fail_client` text NOT NULL,
  `succ_client` text NOT NULL,
  `total_client` text NOT NULL,
  `ret_code` text NOT NULL,
  `succ_ret_code` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `module_id` (`module_id`),
  KEY `interface_id` (`interface_id`),
  KEY `date_key` (`date_key`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=74 ;

-- --------------------------------------------------------

--
-- 表的结构 `stats_client`
--

CREATE TABLE IF NOT EXISTS `stats_client` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `interface_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `ip` varchar(16) NOT NULL,
  `time_key` int(11) NOT NULL,
  `date_key` date NOT NULL,
  `total_count` int(11) NOT NULL,
  `fail_count` int(11) NOT NULL,
  `total_time` double NOT NULL,
  `total_fail_time` double NOT NULL,
  `avg_time` int(11) NOT NULL,
  `avg_fail_time` int(11) NOT NULL,
  `max_time` int(11) NOT NULL,
  `min_time` int(11) NOT NULL,
  `fail_server` text NOT NULL,
  `succ_server` text NOT NULL,
  `total_server` text NOT NULL,
  `ret_code` text NOT NULL,
  `succ_ret_code` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `module_id` (`module_id`),
  KEY `interface_id` (`interface_id`),
  KEY `interface_id_2` (`interface_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=57250164 ;

-- --------------------------------------------------------

--
-- 表的结构 `stats_server`
--

CREATE TABLE IF NOT EXISTS `stats_server` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `interface_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `ip` varchar(16) NOT NULL,
  `time_key` int(11) NOT NULL,
  `date_key` date NOT NULL,
  `total_count` int(11) NOT NULL,
  `fail_count` int(11) NOT NULL,
  `total_time` double NOT NULL,
  `total_fail_time` double NOT NULL,
  `avg_time` int(11) NOT NULL,
  `avg_fail_time` int(11) NOT NULL,
  `max_time` int(11) NOT NULL,
  `min_time` int(11) NOT NULL,
  `fail_client` text NOT NULL,
  `succ_client` text NOT NULL,
  `total_client` text NOT NULL,
  `ret_code` text NOT NULL,
  `succ_ret_code` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `module_id` (`module_id`),
  KEY `interface_id` (`interface_id`),
  KEY `interface_id_2` (`interface_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=88186989 ;

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT 'YYUID',
  `project_id` varchar(50) NOT NULL COMMENT '可参与项目',
  `mobile` varchar(20) NOT NULL,
  `gid` int(11) NOT NULL COMMENT '权限组ID',
  `usertype` tinyint(4) NOT NULL,
  `username` varchar(128) NOT NULL COMMENT '用户名',
  `password` char(40) NOT NULL,
  `realname` varchar(128) NOT NULL,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=68 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
