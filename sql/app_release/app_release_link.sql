-- phpMyAdmin SQL Dump
-- version 4.2.12deb2+deb8u1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2015-12-28 17:30:48
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
-- 表的结构 `app_release_link`
--

CREATE TABLE IF NOT EXISTS `app_release_link` (
`id` int(11) unsigned NOT NULL,
  `app_id` int(11) unsigned NOT NULL,
  `channel_id` int(11) unsigned NOT NULL,
  `release_id` int(11) unsigned NOT NULL,
  `release_link` varchar(255) NOT NULL,
  `create_time` datetime NOT NULL,
  `update_time` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `app_release_link`
--
ALTER TABLE `app_release_link`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `app_channel_release` (`app_id`,`channel_id`,`release_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `app_release_link`
--
ALTER TABLE `app_release_link`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
