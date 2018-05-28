-- MySQL dump 10.13  Distrib 5.7.21, for Linux (x86_64)
--
-- Host: localhost    Database: apex
-- ------------------------------------------------------
-- Server version	5.7.21-0ubuntu0.16.04.1

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
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `require_2fa` tinyint(1) NOT NULL DEFAULT '0',
  `invalid_logins` int(11) NOT NULL DEFAULT '0',
  `last_seen` int(11) NOT NULL DEFAULT '0',
  `sec_hash` varchar(130) NOT NULL DEFAULT '',
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `username` varchar(255) NOT NULL,
  `password` varchar(130) NOT NULL DEFAULT '',
  `full_name` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin`
--

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
INSERT INTO `admin` VALUES (11,'active',0,1,0,'97da2007f3ead80bda35af072313260b5f4b0fefbde728aeb2e697015a6416a94b6e9193bd4365726006744ea5064ffb34c2d40d4bc5bf5c1e7ec79ca6e3c3b8','2017-06-27 13:17:41','envrin','7662c74e3f49650158def74dc9aee35492d7f8e002caca267c671bae60433a6ba27ad94dc1c773ef49b0697639ed943b6fadfb5c48433ab718a51b0075d9dbb2','Envrin','matt@envrin.com');
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_allowips`
--

DROP TABLE IF EXISTS `admin_allowips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_allowips` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `ip_address` varchar(40) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  CONSTRAINT `admin_allowips_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `admin` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_allowips`
--

LOCK TABLES `admin_allowips` WRITE;
/*!40000 ALTER TABLE `admin_allowips` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_allowips` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_auth_sessions`
--

DROP TABLE IF EXISTS `admin_auth_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_auth_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `last_active` int(11) NOT NULL DEFAULT '0',
  `login_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `logout_date` datetime DEFAULT NULL,
  `auth_hash` varchar(130) NOT NULL,
  `2fa_status` tinyint(1) NOT NULL DEFAULT '0',
  `2fa_hash` varchar(130) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `auth_hash` (`auth_hash`),
  KEY `userid` (`userid`),
  CONSTRAINT `admin_auth_sessions_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `admin` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_auth_sessions`
--

LOCK TABLES `admin_auth_sessions` WRITE;
/*!40000 ALTER TABLE `admin_auth_sessions` DISABLE KEYS */;
INSERT INTO `admin_auth_sessions` VALUES (20,11,1498571217,'2017-06-27 13:18:16',NULL,'26872e3714d51e8c507a35326b2b36addafdc51583f776fd0b75fe4ed5bad4cc66b6c711a0816710575d4aae269d63f445ca3d1ba52e4048e201d1e54204af3d',1,''),(30,11,1498642010,'2017-06-28 09:26:50',NULL,'1cb9d825829c2618a7dfc141437ddd4d7f2d44b10b90f870a6477305b83c89885a45f36eee2c76f8d4fc1596a4be6ba3901383a16c1a206dce9f057e1f0e0e2e',1,''),(32,11,1498907749,'2017-07-01 10:50:48',NULL,'5bc375b05195f89074d2cb1db39137fe6534496e815e9a761f42b3f56e768f353c20049db624b05859cf34fd669941b8f9f6d858a45541a4f31ff46fe402c7bf',1,''),(38,11,1499044065,'2017-07-02 23:32:03',NULL,'da7a4d6e814646c1353cd03993cfa0d8b2c3d2433c8d37ca799e05b8a026908e114c113c064b1964e888f1ce8b3816c4ab9d7a737f06a4cbe891c31cdc28e64a',1,''),(41,11,1499083639,'2017-07-03 10:34:48',NULL,'afad93ca501dcfb2e06c3c00e3129fb877bcb6df8e2232e2a35f9bd5db5556d997016bf95dc2e461fa7802d8d8982d424eb0666c3c06d7e3911b4495e6db5eff',1,''),(47,11,1499310324,'2017-07-06 01:31:51',NULL,'408d2dfc8770926d25f5bdee22a7c5049bf05f972ba1ce640f4593ccc4e02ad625b89003328440d94ecd78d06b6d8628908aa056959cb0539ccc78465a379ffc',1,''),(49,11,1499358327,'2017-07-06 16:25:00',NULL,'305a581987fabb3ee9698774b535090ec8db89551535291fa02da721d8f5c06be5e15572d17608e66696e908a27196de7ad4a9590220e67dbf535a08596f26d9',1,''),(50,11,1499366599,'2017-07-06 18:22:09',NULL,'93e5e202f3439ff71a9a6f9d8012ba29ae8f7f49c7448dcb03ceee584f7a6712134b4f5f491e0d1e36508f4a53af6cdac1ee15bdb61b82c50cd6a8e8b0299f66',1,''),(53,11,1499503916,'2017-07-08 07:49:49',NULL,'680c4dc2c2876e1f472913a198cbe44980e10c7d8f949ba3f97506033e83e8e7bfa8b49137bffa4a71c057549b2f61009ca2db31c07975b23813e4f349828be5',1,''),(56,11,1499611241,'2017-07-09 14:40:14',NULL,'a2a70ada38c8de3ed5f797f93529fa583635671db643b32d34ff1150c50cb79c3496d438a80023887b67124fd8a3339d267d2e235cf15eae2c6fc342845d67cf',1,''),(57,11,1500443410,'2017-07-19 05:49:05',NULL,'9f623f01bc0204af7eb73fe3c971f4716a42e0b8f003f2d92d60789ba513ff190dfc835ab94f671a0192d455c82b8c1a74f7b87804dd5df716593411a7b4a9cd',1,''),(58,11,1500680295,'2017-07-21 22:48:08',NULL,'a9d2c60f6f7d3d69585b002f7e0cde9429a68c8169c435e32bbe5e5069dbcd20596c1072b4244b68d72600a5a1663428ddc442cb026723a55a03453a43c6002f',1,''),(59,11,1501671473,'2017-08-02 10:12:55',NULL,'b35c8bf5c94ea93f55c6bb46cff02e8252893e54310bfb06c34e1fffca4b15112e4033029c5ca77bf6cfea1a04a6f0323bb4aae34dac556e719a28aac0ec38a7',1,''),(60,11,1501839078,'2017-08-04 08:23:19',NULL,'95bac8e5d5c81eda8721f8d543ea077d47f1869fba627c91187e89a2fe10df3ea92fcf7e1e1dd250d65811fdfddaabe433ee91ab0008e57b92b776d06209a4f5',1,''),(61,11,1502021995,'2017-08-06 12:19:47',NULL,'2270a6b47ea3c978856ce58e71ebc01102d3cb87343d8da6275d0b619d62a53ad982f2b98ce721f09ad86cbc124cfc98c3aadb2e56d0ab066e15fcb8c13447ae',1,''),(62,11,1502104823,'2017-08-07 11:10:36',NULL,'1dc8fed1e061fe9388828fc056e9c4dfd73383481757d9f9ead9b8b5b977475a175f3088d661dee4c1680b3c0b3aa439719b4b404171a0067f6cc2ca5c08ea51',1,''),(63,11,1502182881,'2017-08-08 06:43:55',NULL,'45ac8fcc28e986ca35b52d26a37b0146771eebee8a9b11788e63078a6bd1611c0396a33ae95d3551bba964c3d804f16cce22a4254ebaf3372537d716436c97d7',1,''),(67,11,1502263128,'2017-08-09 05:44:16',NULL,'22c83a4a14ed4090f08b99d1d2cd7bb5e137bd13e0db54be26fdf64cfbeda6833422aa03b5b74f4537278407c33aa7d8d6dc732fb8318128d4181647e7f931ef',1,''),(69,11,1502270631,'2017-08-09 09:15:25',NULL,'7a8926c2533dd69e015d0d8dc4a2cb31328333af3a92b6bcc12b89a56f3c63f39d589e9760f90e295b29f19a22dd8df2f74dc791572e9743a706c1035ab59e40',1,''),(70,11,1502874423,'2017-08-16 07:57:13',NULL,'0e13551544449ba1606d0e78f020ba85f1c9f40bbbca70e4cf1bec98d71d627c6174e1680512655618f91535c40fcfaef56c99bc54e1c2ac6ff4a26084d3c23f',1,''),(71,11,1502878110,'2017-08-16 09:19:31',NULL,'5e3c50ec1d9429c555375373b4d0f130296cbf9f4f104bb001da66b6289ec06de680da7d048c5d6361faf95c89ea7aa71de5ed0ec4c318e25a3132400656fda0',1,''),(77,11,1502983833,'2017-08-17 13:54:46',NULL,'2a35fb57adadfa7f05c6ceae6bbe01ed6d628b2c999f31c76ce5abcb26ec14f8cbb9c6160372268ed31d2934e10977543a47e7d45b579c921f53581adb516d0c',1,''),(78,11,1502995374,'2017-08-17 18:41:22',NULL,'a3a6a95c936f1cbb8db035f7a2aba6ab4a29defd24049e6ce8dfe3cbe1859a8d4d552c3d5161e5fbf8b353186127012642e1c14b535f5f3662494dd1fa37b6b2',1,''),(79,11,1503010168,'2017-08-17 22:12:24',NULL,'7722a58f87d258b10991203178e3369be2e09746a0df2b76148d83f77a2f132b7f26b346720b973a3fb6de17c48893d7b4d37e2bedaa00529b2d84cd8f7df36e',1,''),(84,11,1503147731,'2017-08-19 09:50:14',NULL,'ad82fd4b26218487d55c78d154f3865e18bc2d34809eab18ef34ac660ba24cf18e4c5ec4fa3852c5d16bbf28bdb704b71cfefd4ed9c61d8bc0797751cfad06ee',1,''),(87,11,1503255369,'2017-08-20 17:51:56',NULL,'72251818bb2447c63f87973368846149abbf5e1fb975047d645ffee883a899b16e54c4fe7c6353750183ac075cbdc19bed1ac7d6a5a9e1ad8d8a7c7ef1dbc129',1,''),(88,11,1503415962,'2017-08-22 13:05:37',NULL,'7a1671169b7a4a179e255e460934091963433ebac324fe145dec3543e5c8a9ffef2bb9365afad1895df4e47d927f454071afbab687ed99fe1cde2913b03f2a3b',1,''),(91,11,1503820748,'2017-08-27 06:10:59',NULL,'55e886ba31a7528c5b69523fe477c61a12b5fbc07e835e9fde3a7abb85f7e4251a8710dddfacc7064e92bf5c7c42ce80e4ed25b0ee1da4e10ca6049e263632ac',1,''),(93,11,1504106191,'2017-08-30 15:16:31',NULL,'0e146cef2afa0fd4104a1b5599e70004e27ca2e72cdd4afb814a9f11282e8f821d7e2363afa76aea8ffb10b62983dbd0b5ae9ce4114447903e3f9ac3994c620c',1,''),(94,11,1504235848,'2017-09-01 03:14:57',NULL,'458c4420f2cd969e7fd4f7cc1f3c7e1e98da05af6b2b12bd01687abe7f8e8055ed24f6d4a365c26df482442e2f6d9b0598f7fdca46aae6b7cedc71d9c1ae8f87',1,''),(95,11,1508298667,'2017-10-18 02:36:11',NULL,'84a12216d803c21bade640a9ab8f6ad2269a368a74d3338c191ef4dfa5978b2956292f6703f1fa11ff55229db39d811f176d31c33a94ac14e136ee11f9608fe5',1,''),(96,11,1509917114,'2017-11-05 20:51:56',NULL,'71cfb57eec43a472edad922d60086b603928c64bd148d9fd5da50f521919c237defc095e67994cebb1fba6df4133996e3a28a612c2236cb3704b4a0673a79c51',1,''),(97,11,1509944844,'2017-11-06 05:07:24',NULL,'2d8d292dd93896e1527e9b694cc1ae34bf0aa03a7b537d55c98bfdc5e9afd17e197f55d325c15327b791d29404dca640ed39dca245951b50c3c5984762015953',1,''),(98,11,1519513176,'2018-02-24 22:59:36',NULL,'4e989d9ee77dd658bce228fa2dffed5cbcd39b8b584ffe6d8a21d1e52a250e4af6cc29a1f704dad6f7d007fd665e9cdde60ddfa920f4bf2820879dcf1c9613aa',1,''),(104,11,1520059662,'2018-03-03 05:16:21',NULL,'c05efdbfbcf572b1e59fc938ca0e97e7b8eea4ae39f111c8717ad52049321695b65db4d71e31871369b17358f95e1c0ff2140a82ddd17cc5289b183aff9726d4',1,''),(105,11,1522403344,'2018-03-30 09:49:04',NULL,'09da73ace3ebebf42a2e6d59b1a41ad750b91920b487a670df5c09b669044964f6ecdaeb62aff9f8aa6f5bb3c0fd664add78c081c7ee7ac28169a6ab943a7425',1,'');
/*!40000 ALTER TABLE `admin_auth_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_security_questions`
--

DROP TABLE IF EXISTS `admin_security_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_security_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `question` varchar(5) NOT NULL,
  `answer` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  CONSTRAINT `admin_security_questions_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `admin` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_security_questions`
--

LOCK TABLES `admin_security_questions` WRITE;
/*!40000 ALTER TABLE `admin_security_questions` DISABLE KEYS */;
INSERT INTO `admin_security_questions` VALUES (4,11,'q1','d526d239f7bdb51b49beb6b1cf31fe89a6704554a5504514ab8c8e9c2d60f114a8a33dbd5893e41ed96620a89d8d107ce5e69ba0eeecff1b64ba5d124f4e7bc3'),(5,11,'q1','d526d239f7bdb51b49beb6b1cf31fe89a6704554a5504514ab8c8e9c2d60f114a8a33dbd5893e41ed96620a89d8d107ce5e69ba0eeecff1b64ba5d124f4e7bc3'),(6,11,'q1','d526d239f7bdb51b49beb6b1cf31fe89a6704554a5504514ab8c8e9c2d60f114a8a33dbd5893e41ed96620a89d8d107ce5e69ba0eeecff1b64ba5d124f4e7bc3');
/*!40000 ALTER TABLE `admin_security_questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_menus`
--

DROP TABLE IF EXISTS `cms_menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `package` varchar(100) NOT NULL,
  `area` varchar(50) NOT NULL DEFAULT 'members',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_system` tinyint(1) NOT NULL DEFAULT '1',
  `require_login` tinyint(1) NOT NULL DEFAULT '0',
  `order_num` smallint(6) NOT NULL DEFAULT '0',
  `link_type` enum('internal','external','parent','header') NOT NULL DEFAULT 'internal',
  `icon` varchar(100) NOT NULL DEFAULT '',
  `parent` varchar(100) NOT NULL DEFAULT '',
  `alias` varchar(100) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=767 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_menus`
--

LOCK TABLES `cms_menus` WRITE;
/*!40000 ALTER TABLE `cms_menus` DISABLE KEYS */;
INSERT INTO `cms_menus` VALUES (740,'core','public',1,1,0,1,'internal','','','index','Index',''),(741,'core','public',1,1,0,2,'internal','','','about','About',''),(742,'core','public',1,1,0,3,'internal','','','services','Services',''),(743,'core','public',1,1,0,4,'internal','','','register','Register',''),(744,'core','public',1,1,0,5,'internal','','','login','Login',''),(745,'core','admin',1,1,0,1,'header','','','hdr_setup','Setup',''),(746,'core','admin',1,1,0,2,'internal','fa fa-fw fa-cog','','settings','Settings',''),(747,'core','admin',1,1,0,3,'internal','fa fa-fw fa-cog','settings','general','General',''),(748,'core','admin',1,1,0,3,'internal','fa fa-fw fa-cog','settings','admin','Administrators',''),(749,'core','admin',1,1,0,3,'internal','fa fa-fw fa-cog','settings','notifications','Notifications',''),(750,'core','admin',1,1,0,3,'internal','fa fa-fw fa-wrench','','maintenance','Maintenance',''),(751,'core','admin',1,1,0,4,'internal','fa fa-fw fa-wrench','maintenance','package_manager','Package Manager',''),(752,'core','admin',1,1,0,4,'internal','fa fa-fw fa-wrench','maintenance','theme_manager','Theme Manager',''),(753,'core','admin',1,1,0,4,'internal','fa fa-fw fa-wrench','maintenance','backup_manager','Backup Manager',''),(754,'core','admin',1,1,0,4,'internal','fa fa-fw fa-wrench','maintenance','cron_manager','Cron Manager',''),(755,'core','admin',1,1,0,4,'internal','fa fa-fw fa-wrench','maintenance','log_manager','Log Manager',''),(756,'core','admin',1,1,0,4,'internal','fa fa-fw fa-wrench','maintenance','system_check','System Check',''),(757,'devkit','admin',1,1,0,6,'header','','','hdr_devkit','Development',''),(758,'devkit','admin',1,1,0,7,'parent','fa fa-fw fa-hash','','devkit','Devel Kit',''),(759,'devkit','admin',1,1,0,6,'internal','fa fa-fw fa-hash','devkit','packages','Packages',''),(760,'devkit','admin',1,1,0,6,'internal','fa fa-fw fa-hash','devkit','themes','Themes',''),(761,'devkit','admin',1,1,0,6,'internal','fa fa-fw fa-hash','devkit','repository','Repository',''),(762,'users','admin',1,1,0,4,'header','','','hdr_accounts','Accounts',''),(763,'users','admin',1,1,0,5,'parent','fa fa-fw fa-users','','users','Users',''),(764,'users','admin',1,1,0,8,'internal','fa fa-fw fa-users','users','create','Create New User',''),(765,'users','admin',1,1,0,8,'internal','fa fa-fw fa-users','users','manage','Manage User',''),(766,'users','admin',1,1,0,8,'internal','fa fa-fw fa-users','users','delete','Delete User','');
/*!40000 ALTER TABLE `cms_menus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_pages`
--

DROP TABLE IF EXISTS `cms_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `area` varchar(100) NOT NULL DEFAULT 'public',
  `layout` varchar(255) NOT NULL DEFAULT 'default',
  `title` varchar(255) NOT NULL DEFAULT '',
  `filename` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_pages`
--

LOCK TABLES `cms_pages` WRITE;
/*!40000 ALTER TABLE `cms_pages` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `images`
--

DROP TABLE IF EXISTS `images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL,
  `record_id` int(11) NOT NULL DEFAULT '0',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `size` enum('TINY','small','medium','large','full') NOT NULL DEFAULT 'full',
  `width` int(11) NOT NULL DEFAULT '0',
  `height` int(11) NOT NULL DEFAULT '0',
  `mime_type` varchar(50) NOT NULL DEFAULT 'image/jpg',
  `filename` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `images`
--

LOCK TABLES `images` WRITE;
/*!40000 ALTER TABLE `images` DISABLE KEYS */;
/*!40000 ALTER TABLE `images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `images_contents`
--

DROP TABLE IF EXISTS `images_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `images_contents` (
  `id` int(11) NOT NULL,
  `contents` longtext NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `images_contents_ibfk_1` FOREIGN KEY (`id`) REFERENCES `images` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `images_contents`
--

LOCK TABLES `images_contents` WRITE;
/*!40000 ALTER TABLE `images_contents` DISABLE KEYS */;
/*!40000 ALTER TABLE `images_contents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `internal_components`
--

DROP TABLE IF EXISTS `internal_components`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `internal_components` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_num` smallint(6) NOT NULL DEFAULT '0',
  `type` varchar(15) NOT NULL,
  `package` varchar(100) NOT NULL,
  `parent` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `package` (`package`),
  CONSTRAINT `internal_components_ibfk_1` FOREIGN KEY (`package`) REFERENCES `internal_packages` (`alias`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=238 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `internal_components`
--

LOCK TABLES `internal_components` WRITE;
/*!40000 ALTER TABLE `internal_components` DISABLE KEYS */;
INSERT INTO `internal_components` VALUES (111,0,'config','core','','theme_admin','admin_default'),(112,0,'config','core','','theme_public','public_default'),(113,0,'config','core','','site_name','My Company Name'),(114,0,'config','core','','session_expire_mins_admin','60'),(115,0,'config','core','','session_expire_mins_user','60'),(116,0,'config','core','','session_retain_logs_admin',''),(117,0,'config','core','','session_retain_logs_user',''),(118,0,'config','core','','password_retries_allowed_admin','5'),(119,0,'config','core','','require_2fa_admin','0'),(120,0,'config','core','','num_security_questions_admin','3'),(121,0,'hash','core','','secondary_security_questions',''),(122,1,'hash_var','core','secondary_security_questions','q1','What was your childhood nickname?'),(123,2,'hash_var','core','secondary_security_questions','q2','In what city did you meet your spouse/significant other?'),(124,3,'hash_var','core','secondary_security_questions','q3','What is the name of your favorite childhood friend?'),(125,4,'hash_var','core','secondary_security_questions','q4','What street did you live on in third grade?'),(126,5,'hash_var','core','secondary_security_questions','q5','What is your oldest sibling?s birthday month and year? (e.g., January 1900)'),(127,6,'hash_var','core','secondary_security_questions','q6','What is the middle name of your oldest child?'),(128,7,'hash_var','core','secondary_security_questions','q7','What is your oldest siblings middle name?'),(129,8,'hash_var','core','secondary_security_questions','q8','What school did you attend for sixth grade?'),(130,9,'hash_var','core','secondary_security_questions','q9','What was your childhood phone number including area code? (e.g., 000-000-0000)'),(131,10,'hash_var','core','secondary_security_questions','q10','What is your oldest cousins first and last name?'),(132,11,'hash_var','core','secondary_security_questions','q11','What was the name of your first stuffed animal?'),(133,12,'hash_var','core','secondary_security_questions','q12','In what city or town did your mother and father meet?'),(134,13,'hash_var','core','secondary_security_questions','q13','Where were you when you had your first kiss?'),(135,14,'hash_var','core','secondary_security_questions','q14','What is the first name of the boy or girl that you first kissed?'),(136,15,'hash_var','core','secondary_security_questions','q15','What was the last name of your third grade teacher?'),(137,16,'hash_var','core','secondary_security_questions','q16','In what city does your nearest sibling live?'),(138,17,'hash_var','core','secondary_security_questions','q17','What is your oldest brothers birthday month and year? (e.g., January 1900)'),(139,18,'hash_var','core','secondary_security_questions','q18','What is your maternal grandmothers maiden name?'),(140,19,'hash_var','core','secondary_security_questions','q19','In what city or town was your first job?'),(141,20,'hash_var','core','secondary_security_questions','q20','What is the name of the place your wedding reception was held?'),(142,21,'hash_var','core','secondary_security_questions','q21','What is the name of a college you applied to but didnt attend?'),(143,0,'hash','core','','notify_system_actions',''),(144,1,'hash_var','core','notify_system_actions','2fa_admin','2FA - Administrator'),(145,2,'hash_var','core','notify_system_actions','2fa_user','2FA - Member'),(146,0,'hash','core','','notification_content_type',''),(147,1,'hash_var','core','notification_content_type','text/plain','Plain Text'),(148,2,'hash_var','core','notification_content_type','text/html','HTML'),(203,0,'cron','core','','rit=======rotate_logs',''),(204,0,'form','core','','notification',''),(205,0,'form','core','','admin',''),(206,0,'htmlfunc','core','','display_form',''),(207,0,'htmlfunc','core','','notification_condition',''),(208,0,'htmlfunc','core','','display_table',''),(209,0,'lib','core','','notification',''),(210,0,'lib','core','','admin',''),(211,0,'table','core','','notifications',''),(212,0,'ajax','core','','search_table',''),(213,0,'controller','core','http_requests','admin',''),(214,0,'controller','core','http_requests','default',''),(215,0,'controller','core','notifications','system',''),(216,0,'ajax','core','','delete_rows',''),(217,0,'ajax','core','','navigate_table',''),(218,0,'ajax','core','','sort_table',''),(219,0,'table','core','','admin',''),(220,0,'config','devkit','','repo_enabled','0'),(221,0,'config','devkit','','repo_host',''),(222,0,'config','devkit','','repo_name',''),(223,0,'config','devkit','','repo_tagline',''),(224,0,'config','devkit','','repo_email',''),(225,0,'config','devkit','','repo_url',''),(229,0,'config','users','','username_column','username'),(230,0,'config','users','','require_2fa_user','2'),(231,0,'form','users','','users_register',''),(232,0,'config','core','','install_url',''),(233,0,'config','users','','users_default_group','1'),(234,0,'config','users','','users_enable_public_profiles','0'),(235,0,'config','users','','users_enable_avatar','0'),(236,0,'config','users','','users_enable_about_me','0'),(237,0,'lib','users','','user','');
/*!40000 ALTER TABLE `internal_components` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `internal_packages`
--

DROP TABLE IF EXISTS `internal_packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `internal_packages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `in_development` tinyint(1) NOT NULL DEFAULT '0',
  `repo_id` int(11) NOT NULL,
  `version` varchar(15) NOT NULL DEFAULT '0.0.0',
  `prev_version` varchar(15) NOT NULL DEFAULT '0.0.0',
  `date_installed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_modified` datetime DEFAULT NULL,
  `alias` varchar(100) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias` (`alias`),
  KEY `repo_id` (`repo_id`),
  CONSTRAINT `internal_packages_ibfk_1` FOREIGN KEY (`repo_id`) REFERENCES `internal_repos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `internal_packages`
--

LOCK TABLES `internal_packages` WRITE;
/*!40000 ALTER TABLE `internal_packages` DISABLE KEYS */;
INSERT INTO `internal_packages` VALUES (1,0,1,'1.0.0.0','0.0.0','2017-02-06 04:23:27',NULL,'core','Core Framework'),(2,0,1,'1.0.0.0','0.0.0','2017-08-02 08:35:20',NULL,'devkit','Development Kit'),(3,0,1,'1.0.0.0','0.0.0','2017-08-28 13:45:48',NULL,'users','User Management');
/*!40000 ALTER TABLE `internal_packages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `internal_repos`
--

DROP TABLE IF EXISTS `internal_repos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `internal_repos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `url` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `internal_repos`
--

LOCK TABLES `internal_repos` WRITE;
/*!40000 ALTER TABLE `internal_repos` DISABLE KEYS */;
INSERT INTO `internal_repos` VALUES (1,1,'2017-02-05 07:24:36','http://repo.envrin.com','Envrin Main Repository','The main, public repository for the Apex Framework.');
/*!40000 ALTER TABLE `internal_repos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `controller` varchar(100) NOT NULL,
  `sender` varchar(30) NOT NULL,
  `recipient` varchar(30) NOT NULL,
  `content_type` enum('text/plain','text/html') NOT NULL DEFAULT 'text/plain',
  `subject` varchar(255) NOT NULL,
  `contents` longtext NOT NULL,
  `condition_vars` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (2,1,'system','admin:11','user','text/plain','test 1234','',''),(3,1,'system','admin:11','user','text/plain','Matt Is Here','DQpKdXN0IGEgcXVpY2sgdGVzdC4uLg0KDQpNYXR0DQo=',''),(6,1,'system','admin:11','user','text/plain','Boxer is awesome','DQpzZGtqZmtsc2RqZmxrO2RzDQpzYWRnDQo=',''),(7,1,'system','admin:11','user','text/plain','Boxer is awesome','DQpzZGtqZmtsc2RqZmxrO2RzDQpzYWRnDQo=',''),(8,1,'system','admin:11','user','text/plain','Boxer is awesome','DQpzZGtqZmtsc2RqZmxrO2RzDQpzYWRnDQo=',''),(9,1,'system','admin:11','user','text/plain','Boxer is awesome','DQpzZGtqZmtsc2RqZmxrO2RzDQpzYWRnDQo=',''),(10,1,'system','admin:11','user','text/plain','Boxer is awesome','DQpzZGtqZmtsc2RqZmxrO2RzDQpzYWRnDQo=',''),(11,1,'system','admin:11','user','text/plain','Boxer is awesome','DQpzZGtqZmtsc2RqZmxrO2RzDQpzYWRnDQo=',''),(12,1,'system','admin:11','user','text/plain','Boxer is awesome','DQpzZGtqZmtsc2RqZmxrO2RzDQpzYWRnDQo=',''),(13,1,'system','admin:11','user','text/plain','','DQpzZGtqZmtsc2RqZmxrO2RzDQpzYWRnDQo=',''),(14,1,'system','admin:11','user','text/plain','Boxer is awesome','DQpzZGtqZmtsc2RqZmxrO2RzDQpzYWRnDQo=',''),(15,1,'system','admin:11','user','text/plain','','DQpzZGtqZmtsc2RqZmxrO2RzDQpzYWRnDQo=',''),(16,1,'system','admin:11','user','text/plain','','DQpzZGtqZmtsc2RqZmxrO2RzDQpzYWRnDQo=',''),(17,1,'system','admin:11','user','text/plain','Boxer is awesome','DQpzZGtqZmtsc2RqZmxrO2RzDQpzYWRnDQo=',''),(18,1,'system','admin:11','user','text/plain','Boxer is awesome','DQpzZGtqZmtsc2RqZmxrO2RzDQpzYWRnDQo=',''),(19,1,'system','admin:11','user','text/plain','Boxer is awesome','DQpzZGtqZmtsc2RqZmxrO2RzDQpzYWRnDQo=',''),(20,1,'system','admin:11','user','text/plain','Boxer is awesome','DQpzZGtqZmtsc2RqZmxrO2RzDQpzYWRnDQo=',''),(21,1,'system','admin:11','user','text/plain','','',''),(22,1,'system','admin:11','user','text/plain','','',''),(23,1,'system','admin:11','user','text/plain','','','');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications_attachments`
--

DROP TABLE IF EXISTS `notifications_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notification_id` int(11) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `contents` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `notification_id` (`notification_id`),
  CONSTRAINT `notifications_attachments_ibfk_1` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications_attachments`
--

LOCK TABLES `notifications_attachments` WRITE;
/*!40000 ALTER TABLE `notifications_attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications_attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test`
--

DROP TABLE IF EXISTS `test`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test` (
  `id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test`
--

LOCK TABLES `test` WRITE;
/*!40000 ALTER TABLE `test` DISABLE KEYS */;
/*!40000 ALTER TABLE `test` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(160) NOT NULL DEFAULT '',
  `group_id` smallint(6) NOT NULL,
  `status` enum('active','inactive','pending') NOT NULL DEFAULT 'active',
  `sponsor` int(11) NOT NULL DEFAULT '0',
  `verify_level` tinyint(1) NOT NULL DEFAULT '0',
  `require_2fa` tinyint(1) NOT NULL DEFAULT '0',
  `email_verified` tinyint(1) NOT NULL DEFAULT '0',
  `failed_logins` smallint(6) NOT NULL DEFAULT '0',
  `rating` int(11) NOT NULL DEFAULT '0',
  `rating_total` int(11) NOT NULL DEFAULT '0',
  `country` varchar(5) NOT NULL DEFAULT '',
  `phone_country` varchar(5) NOT NULL DEFAULT '',
  `phone_number` varchar(20) NOT NULL DEFAULT '',
  `reg_ip` varchar(60) NOT NULL DEFAULT '',
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'','b07c12ef26cad4f9b43ae81a33e2f3730f6a6f5631dfa66308a2dbfb1c44802ac6874677d741d019653388461a79be7cc373910fa29f0d193c30138f2d08d2a9',1,'active',0,0,0,0,0,0,0,'','','','','2018-03-29 17:10:00');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_groups`
--

DROP TABLE IF EXISTS `users_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_groups`
--

LOCK TABLES `users_groups` WRITE;
/*!40000 ALTER TABLE `users_groups` DISABLE KEYS */;
INSERT INTO `users_groups` VALUES (1,'Member');
/*!40000 ALTER TABLE `users_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_profile`
--

DROP TABLE IF EXISTS `users_profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_profile` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `about_me` text NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `users_profile_ibfk_1` FOREIGN KEY (`id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_profile`
--

LOCK TABLES `users_profile` WRITE;
/*!40000 ALTER TABLE `users_profile` DISABLE KEYS */;
INSERT INTO `users_profile` VALUES (1,'Matt','matt@envrin.com','');
/*!40000 ALTER TABLE `users_profile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_profile_fields`
--

DROP TABLE IF EXISTS `users_profile_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_profile_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `allow_duplicates` tinyint(1) NOT NULL DEFAULT '1',
  `order_num` smallint(6) NOT NULL,
  `form_field` enum('textbox','textarea','select','radio','checkbox') NOT NULL DEFAULT 'textbox',
  `alias` varchar(50) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `options` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias` (`alias`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_profile_fields`
--

LOCK TABLES `users_profile_fields` WRITE;
/*!40000 ALTER TABLE `users_profile_fields` DISABLE KEYS */;
INSERT INTO `users_profile_fields` VALUES (2,1,1,1,'textbox','full_name','Full Name',''),(3,1,1,2,'textbox','email','E-Mail Address','');
/*!40000 ALTER TABLE `users_profile_fields` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-05-27 17:37:33
