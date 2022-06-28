--
-- Table structure for table `wp_goc_attack_log`
--

DROP TABLE IF EXISTS `wp_goc_attack_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wp_goc_attack_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attacker` int(11) NOT NULL,
  `defender` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_goc_attack_log`
--

LOCK TABLES `wp_goc_attack_log` WRITE;
/*!40000 ALTER TABLE `wp_goc_attack_log` DISABLE KEYS */;
INSERT INTO `wp_goc_attack_log` VALUES (6,4,2,'2018-07-02 19:29:01'),(7,4,2,'2018-07-27 19:45:39'),(8,4,2,'2018-07-27 19:45:41'),(9,4,2,'2018-07-27 19:48:58'),(10,4,2,'2018-07-27 19:54:13'),(11,4,2,'2018-07-27 19:56:47'),(12,4,1,'2018-07-27 19:57:44'),(13,4,2,'2018-07-27 20:06:01'),(14,4,1,'2018-07-27 20:06:11'),(15,4,2,'2018-07-27 20:08:03'),(16,4,1,'2018-07-27 20:08:12'),(17,1,2,'2018-07-28 13:04:45'),(18,1,4,'2018-07-28 15:02:34'),(19,1,2,'2018-07-28 15:02:51'),(20,1,4,'2018-07-28 15:05:33'),(21,4,2,'2018-07-28 21:48:49'),(22,1,4,'2018-07-29 08:53:43'),(23,4,2,'2018-07-29 09:03:25'),(24,1,4,'2018-08-14 19:01:46'),(25,2,4,'2018-08-15 17:02:28'),(26,1,4,'2019-04-12 18:49:41'),(27,1,4,'2019-04-12 19:18:48'),(28,1,2,'2019-04-12 19:19:10'),(29,1,4,'2019-12-25 13:27:38'),(30,1,4,'2019-12-25 13:27:57'),(31,1,4,'2019-12-25 13:28:03'),(32,1,4,'2019-12-25 13:28:11'),(33,1,4,'2019-12-25 13:29:00'),(34,1,4,'2020-01-03 16:50:16'),(35,1,2,'2020-01-03 17:51:26'),(36,1,2,'2020-01-03 17:51:37'),(37,1,4,'2020-01-05 16:13:22'),(38,1,4,'2020-01-05 16:13:48'),(39,1,4,'2020-01-05 16:13:56'),(40,1,4,'2020-01-05 16:14:27'),(41,1,4,'2020-01-05 16:14:31'),(42,1,4,'2020-01-05 16:14:34'),(43,1,4,'2020-01-05 16:14:36'),(44,1,4,'2020-01-05 16:14:39'),(45,1,4,'2020-01-05 16:14:42'),(46,1,2,'2020-01-05 16:14:51');
/*!40000 ALTER TABLE `wp_goc_attack_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wp_goc_power`
--

DROP TABLE IF EXISTS `wp_goc_power`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wp_goc_power` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `attack` int(11) NOT NULL,
  `defence` int(11) NOT NULL,
  `shop_item_id` int(11) NOT NULL,
  `shop_item_amount` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wp_goc_power`
--

LOCK TABLES `wp_goc_power` WRITE;
/*!40000 ALTER TABLE `wp_goc_power` DISABLE KEYS */;
INSERT INTO `wp_goc_power` VALUES (2,2,400,250,24,2,'2018-07-09 19:30:22'),(3,2,400,250,24,3,'2018-07-09 19:38:49'),(4,2,400,250,24,10,'2018-07-09 19:41:25'),(5,2,20,50,33,20,'2018-07-09 19:47:14'),(6,2,20,50,33,100,'2018-07-11 21:25:35'),(7,1,20,50,33,1,'2018-07-23 19:23:34'),(8,1,20,50,33,10,'2018-07-27 18:31:52'),(9,1,400,250,24,500,'2018-07-27 18:33:00'),(10,4,400,250,24,28,'2018-07-27 18:34:11'),(11,4,400,250,24,22,'2018-07-27 19:32:23'),(12,4,20,50,33,120,'2018-07-27 19:32:33'),(13,4,2500,5000,43,5,'2018-07-27 20:00:48'),(14,1,20,50,33,1000,'2018-07-28 15:01:55'),(15,1,2500,5000,43,1,'2019-04-12 18:49:32'),(16,1,2500,5000,43,9,'2019-12-25 13:28:42');
/*!40000 ALTER TABLE `wp_goc_power` ENABLE KEYS */;
UNLOCK TABLES;
