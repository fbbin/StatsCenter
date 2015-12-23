-- MySQL dump 10.13  Distrib 5.5.46, for debian-linux-gnu (x86_64)
--
-- Host: 192.168.1.123    Database: db_push
-- ------------------------------------------------------
-- Server version	5.5.38-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `app`
--

DROP TABLE IF EXISTS `app`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '应用名称，中文描述',
  `app_key` varchar(50) NOT NULL COMMENT '应用代号',
  `os` int(11) NOT NULL COMMENT '平台标识(1: IOS, 2: Android)',
  `apns_cert` varchar(150) DEFAULT NULL COMMENT 'for iOS',
  `apns_pwd` varchar(45) DEFAULT NULL COMMENT 'for iOS',
  `umeng_cert` varchar(150) DEFAULT NULL,
  `umeng_pwd` varchar(45) DEFAULT NULL COMMENT 'for android',
  `enable` tinyint(1) NOT NULL DEFAULT '2',
  `is_inited` tinyint(1) NOT NULL DEFAULT '2',
  `update_time` timestamp NULL DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8 COMMENT='app表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app`
--

LOCK TABLES `app` WRITE;
/*!40000 ALTER TABLE `app` DISABLE KEYS */;
INSERT INTO `app` VALUES (29,'车轮','CheLun',1,'/data/msg_push/ios_certification/chelun_aps_production.p12','eclicks0716',NULL,NULL,1,1,'2015-10-23 10:29:46','2014-09-18 08:58:38'),(30,'车轮','CheLun',2,'/data/msg_push/android_certification/chelun_umeng_CA.p12','123456','/data/msg_push/android_certification/chelun_umeng_client.p12','123456',1,1,'2015-10-23 10:29:41','2014-09-18 08:58:38'),(32,'查违章','QueryViolations',1,'/data/msg_push/ios_certification/violation_aps_production.p12','eclicks',NULL,NULL,2,2,'2015-10-23 10:35:21','2014-10-14 02:45:41'),(33,'查违章','QueryViolations',2,'/data/msg_push/android_certification/queryviolations_umeng_CA.p12','123456','/data/msg_push/android_certification/queryviolations_umeng_client.p12','123456',2,2,'2015-10-23 10:35:28','2014-10-14 02:49:27'),(34,'考驾照','DrivingTest',1,'/data/msg_push/ios_certification/drivingtest_aps_production.p12','eclicks0716',NULL,NULL,2,1,'2015-11-04 11:15:01','2014-10-23 09:38:58'),(35,'考驾照','DrivingTest',2,'/data/msg_push/android_certification/drivingtest_umeng_CA.p12','123456','/data/msg_push/android_certification/drivingtest_umeng_client.p12','123456',2,1,'2015-11-04 11:15:07','2014-12-06 08:26:12'),(36,'福利大全','chelunwelfare',1,'/data/msg_push/ios_certification/chelunwelfare_aps_production.p12','eclicks0716',NULL,NULL,2,2,'2015-04-08 08:04:10','2015-04-08 08:04:10'),(37,'福利大全','chelunwelfare',2,'/data/msg_push/android_certification/chelunwelfare_umeng_CA.p12','123456','/data/msg_push/android_certification/chelunwelfare_umeng_client.p12','123456',2,2,'2015-04-20 09:23:21','2015-04-20 09:23:21'),(38,'考驾照拆分版科目新版1','DrivingTestS1',2,'/data/msg_push/android_certification/drivingtestc1_umeng_CA.p12','123456','/data/msg_push/android_certification/drivingtestc1_umeng_client.p12','123456',2,2,'2015-05-11 03:16:46','2015-05-11 03:16:46'),(39,'考驾照拆分版科目新版2','DrivingTestS2',2,'/data/msg_push/android_certification/drivingtestc2_umeng_CA.p12','123456','/data/msg_push/android_certification/drivingtestc2_umeng_client.p12','123456',2,2,'2015-05-11 03:17:02','2015-05-11 03:17:02'),(40,'考驾照拆分版科目新版3','DrivingTestS3',2,'/data/msg_push/android_certification/drivingtestc3_umeng_CA.p12','123456','/data/msg_push/android_certification/drivingtestc3_umeng_client.p12','123456',2,2,'2015-05-11 03:17:05','2015-05-11 03:17:05'),(41,'考驾照拆分版科目新版4','DrivingTestS4',2,'/data/msg_push/android_certification/drivingtestc4_umeng_CA.p12','123456','/data/msg_push/android_certification/drivingtestc4_umeng_client.p12','123456',2,2,'2015-05-11 03:17:07','2015-05-11 03:17:07'),(42,'考驾照拆分版科目旧版1','DrivingTestC1',2,'/data/msg_push/android_certification/drivingtestc01_umeng_CA.p12','123456','/data/msg_push/android_certification/drivingtestc01_umeng_client.p12','123456',2,2,'2015-05-14 10:08:11','2015-05-14 10:08:11'),(43,'考驾照拆分版科目旧版2','DrivingTestC2',2,'/data/msg_push/android_certification/drivingtestc02_umeng_CA.p12','123456','/data/msg_push/android_certification/drivingtestc02_umeng_client.p12','123456',2,2,'2015-05-14 10:08:15','2015-05-14 10:08:15'),(44,'考驾照拆分版科目旧版3','DrivingTestC3',2,'/data/msg_push/android_certification/drivingtestc03_umeng_CA.p12','123456','/data/msg_push/android_certification/drivingtestc03_umeng_client.p12','123456',2,2,'2015-05-14 10:08:17','2015-05-14 10:08:17'),(45,'考驾照拆分版科目旧版4','DrivingTestC4',2,'/data/msg_push/android_certification/drivingtestc04_umeng_CA.p12','123456','/data/msg_push/android_certification/drivingtestc04_umeng_client.p12','123456',2,2,'2015-05-14 10:08:20','2015-05-14 10:08:20'),(46,'考驾照拆分版科目2','DrivingTestS2',1,'/data/msg_push/ios_certification/drivingtests2_aps_production.p12','eclicks0716',NULL,NULL,2,2,'2015-05-15 11:58:00','2015-05-15 11:58:00'),(47,'考驾照拆分版科目3','DrivingTestS3',1,'/data/msg_push/ios_certification/drivingtests3_aps_production.p12','eclicks0716',NULL,NULL,2,2,'2015-05-15 11:58:04','2015-05-15 11:58:04'),(48,'考驾照拆分版科目4','DrivingTestS4',1,'/data/msg_push/ios_certification/drivingtests4_aps_production.p12','eclicks0716',NULL,NULL,2,2,'2015-05-15 11:58:12','2015-05-15 11:58:12'),(49,'考驾照拆分版科目1','DrivingTestS1',1,'/data/msg_push/ios_certification/drivingtests1_aps_production.p12','eclicks0716',NULL,NULL,2,2,'2015-05-15 11:58:53','2015-05-15 11:58:53'),(50,'考驾照主旧版','DrivingTestOld',2,'/data/msg_push/android_certification/drivingtestold_umeng_CA.p12','123456','/data/msg_push/android_certification/drivingtestold_umeng_client.p12','123456',2,2,'2015-05-22 03:13:09','2015-05-22 03:13:09'),(51,'考驾照拆分版科目旧版1','DrivingTestC1',1,'/data/msg_push/ios_certification/drivingtestci1_aps_production.p12','eclicks0716',NULL,NULL,2,2,'2015-05-28 10:05:28','2015-05-28 10:05:28'),(52,'考驾照拆分版科目旧版2','DrivingTestC2',1,'/data/msg_push/ios_certification/drivingtestci2_aps_production.p12','eclicks0716',NULL,NULL,2,2,'2015-05-28 10:05:31','2015-05-28 10:05:31'),(53,'考驾照拆分版科目旧版3','DrivingTestC3',1,'/data/msg_push/ios_certification/drivingtestci3_aps_production.p12','eclicks0716',NULL,NULL,2,2,'2015-05-28 10:05:34','2015-05-28 10:05:34'),(56,'考驾照拆分版科目旧版4','DrivingTestC4',1,'/data/msg_push/ios_certification/drivingtestci4_aps_production.p12','eclicks0716',NULL,NULL,2,2,'2015-05-28 10:16:01','2015-05-28 10:16:01'),(57,'考驾照拆分复刻版旧版1','DrivingTestC1',2,'/data/msg_push/android_certification/drivingtestcf1_umeng_CA.p12','123456','/data/msg_push/android_certification/drivingtestcf1_umeng_client.p12','123456',2,2,'2015-05-29 03:31:00','2015-05-29 03:31:00'),(62,'考驾照拆分复刻版旧版2','DrivingTestC2',2,'/data/msg_push/android_certification/drivingtestcf2_umeng_CA.p12','123456','/data/msg_push/android_certification/drivingtestcf2_umeng_client.p12','123456',2,2,'2015-05-29 07:30:49','2015-05-29 07:30:49'),(63,'考驾照拆分复刻版旧版3','DrivingTestC3',2,'/data/msg_push/android_certification/drivingtestcf3_umeng_CA.p12','123456','/data/msg_push/android_certification/drivingtestcf3_umeng_client.p12','123456',2,2,'2015-05-29 07:30:51','2015-05-29 07:30:51'),(64,'考驾照拆分复刻版旧版4','DrivingTestC4',2,'/data/msg_push/android_certification/drivingtestcf4_umeng_CA.p12','123456','/data/msg_push/android_certification/drivingtestcf4_umeng_client.p12','123456',2,2,'2015-05-29 07:30:53','2015-05-29 07:30:53'),(65,'考驾照旧版主板','DrivingTestKjz',1,'/data/msg_push/ios_certification/drivingtestold_aps_production.p12','eclicks0716',NULL,NULL,2,2,'2015-06-01 06:23:08','2015-06-01 06:23:08'),(66,'驾考教练端','DrivingCoach',2,'/data/msg_push/android_certification/driving_coach_umeng_CA.p12','123456','/data/msg_push/android_certification/driving_coach_umeng_client.p12','123456',2,2,'2015-07-06 03:04:00','2015-07-06 03:04:00'),(67,'驾考教练端','DrivingCoach',1,'/data/msg_push/ios_certification/driving_coach_apns.p12','eclicks0716',NULL,NULL,2,2,'2015-09-29 11:29:45','2015-09-29 11:29:45');
/*!40000 ALTER TABLE `app` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-12-23 12:31:38
