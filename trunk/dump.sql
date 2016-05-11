-- MySQL dump 10.11
--
-- Host: localhost    Database: asterisk
-- ------------------------------------------------------
-- Server version	5.0.95

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
-- Table structure for table `bl`
--

DROP TABLE IF EXISTS `bl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bl` (
  `number` bigint(15) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY  (`number`),
  UNIQUE KEY `number` (`number`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cdr`
--

DROP TABLE IF EXISTS `cdr`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cdr` (
  `calldate` datetime NOT NULL default '0000-00-00 00:00:00',
  `clid` varchar(80) NOT NULL default '',
  `src` varchar(80) NOT NULL default '',
  `dst` varchar(80) NOT NULL default '',
  `dcontext` varchar(80) NOT NULL default '',
  `channel` varchar(80) NOT NULL default '',
  `dstchannel` varchar(80) NOT NULL default '',
  `lastapp` varchar(80) NOT NULL default '',
  `lastdata` varchar(80) NOT NULL default '',
  `duration` int(11) NOT NULL default '0',
  `billsec` int(11) NOT NULL default '0',
  `disposition` varchar(45) NOT NULL default '',
  `amaflags` int(11) NOT NULL default '0',
  `accountcode` varchar(20) NOT NULL default '',
  `userfield` varchar(255) NOT NULL default '',
  `uniqueid` varchar(32) NOT NULL default '',
  `linkedid` varchar(32) NOT NULL default '',
  `sequence` varchar(32) NOT NULL default '',
  `peeraccount` varchar(32) NOT NULL default '',
  KEY `calldate` (`calldate`),
  KEY `dst` (`dst`),
  KEY `accountcode` (`accountcode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `codes`
--

DROP TABLE IF EXISTS `codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `codes` (
  `code_id` bigint(20) unsigned NOT NULL auto_increment,
  `code_abcdef` smallint(3) NOT NULL,
  `code_from` int(11) NOT NULL,
  `code_to` int(11) NOT NULL,
  `code_volume` int(11) NOT NULL,
  `operator` varchar(400) NOT NULL,
  `region` varchar(400) NOT NULL,
  UNIQUE KEY `code_id` (`code_id`),
  KEY `code_abcdef` (`code_abcdef`)
) ENGINE=MyISAM AUTO_INCREMENT=253607 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `events`
--
DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `timestamp` datetime NOT NULL default '0000-00-00 00:00:00',
  `event` longtext,
    PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=131 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
--
-- Table structure for table `pbook`
--

DROP TABLE IF EXISTS `pbook`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pbook` (
  `id` int(11) NOT NULL auto_increment,
  `calld` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `bemerkung` text NOT NULL,
  `Diverses` int(1) NOT NULL default '0',
  KEY `id` (`id`),
  KEY `calld` (`calld`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pbook_directory`
--

DROP TABLE IF EXISTS `pbook_directory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pbook_directory` (
  `id` int(9) NOT NULL,
  `tel` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `strasse` varchar(50) NOT NULL,
  `ort` varchar(50) NOT NULL,
  KEY `tel` (`tel`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `extension` varchar(20) NOT NULL,
  `password` varchar(20) default NULL,
  `name` varchar(50) default NULL,
  `voicemail` varchar(50) default NULL,
  `ringtimer` int(3) default NULL,
  `noanswer` varchar(100) default NULL,
  `recording` varchar(50) default NULL,
  `outboundcid` varchar(50) default NULL,
  `directdid` varchar(50) default NULL,
  `didalert` varchar(50) default NULL,
  `faxexten` varchar(20) default NULL,
  `faxemail` varchar(50) default NULL,
  `answer` tinyint(1) default NULL,
  `wait` int(2) default NULL,
  `privacyman` tinyint(1) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

CREATE TABLE `masscall` (
  `id` int(11) NOT NULL auto_increment,
  `number` bigint(11) NOT NULL,
  `sound_file1` varchar(30) default NULL,
  `sound_file2` varchar(30) default NULL,
  `pitch_var` int(10) default NULL,
  `end_describe` varchar(120) default NULL,
  `wav` varchar(120) default NULL,
  `billsec` int(3) default NULL,
  `time_describe` datetime default NULL,
  `ready` int(2) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `number` (`number`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-07-31 16:28:00
