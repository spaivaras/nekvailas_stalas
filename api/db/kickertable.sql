-- MySQL dump 10.13  Distrib 5.5.35, for debian-linux-gnu (x86_64)
--
-- Host: mysql.ox.nfq.lt    Database: wonderwall
-- ------------------------------------------------------
-- Server version	5.5.35-0+wheezy1

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
-- Table structure for table `kickertable_event`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kickertable_event` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `timeSec` varchar(10) NOT NULL,
  `usec` varchar(6) NOT NULL,
  `type` varchar(16) NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kickertable_user`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kickertable_user` (
  `userId` int(10) NOT NULL,
  `firstName` varchar(20) NOT NULL DEFAULT '',
  `lastName` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kickertable_user`
--

LOCK TABLES `kickertable_user` WRITE;
/*!40000 ALTER TABLE `kickertable_user` DISABLE KEYS */;
INSERT INTO `kickertable_user` VALUES (-1,'Svečias', '');
INSERT INTO `kickertable_user` VALUES (0,'Neatpažintas', '');
/*!40000 ALTER TABLE `kickertable_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kickertable_user_card`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kickertable_user_card` (
  `cardId` int(10) NOT NULL AUTO_INCREMENT,
  `userId` int(10) NOT NULL,
  `cardNumber` int(10) unsigned NOT NULL DEFAULT '0',
  `cardValue` varchar(21) NOT NULL DEFAULT '',
  PRIMARY KEY (`cardId`),
  KEY `CardNumber` (`cardNumber`),
  KEY `UserId` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kickertable_user_card`
--

LOCK TABLES `kickertable_user_card` WRITE;
/*!40000 ALTER TABLE `kickertable_user_card` DISABLE KEYS */;
INSERT INTO `kickertable_user_card` VALUES (1,0,0,'');
/*!40000 ALTER TABLE `kickertable_user_card` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-05-20 13:33:10
