-- MySQL dump 10.13  Distrib 5.5.37, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: dahlen_jack_users
-- ------------------------------------------------------
-- Server version	5.5.37-0ubuntu0.12.04.1

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
-- Table structure for table `Users`
--

DROP TABLE IF EXISTS `Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Users` (
  `ID` int(7) unsigned NOT NULL AUTO_INCREMENT,
  `Username` varchar(15) NOT NULL,
  `Password` varchar(40) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Activated` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `Confirmation` char(40) NOT NULL,
  `RegDate` int(11) unsigned NOT NULL,
  `LastLogin` int(11) unsigned NOT NULL DEFAULT '0',
  `GroupID` int(2) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Users`
--

LOCK TABLES `Users` WRITE;
/*!40000 ALTER TABLE `Users` DISABLE KEYS */;
INSERT INTO `Users` VALUES (1,'elpadi','e3714b026ef90c07590a8d0fe48ac0359b63a212','padi_05@yahoo.com',1,'',1404104866,1404956100,1),(2,'dahlen','7d0052d8c9bc03ea87ac8810764700bda084bea2','dah@thejackmag.com',1,'',1404297042,1404301993,1),(3,'guest','39667266abc81ea35f942e8b722082a051c43d26','guest@thejackmag.com',1,'',1404954719,0,1);
/*!40000 ALTER TABLE `Users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rbac_permissions`
--

DROP TABLE IF EXISTS `rbac_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rbac_permissions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Lft` int(11) NOT NULL,
  `Rght` int(11) NOT NULL,
  `Title` char(64) COLLATE utf8_bin NOT NULL,
  `Description` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `Title` (`Title`),
  KEY `Lft` (`Lft`),
  KEY `Rght` (`Rght`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rbac_permissions`
--

LOCK TABLES `rbac_permissions` WRITE;
/*!40000 ALTER TABLE `rbac_permissions` DISABLE KEYS */;
INSERT INTO `rbac_permissions` VALUES (1,0,13,'root','root'),(2,1,8,'access admin','Can access administration pages'),(3,2,3,'edit acl','Can create and edit roles and permissions'),(4,4,5,'edit users','Can create and edit users'),(5,6,7,'edit issues','Can create and edit magazine issues'),(6,9,10,'access content','Can access all the frontend pages'),(7,11,12,'access analytics','Can access all analytic pages');
/*!40000 ALTER TABLE `rbac_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rbac_rolepermissions`
--

DROP TABLE IF EXISTS `rbac_rolepermissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rbac_rolepermissions` (
  `RoleID` int(11) NOT NULL,
  `PermissionID` int(11) NOT NULL,
  `AssignmentDate` int(11) NOT NULL,
  PRIMARY KEY (`RoleID`,`PermissionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rbac_rolepermissions`
--

LOCK TABLES `rbac_rolepermissions` WRITE;
/*!40000 ALTER TABLE `rbac_rolepermissions` DISABLE KEYS */;
INSERT INTO `rbac_rolepermissions` VALUES (1,1,1404088517),(2,2,1404189844),(2,3,1404189844),(2,4,1404189844),(2,5,1404189844),(2,6,1404189844),(2,7,1404189844),(3,6,1404189542),(4,4,1404189831),(4,5,1404189831),(4,6,1404189831),(4,7,1404189831),(5,6,1404956123);
/*!40000 ALTER TABLE `rbac_rolepermissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rbac_roles`
--

DROP TABLE IF EXISTS `rbac_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rbac_roles` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Lft` int(11) NOT NULL,
  `Rght` int(11) NOT NULL,
  `Title` varchar(128) COLLATE utf8_bin NOT NULL,
  `Description` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `Title` (`Title`),
  KEY `Lft` (`Lft`),
  KEY `Rght` (`Rght`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rbac_roles`
--

LOCK TABLES `rbac_roles` WRITE;
/*!40000 ALTER TABLE `rbac_roles` DISABLE KEYS */;
INSERT INTO `rbac_roles` VALUES (1,0,9,'root','root'),(2,1,4,'administrator','User has full access to all administration pages.'),(3,5,8,'invited guest','User can access magazine content'),(4,2,3,'editor','User has partial access to the administration pages'),(5,6,7,'guest','Unidentified user');
/*!40000 ALTER TABLE `rbac_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rbac_userroles`
--

DROP TABLE IF EXISTS `rbac_userroles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rbac_userroles` (
  `UserID` int(11) NOT NULL,
  `RoleID` int(11) NOT NULL,
  `AssignmentDate` int(11) NOT NULL,
  PRIMARY KEY (`UserID`,`RoleID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rbac_userroles`
--

LOCK TABLES `rbac_userroles` WRITE;
/*!40000 ALTER TABLE `rbac_userroles` DISABLE KEYS */;
INSERT INTO `rbac_userroles` VALUES (1,1,1404088517),(1,2,1404104866),(2,4,1404297042),(3,5,1404954719);
/*!40000 ALTER TABLE `rbac_userroles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_data`
--

DROP TABLE IF EXISTS `user_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_data` (
  `user_id` int(10) unsigned NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `company` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `company` (`company`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_data`
--

LOCK TABLES `user_data` WRITE;
/*!40000 ALTER TABLE `user_data` DISABLE KEYS */;
INSERT INTO `user_data` VALUES (1,'Carlos Padilla','The Jack Mag','Administrator'),(2,'Dah Wee','The Jack Mag','Creative Director'),(3,'Guest','JACK','Guest');
/*!40000 ALTER TABLE `user_data` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-07-09 23:19:15
