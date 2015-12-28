-- phpMyAdmin SQL Dump
-- version 4.2.12deb2+deb8u1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2015-12-28 17:30:31
-- 服务器版本： 5.5.46-0+deb8u1-log
-- PHP Version: 5.6.14-0+deb8u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `platform`
--

-- --------------------------------------------------------

--
-- 表的结构 `app_release`
--

CREATE TABLE IF NOT EXISTS `app_release` (
`id` int(11) unsigned NOT NULL,
  `app_id` int(11) unsigned NOT NULL,
  `force_upgrade` tinyint(1) unsigned NOT NULL COMMENT '是否强制更新，1是，所有旧版本都强制更新到当前版本，0否',
  `force_upgrade_version` text NOT NULL COMMENT '指定需要强制更新的版本，force_upgrade为1时不起作用',
  `version_number` varchar(255) NOT NULL COMMENT '版本号，格式x.y.z，如1.2.4',
  `version_int` int(11) NOT NULL COMMENT '版本号x.y.z的数字表示',
  `prompt_title` varchar(255) NOT NULL COMMENT '弹框标题',
  `prompt_content` text NOT NULL COMMENT '弹框内容',
  `prompt_interval` int(11) NOT NULL COMMENT '弹框提示周期',
  `prompt_confirm_button_text` varchar(50) NOT NULL COMMENT '弹框确定按钮文字',
  `prompt_cancel_button_text` varchar(59) NOT NULL COMMENT '弹框取消按钮文字',
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `app_release`
--
ALTER TABLE `app_release`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `app_id_version` (`app_id`,`version_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `app_release`
--
ALTER TABLE `app_release`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
