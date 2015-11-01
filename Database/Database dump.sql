-- MySQL dump 10.13  Distrib 5.6.24, for Win64 (x86_64)
--
-- Host: localhost    Database: dickaj1_olveston
-- ------------------------------------------------------
-- Server version	5.6.26-log

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
-- Dumping data for table `tbl_frame`
--

LOCK TABLES `tbl_frame` WRITE;
/*!40000 ALTER TABLE `tbl_frame` DISABLE KEYS */;
INSERT INTO `tbl_frame` VALUES (1,1,1,'billiards01.png'),(4,1,2,'billiards02.png'),(6,2,1,'kitchen01.png'),(7,2,2,'kitchen02.png'),(8,2,3,'kitchen03.png'),(9,2,4,'kitchen04.png'),(10,2,5,'kitchen05.png'),(11,2,6,'kitchen06.png'),(12,2,7,'kitchen07.png'),(13,2,8,'kitchen08.png'),(14,1,3,'billiards03.png');
/*!40000 ALTER TABLE `tbl_frame` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `tbl_hotspot`
--

LOCK TABLES `tbl_hotspot` WRITE;
/*!40000 ALTER TABLE `tbl_hotspot` DISABLE KEYS */;
INSERT INTO `tbl_hotspot` VALUES (1,'234,0,356,371',6,1),(2,'922,343,955,401',6,2),(3,'339, 228, 364, 254',1,7),(4,'632, 39, 693, 128',1,8),(5,'548, 213, 548, 324, 599, 341, 635, 312, 630, 236, 598, 227, 591, 203',4,9),(6,'346, 244, 348, 398, 479, 399, 475, 285, 440, 267, 437, 235',4,9),(7,'83, 174, 12',14,10);
/*!40000 ALTER TABLE `tbl_hotspot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `tbl_item`
--

LOCK TABLES `tbl_item` WRITE;
/*!40000 ALTER TABLE `tbl_item` DISABLE KEYS */;
INSERT INTO `tbl_item` VALUES (1,'Samuels apron','Its red and has a frog on it, I was bored and added googly eyes','apron.jpg',NULL),(2,'Light switch','Its a lightswitch yo','switch.jpg',NULL),(7,'Majong','A majong set',NULL,'5774'),(8,'Picture','Large picture',NULL,'1'),(9,'Chair','A fancy chair',NULL,'5825'),(10,'Urn','Large urn',NULL,'5758');
/*!40000 ALTER TABLE `tbl_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `tbl_room`
--

LOCK TABLES `tbl_room` WRITE;
/*!40000 ALTER TABLE `tbl_room` DISABLE KEYS */;
INSERT INTO `tbl_room` VALUES (1,'Billiards Room','The Billiards Room','billiards00.png'),(2,'Arrons Kitchen','Yup its a mess','kitchen00.png');
/*!40000 ALTER TABLE `tbl_room` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-10-14 21:32:20
