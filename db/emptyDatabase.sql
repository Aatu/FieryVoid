-- MySQL dump 10.13  Distrib 5.7.20, for Linux (x86_64)
--
-- Host: localhost    Database: B5CGM
-- ------------------------------------------------------
-- Server version	5.7.20-0ubuntu0.16.04.1

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
DROP DATABASE IF EXISTS B5CGM;
CREATE DATABASE B5CGM;

CREATE USER 'aatu'@'localhost' IDENTIFIED BY 'Kiiski';
GRANT ALL PRIVILEGES ON B5CGM.* To 'aatu'@'localhost' IDENTIFIED BY 'Kiiski';
USE B5CGM;
--
-- Table structure for table `chat`
--

DROP TABLE IF EXISTS `chat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `username` varchar(200) NOT NULL,
  `gameid` int(11) DEFAULT '0',
  `time` datetime NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `gameid_id` (`gameid`,`id`),
  KEY `time` (`time`)
) ENGINE=InnoDB AUTO_INCREMENT=39191 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fx_helpmessages`
--

DROP TABLE IF EXISTS `fx_helpmessages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fx_helpmessages` (
  `messageid` int(11) NOT NULL AUTO_INCREMENT,
  `message` text NOT NULL,
  `HelpLocation` varchar(200) NOT NULL,
  `HelpImage` varchar(200) NOT NULL,
  `nextpageid` int(11) NOT NULL,
  PRIMARY KEY (`messageid`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `player`
--

DROP TABLE IF EXISTS `player`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `player` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(45) NOT NULL,
  `password` varchar(400) NOT NULL,
  `accesslevel` int(11) NOT NULL DEFAULT '1',
  `discord_id` varchar(32) DEFAULT NULL,
  `dm_channel_id` varchar(32) DEFAULT NULL,
  `discord_verify_id` varchar(32) DEFAULT NULL,
  `discord_verify_code` varchar(10) DEFAULT NULL,
  `discord_verify_expires` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_discord_id` (`discord_id`)
) ENGINE=MyISAM AUTO_INCREMENT=210 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Dumping data for table `player`
--

LOCK TABLES `player` WRITE;
/*!40000 ALTER TABLE `player` DISABLE KEYS */;
INSERT INTO `player` (`id`,`username`,`password`,`accesslevel`) VALUES (3,'player1','*2470C0C06DEE42FD1618BB99005ADCA2EC9D1E19',0),(4,'player2','*2470C0C06DEE42FD1618BB99005ADCA2EC9D1E19',0);
/*!40000 ALTER TABLE `player` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `player_chat`
--

DROP TABLE IF EXISTS `player_chat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `player_chat` (
  `playerid` int(11) NOT NULL,
  `gameid` int(11) NOT NULL,
  `last_checked` datetime DEFAULT NULL,
  PRIMARY KEY (`playerid`,`gameid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;



--
-- Table structure for table `tac_ammo`
--

DROP TABLE IF EXISTS `tac_ammo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tac_ammo` (
  `shipid` int(11) NOT NULL,
  `systemid` int(11) NOT NULL,
  `firingmode` int(11) NOT NULL,
  `gameid` int(11) NOT NULL,
  `ammo` int(11) NOT NULL,
  `turn` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`gameid`,`shipid`,`systemid`,`firingmode`,`turn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `tac_enhancements`
--

DROP TABLE IF EXISTS `tac_enhancements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tac_enhancements` (
  `gameid` int(11) NOT NULL,
  `shipid` int(11) NOT NULL,
  `enhid` varchar(10) NOT NULL,
  `numbertaken` int(11) NOT NULL,
  `enhname` text(50) NOT NULL,
  PRIMARY KEY (`gameid`,`shipid`,`enhid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `tac_sys_enhancements`
--
-- PER-SYSTEM enhancements (WEAPON_ENHANCEMENTS_PLAN.md §3.3, D1). Additive twin of
-- tac_enhancements, NOT a PK migration of it: six Twin Arrays each with Gunsights is six
-- rows, so `systemid` has to be in the key, and rebuilding the key of the table every
-- existing game reads on every load buys nothing.
-- `sysname` is deliberately NOT in the key - the id is the identity, the name is the
-- INTEGRITY CHECK (D13). System ids are pure constructor order, so a system inserted
-- mid-constructor shifts every id after it; a row whose name no longer matches is DROPPED
-- on load, never re-homed by searching for the name (plan §4.7.1).
-- `enhvalue` is the price PAID, stored so a refund is exact. ⚠️ mysqli returns DECIMAL as
-- a PHP STRING - cast on read (db/fractionalEnhancementValue.sql).
--

DROP TABLE IF EXISTS `tac_sys_enhancements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tac_sys_enhancements` (
  `gameid`      INT(11)       NOT NULL,
  `shipid`      INT(11)       NOT NULL,
  `systemid`    INT(11)       NOT NULL,
  `sysname`     VARCHAR(50)   NOT NULL,
  `enhid`       VARCHAR(10)   NOT NULL,
  `numbertaken` INT(11)       NOT NULL,
  `enhname`     VARCHAR(50)   NOT NULL,
  `enhvalue`    DECIMAL(10,2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`gameid`,`shipid`,`systemid`,`enhid`),
  KEY `idx_shipid` (`shipid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `tac_critical`
--

DROP TABLE IF EXISTS `tac_critical`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tac_critical` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gameid` int(11) NOT NULL,
  `shipid` int(11) NOT NULL,
  `systemid` int(11) NOT NULL,
  `type` varchar(100) NOT NULL,
  `turn` int(11) NOT NULL,
  `turnend` int(11) NOT NULL DEFAULT 0,
  `param` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gameid` (`gameid`),
  KEY `shipid` (`shipid`)
) ENGINE=InnoDB AUTO_INCREMENT=44475 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tac_damage`
--

DROP TABLE IF EXISTS `tac_damage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tac_damage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shipid` int(11) NOT NULL,
  `gameid` int(11) NOT NULL,
  `systemid` int(11) NOT NULL,
  `turn` int(11) NOT NULL,
  `damage` int(11) NOT NULL DEFAULT '0',
  `armour` int(11) DEFAULT '0',
  `shields` int(11) DEFAULT '0',
  `fireorderid` int(11) NOT NULL,
  `destroyed` tinyint(1) NOT NULL DEFAULT '0',
  `undestroyed` tinyint(1) NOT NULL DEFAULT '0',
  `pubnotes` text,
  `damageclass` tinytext,
  PRIMARY KEY (`id`),
  KEY `gameid` (`gameid`),
  KEY `shipid` (`shipid`)
) ENGINE=InnoDB AUTO_INCREMENT=412134 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tac_ew`
--

DROP TABLE IF EXISTS `tac_ew`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tac_ew` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gameid` int(11) NOT NULL,
  `shipid` int(11) NOT NULL,
  `turn` int(11) NOT NULL,
  `type` varchar(45) NOT NULL,
  `amount` int(11) NOT NULL,
  `targetid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `gameid` (`gameid`),
  KEY `shipid` (`shipid`)
) ENGINE=InnoDB AUTO_INCREMENT=309637 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tac_fireorder`
--

DROP TABLE IF EXISTS `tac_fireorder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tac_fireorder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(45) NOT NULL DEFAULT 'normal',
  `shooterid` int(11) NOT NULL DEFAULT '0',
  `targetid` int(11) NOT NULL DEFAULT '0',
  `weaponid` int(11) NOT NULL DEFAULT '0',
  `calledid` int(11) NOT NULL DEFAULT '0',
  `turn` int(11) NOT NULL DEFAULT '0',
  `firingmode` int(11) NOT NULL DEFAULT '0',
  `needed` int(11) NOT NULL DEFAULT '0',
  `rolled` int(11) NOT NULL DEFAULT '0',
  `gameid` int(11) NOT NULL,
  `notes` text NOT NULL,
  `shotshit` int(11) NOT NULL DEFAULT '0',
  `shots` int(11) NOT NULL DEFAULT '1',
  `pubnotes` text NOT NULL,
  `intercepted` int(11) NOT NULL DEFAULT '0',
  `x` varchar(10) NOT NULL DEFAULT 'null',
  `y` varchar(10) NOT NULL DEFAULT 'null',
  `damageclass` tinytext,  
  `resolutionorder` int(11) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`id`),
  KEY `gameid` (`gameid`),
  KEY `shooterid` (`shooterid`),
  KEY `weaponid` (`weaponid`)
) ENGINE=InnoDB AUTO_INCREMENT=490359 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tac_flightsize`
--

DROP TABLE IF EXISTS `tac_flightsize`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tac_flightsize` (
  `entry` int(11) NOT NULL AUTO_INCREMENT,
  `gameid` int(11) DEFAULT NULL,
  `shipid` int(11) DEFAULT NULL,
  `flightsize` int(11) DEFAULT NULL,
  PRIMARY KEY (`entry`)
) ENGINE=InnoDB AUTO_INCREMENT=2536 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tac_game`
--

DROP TABLE IF EXISTS `tac_game`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tac_game` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text,
  `turn` int(11) DEFAULT NULL,
  `phase` int(11) DEFAULT NULL,
  `activeship` varchar(4000) default '-1',
  `background` varchar(200) DEFAULT NULL,
  `points` int(6) DEFAULT '1000',
  `status` varchar(45) NOT NULL DEFAULT 'LOBBY',
  `slots` int(11) NOT NULL DEFAULT '2',
  `creator` int(11) DEFAULT NULL,
  `submitLock` datetime DEFAULT NULL,
  `gamespace` varchar(45) DEFAULT NULL,
  `rules` varchar(400) DEFAULT '{}',  
  `description` text ,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3670 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tac_iniative`
--

DROP TABLE IF EXISTS `tac_iniative`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tac_iniative` (
  `gameid` int(11) NOT NULL,
  `shipid` int(11) NOT NULL,
  `turn` int(11) NOT NULL,
  `iniative` int(11) DEFAULT NULL,
  `unmodified_iniative` int(11),
  PRIMARY KEY (`gameid`,`turn`,`shipid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `tac_playeringame`
--

DROP TABLE IF EXISTS `tac_playeringame`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tac_playeringame` (
  `gameid` int(11) NOT NULL,
  `slot` int(11) NOT NULL DEFAULT '0',
  `playerid` int(11) DEFAULT NULL,
  `teamid` int(11) DEFAULT '0',
  `lastturn` int(11) DEFAULT '0',
  `lastphase` int(11) DEFAULT '0',
  `lastactivity` datetime DEFAULT NULL,
  `submitLock` datetime DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL,
  `points` int(11) DEFAULT NULL,
  `depx` decimal(10,0) DEFAULT NULL,
  `depy` decimal(10,0) DEFAULT NULL,
  `deptype` varchar(45) DEFAULT NULL,
  `depwidth` int(11) DEFAULT NULL,
  `depheight` int(11) DEFAULT NULL,
  `depavailable` int(11) DEFAULT NULL,
  `waiting` boolean DEFAULT TRUE,
  `surrendered` int(11) DEFAULT NULL,  
  PRIMARY KEY (`gameid`,`slot`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `tac_power`
--

DROP TABLE IF EXISTS `tac_power`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tac_power` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shipid` int(11) NOT NULL,
  `gameid` int(11) NOT NULL,
  `systemid` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `turn` int(11) NOT NULL,
  `amount` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `gameid` (`gameid`),
  KEY `shipid` (`shipid`)
) ENGINE=InnoDB AUTO_INCREMENT=152404 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tac_ship`
--

DROP TABLE IF EXISTS `tac_ship`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tac_ship` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `playerid` int(11) NOT NULL,
  `tacgameid` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `phpclass` varchar(45) NOT NULL,
  `rolling` tinyint(1) NOT NULL DEFAULT '0',
  `rolled` tinyint(1) NOT NULL DEFAULT '0',
  `campaignX` int(11) DEFAULT NULL,
  `campaignY` int(11) DEFAULT NULL,
  `campaigngameid` int(11) DEFAULT NULL,
  `slot` int(11) NOT NULL DEFAULT '0',
  `enhvalue` decimal(10,2) NOT NULL DEFAULT '0.00',
  -- Reinforcements (db/reinforcements.sql, REINFORCEMENTS_PLAN.md 3.1): bought as a
  -- reinforcement / the turn it arrives (NULL = still in hyperspace) / the id of the
  -- opener unit whose jump point exit it is riding through (NULL = unassigned).
  `reinforcement` tinyint(1) NOT NULL DEFAULT '0',
  `arrivalturn` int(11) DEFAULT NULL,
  `arrivalvia` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gameid` (`tacgameid`)
) ENGINE=InnoDB AUTO_INCREMENT=28910 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tac_shipmovement`
--

DROP TABLE IF EXISTS `tac_shipmovement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tac_shipmovement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shipid` int(11) NOT NULL,
  `gameid` int(11) NOT NULL DEFAULT '0',
  `type` varchar(45) DEFAULT NULL,
  `x` int(11) DEFAULT NULL,
  `y` int(11) DEFAULT NULL,
  `xOffset` int(11) NOT NULL DEFAULT '0',
  `yOffset` int(11) NOT NULL DEFAULT '0',
  `speed` int(11) DEFAULT NULL,
  `heading` int(11) DEFAULT NULL,
  `facing` int(11) DEFAULT NULL,
  `preturn` int(11) DEFAULT NULL,
  `requiredthrust` text,
  `assignedthrust` text,
  `turn` int(11) NOT NULL DEFAULT '1',
  `value` varchar(100) NOT NULL DEFAULT '0',
  `at_initiative` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`,`shipid`,`gameid`),
  KEY `gameid` (`gameid`)
) ENGINE=InnoDB AUTO_INCREMENT=1336799 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `tac_individual_notes`
--

DROP TABLE IF EXISTS `tac_individual_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tac_individual_notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gameid` int(11) NOT NULL DEFAULT '0',
  `turn` int(11) NOT NULL DEFAULT '1',
  `phase` int(11) NOT NULL DEFAULT '1',
  `shipid` int(11) NOT NULL,
  `systemid` int(11) NOT NULL,  
  `notekey` varchar(40) DEFAULT '',
  `notekey_human` varchar(40) DEFAULT '',
  `notevalue` varchar(4096) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `gameid` (`gameid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `tac_systemdata`
--

DROP TABLE IF EXISTS `tac_systemdata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tac_systemdata` (
  `systemid` int(11) NOT NULL,
  `subsystem` varchar(45) NOT NULL,
  `gameid` int(11) NOT NULL,
  `shipid` int(11) NOT NULL,
  `data` text,
  `turn` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`systemid`,`subsystem`,`gameid`,`shipid`, `turn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `tac_saved_list`
--

DROP TABLE IF EXISTS `tac_saved_list`;

CREATE TABLE `tac_saved_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text DEFAULT NULL,
  `userid` int(11) DEFAULT NULL,
  `points` int(11) DEFAULT NULL,
  `isPublic` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `userid_key` (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


--
-- Table structure for table `tac_saved_ship`
--

DROP TABLE IF EXISTS `tac_saved_ship`;

CREATE TABLE `tac_saved_ship` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `userid` INT(11) NOT NULL,
  `listid` INT(11) NOT NULL,
  `name` VARCHAR(200) NOT NULL,
  `phpclass` VARCHAR(45) NOT NULL,
  `flightsize` INT(11) NOT NULL DEFAULT 0,
  `bulkbuy` INT(11) NOT NULL DEFAULT 1,
  -- DECIMAL, not INT: MINE_DMG is priced at 0.5/level, so a per-unit enhancement cost is
  -- not always a whole number. See db/fractionalEnhancementValue.sql.
  `enhvalue` DECIMAL(10,2) NOT NULL DEFAULT 0,
  -- Bought as a reinforcement, i.e. this unit waits in hyperspace (db/reinforcements.sql).
  -- The PURCHASE-TIME flag only: arrivalturn/arrivalvia are in-play state and live on
  -- tac_ship, never here.
  `reinforcement` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `listid_key` (`listid`),
  CONSTRAINT `fk_ship_list`
    FOREIGN KEY (`listid`) REFERENCES `tac_saved_list` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


--
-- Table structure for table `tac_saved_enh`
--

DROP TABLE IF EXISTS `tac_saved_enh`;

CREATE TABLE `tac_saved_enh` (
  `listid` INT(11) NOT NULL,
  `shipid` INT(11) NOT NULL,
  `enhid` VARCHAR(10) NOT NULL,
  `numbertaken` INT(11) NOT NULL,
  `enhname` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`listid`,`shipid`,`enhid`),
  KEY `idx_shipid` (`shipid`),
  KEY `idx_listid` (`listid`),
  CONSTRAINT `fk_enh_ship`
    FOREIGN KEY (`shipid`) REFERENCES `tac_saved_ship` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_enh_list`
    FOREIGN KEY (`listid`) REFERENCES `tac_saved_list` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


--
-- Table structure for table `tac_saved_sysenh`
--
-- PER-SYSTEM enhancements a saved fleet carries (WEAPON_ENHANCEMENTS_PLAN.md §3.3, D1).
-- Additive twin of tac_saved_enh with `systemid` in the key, and the same double FK, so
-- deleteSavedFleet keeps working untouched - these cascade away with the list.
-- `sysname` is the D13 integrity check, not part of the identity: a saved fleet outlives
-- the blueprint it was priced against, and a stored id that now names a differently-named
-- system is dropped rather than applied to whatever moved into its slot.
-- `enhvalue` is the price PAID; it is re-derived on load anyway (D4), but storing it keeps
-- a refund exact and makes a re-price visible.
--

DROP TABLE IF EXISTS `tac_saved_sysenh`;

CREATE TABLE `tac_saved_sysenh` (
  `listid`      INT(11)       NOT NULL,
  `shipid`      INT(11)       NOT NULL,
  `systemid`    INT(11)       NOT NULL,
  `sysname`     VARCHAR(50)   NOT NULL,
  `enhid`       VARCHAR(10)   NOT NULL,
  `numbertaken` INT(11)       NOT NULL,
  `enhname`     VARCHAR(255)  NOT NULL,
  `enhvalue`    DECIMAL(10,2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`listid`,`shipid`,`systemid`,`enhid`),
  KEY `idx_shipid` (`shipid`),
  KEY `idx_listid` (`listid`),
  CONSTRAINT `fk_sysenh_ship`
    FOREIGN KEY (`shipid`) REFERENCES `tac_saved_ship` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_sysenh_list`
    FOREIGN KEY (`listid`) REFERENCES `tac_saved_list` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


--
-- Table structure for table `tac_saved_ammo`
--

DROP TABLE IF EXISTS `tac_saved_ammo`;

CREATE TABLE `tac_saved_ammo` (
  `listid` INT(11) NOT NULL,
  `shipid` INT(11) NOT NULL,
  `systemid` INT(11) NOT NULL,
  `firingmode` INT(11) NOT NULL,
  `ammo` INT(11) NOT NULL,
  PRIMARY KEY (`listid`,`shipid`,`systemid`,`firingmode`),
  KEY `idx_shipid` (`shipid`),
  KEY `idx_listid` (`listid`),
  CONSTRAINT `fk_ammo_ship`
    FOREIGN KEY (`shipid`) REFERENCES `tac_saved_ship` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_ammo_list`
    FOREIGN KEY (`listid`) REFERENCES `tac_saved_list` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Table structure for table `tac_saved_damage`
--
-- Battle damage a saved fleet carries (PREBATTLE_DAMAGE_PLAN.md §4.1).
-- kind = 0 -> `ref` is a ShipSystem id; kind = 1 -> `ref` is a fighter ordinal
-- 1..flightSize (per-fighter system ids do not exist in the lobby, see plan §1.1);
-- kind = 2 -> `ref` is a MINE ordinal 1..bulkbuy (a bulk mine purchase is one lobby
-- object plus a count, exactly like a flight; the damage lands on that copy's Structure).
--

DROP TABLE IF EXISTS `tac_saved_damage`;

CREATE TABLE `tac_saved_damage` (
  `listid`    INT(11)    NOT NULL,
  `shipid`    INT(11)    NOT NULL,
  `kind`      TINYINT(1) NOT NULL DEFAULT 0,
  `ref`       INT(11)    NOT NULL,
  `damage`    INT(11)    NOT NULL DEFAULT 0,
  `destroyed` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`listid`,`shipid`,`kind`,`ref`),
  KEY `idx_shipid` (`shipid`),
  KEY `idx_listid` (`listid`),
  CONSTRAINT `fk_dmg_ship`
    FOREIGN KEY (`shipid`) REFERENCES `tac_saved_ship` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_dmg_list`
    FOREIGN KEY (`listid`) REFERENCES `tac_saved_list` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Table structure for table `tac_saved_crit`
--
-- Critical effects a saved fleet carries. Same key shape as tac_saved_damage.
-- `type` is a Critical subclass name and is validated on both write and read -
-- DBManager::getCriticalsForShips does `new $type(...)` on the stored string.
-- `param` carries the magnitude of a PARAM-CARRYING critical (DamageReductionReduced),
-- where one crit represents a whole reduction rather than one point. INT rather than
-- tac_critical's varchar(200): only integer params are ever allowed into a saved fleet
-- (PreBattleDamage::$paramCriticals is the allow-list).
--

DROP TABLE IF EXISTS `tac_saved_crit`;

CREATE TABLE `tac_saved_crit` (
  `listid`   INT(11)      NOT NULL,
  `shipid`   INT(11)      NOT NULL,
  `kind`     TINYINT(1)   NOT NULL DEFAULT 0,
  `ref`      INT(11)      NOT NULL,
  `type`     VARCHAR(100) NOT NULL,
  `amount`   INT(11)      NOT NULL DEFAULT 1,
  `param`    INT(11)               DEFAULT NULL,
  PRIMARY KEY (`listid`,`shipid`,`kind`,`ref`,`type`),
  KEY `idx_shipid` (`shipid`),
  KEY `idx_listid` (`listid`),
  CONSTRAINT `fk_crit_ship`
    FOREIGN KEY (`shipid`) REFERENCES `tac_saved_ship` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_crit_list`
    FOREIGN KEY (`listid`) REFERENCES `tac_saved_list` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Table structure for table `tac_ladder_games`
--

DROP TABLE IF EXISTS `tac_ladder_games`;

CREATE TABLE `tac_ladder_games` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `gameid` INT NOT NULL,
  `playerid` INT NOT NULL,
  `status` VARCHAR(20) NOT NULL,
  `recorded_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `tac_ladder_rankings`
--

DROP TABLE IF EXISTS `tac_ladder_rankings`;

CREATE TABLE `tac_ladder_rankings` (
  `playerid` INT NOT NULL,
  `rating` INT NOT NULL DEFAULT 100,
  PRIMARY KEY (`playerid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-01-11 15:13:05

