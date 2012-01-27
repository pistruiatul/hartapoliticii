-- phpMyAdmin SQL Dump
-- version 3.4.0
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 27, 2012 at 12:52 AM
-- Server version: 5.5.12
-- PHP Version: 5.3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `hartapoliticii_pistruiatul`
--

-- --------------------------------------------------------

--
-- Table structure for table `alegeritv`
--

DROP TABLE IF EXISTS `alegeritv`;
CREATE TABLE IF NOT EXISTS `alegeritv` (
  `iddep` int(11) NOT NULL,
  `idperson` int(11) NOT NULL,
  `urlatv` varchar(150) NOT NULL,
  `idatv` int(11) NOT NULL,
  KEY `iddep` (`iddep`),
  KEY `idperson` (`idperson`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `alegeri_2008_candidates`
--

DROP TABLE IF EXISTS `alegeri_2008_candidates`;
CREATE TABLE IF NOT EXISTS `alegeri_2008_candidates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `college_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `idperson` int(11) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `party` varchar(255) DEFAULT NULL,
  `name_cleaned` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `college_id` (`college_id`),
  KEY `name_cleaned` (`name_cleaned`),
  KEY `idperson` (`idperson`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=2130 ;

-- --------------------------------------------------------

--
-- Table structure for table `alegeri_2008_colleges`
--

DROP TABLE IF EXISTS `alegeri_2008_colleges`;
CREATE TABLE IF NOT EXISTS `alegeri_2008_colleges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=454 ;

-- --------------------------------------------------------

--
-- Table structure for table `away_times`
--

DROP TABLE IF EXISTS `away_times`;
CREATE TABLE IF NOT EXISTS `away_times` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `iddepsen` int(11) NOT NULL,
  `idperson` int(11) NOT NULL,
  `chamber` int(11) NOT NULL,
  `time_left` bigint(11) NOT NULL,
  `time_back` bigint(11) NOT NULL,
  `reason` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idperson` (`iddepsen`),
  KEY `chamber` (`chamber`),
  KEY `idperson_2` (`idperson`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=40 ;

-- --------------------------------------------------------

--
-- Table structure for table `catavencu_2008`
--

DROP TABLE IF EXISTS `catavencu_2008`;
CREATE TABLE IF NOT EXISTS `catavencu_2008` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `idperson` int(11) NOT NULL,
  `t` text NOT NULL,
  `url` varchar(150) NOT NULL,
  `party` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=507 ;

-- --------------------------------------------------------

--
-- Table structure for table `cdep_2004_belong`
--

DROP TABLE IF EXISTS `cdep_2004_belong`;
CREATE TABLE IF NOT EXISTS `cdep_2004_belong` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `iddep` int(11) NOT NULL,
  `idperson` int(11) NOT NULL,
  `idparty` int(11) NOT NULL,
  `time` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `iddep` (`iddep`,`idparty`,`time`),
  KEY `idperson` (`idperson`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED AUTO_INCREMENT=665022 ;

-- --------------------------------------------------------

--
-- Table structure for table `cdep_2004_belong_agg`
--

DROP TABLE IF EXISTS `cdep_2004_belong_agg`;
CREATE TABLE IF NOT EXISTS `cdep_2004_belong_agg` (
  `iddep` int(11) NOT NULL,
  `idperson` int(11) NOT NULL,
  `idparty` int(11) NOT NULL,
  KEY `iddep` (`iddep`,`idparty`),
  KEY `idperson` (`idperson`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- --------------------------------------------------------

--
-- Table structure for table `cdep_2004_deputies`
--

DROP TABLE IF EXISTS `cdep_2004_deputies`;
CREATE TABLE IF NOT EXISTS `cdep_2004_deputies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idm` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `idperson` int(11) NOT NULL,
  `timein` bigint(20) NOT NULL,
  `timeout` bigint(20) NOT NULL DEFAULT '0',
  `next` int(11) NOT NULL DEFAULT '0',
  `prev` int(11) NOT NULL DEFAULT '0',
  `motif` varchar(50) NOT NULL,
  `imgurl` varchar(100) NOT NULL,
  KEY `id` (`id`),
  KEY `name` (`name`),
  KEY `idperson` (`idperson`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=367 ;

-- --------------------------------------------------------

--
-- Table structure for table `cdep_2004_laws`
--

DROP TABLE IF EXISTS `cdep_2004_laws`;
CREATE TABLE IF NOT EXISTS `cdep_2004_laws` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idp` int(11) NOT NULL,
  `number` varchar(20) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idp` (`idp`,`number`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=2134 ;

-- --------------------------------------------------------

--
-- Table structure for table `cdep_2004_laws_proponents`
--

DROP TABLE IF EXISTS `cdep_2004_laws_proponents`;
CREATE TABLE IF NOT EXISTS `cdep_2004_laws_proponents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `iddep` int(11) NOT NULL,
  `idperson` int(11) NOT NULL,
  `idlaw` int(11) NOT NULL,
  `authorscount` int(11) NOT NULL,
  `chamber` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `iddep` (`iddep`),
  KEY `idlaw` (`idlaw`),
  KEY `chamber` (`chamber`),
  KEY `idperson` (`idperson`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED AUTO_INCREMENT=4221 ;

-- --------------------------------------------------------

--
-- Table structure for table `cdep_2004_laws_status`
--

DROP TABLE IF EXISTS `cdep_2004_laws_status`;
CREATE TABLE IF NOT EXISTS `cdep_2004_laws_status` (
  `idlaw` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  KEY `idlaw` (`idlaw`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- --------------------------------------------------------

--
-- Table structure for table `cdep_2004_video`
--

DROP TABLE IF EXISTS `cdep_2004_video`;
CREATE TABLE IF NOT EXISTS `cdep_2004_video` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `iddep` int(11) NOT NULL,
  `idperson` int(11) NOT NULL,
  `idv` int(11) NOT NULL COMMENT 'idv is Id Video not idvote, talk to cdep.ro not me :-)',
  `sessions` int(11) NOT NULL,
  `seconds` int(11) NOT NULL COMMENT 'length is in number of seconds',
  PRIMARY KEY (`id`),
  KEY `iddep` (`iddep`),
  KEY `idperson` (`idperson`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED AUTO_INCREMENT=365 ;

-- --------------------------------------------------------

--
-- Table structure for table `cdep_2004_votes`
--

DROP TABLE IF EXISTS `cdep_2004_votes`;
CREATE TABLE IF NOT EXISTS `cdep_2004_votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idv` int(11) NOT NULL,
  `idlaw` int(11) NOT NULL,
  `iddep` int(11) NOT NULL,
  `idperson` int(11) NOT NULL,
  `vote` varchar(20) NOT NULL,
  `time` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `vote` (`vote`),
  KEY `iddep` (`iddep`),
  KEY `idlaw` (`idlaw`),
  KEY `idv` (`idv`),
  KEY `idperson` (`idperson`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=665022 ;

-- --------------------------------------------------------

--
-- Table structure for table `cdep_2004_votes_agg`
--

DROP TABLE IF EXISTS `cdep_2004_votes_agg`;
CREATE TABLE IF NOT EXISTS `cdep_2004_votes_agg` (
  `iddep` int(11) NOT NULL,
  `idperson` int(11) NOT NULL,
  `vda` int(11) NOT NULL,
  `vnu` int(11) NOT NULL,
  `vab` int(11) NOT NULL,
  `vmi` int(11) NOT NULL,
  `possible` int(11) NOT NULL,
  `percent` float NOT NULL,
  KEY `iddep` (`iddep`),
  KEY `idperson` (`idperson`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- --------------------------------------------------------

--
-- Table structure for table `cdep_2008_belong`
--

DROP TABLE IF EXISTS `cdep_2008_belong`;
CREATE TABLE IF NOT EXISTS `cdep_2008_belong` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `iddep` int(11) NOT NULL,
  `idperson` int(11) NOT NULL DEFAULT '0',
  `idparty` int(11) NOT NULL,
  `time` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `iddep` (`iddep`,`idparty`,`time`),
  KEY `idperson` (`idperson`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED AUTO_INCREMENT=3320738 ;

-- --------------------------------------------------------

--
-- Table structure for table `cdep_2008_belong_agg`
--

DROP TABLE IF EXISTS `cdep_2008_belong_agg`;
CREATE TABLE IF NOT EXISTS `cdep_2008_belong_agg` (
  `iddep` int(11) NOT NULL,
  `idperson` int(11) NOT NULL,
  `idparty` int(11) NOT NULL,
  KEY `iddep` (`iddep`,`idparty`),
  KEY `idperson` (`idperson`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- --------------------------------------------------------

--
-- Table structure for table `cdep_2008_deputies`
--

DROP TABLE IF EXISTS `cdep_2008_deputies`;
CREATE TABLE IF NOT EXISTS `cdep_2008_deputies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idm` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `idperson` int(11) NOT NULL,
  `timein` bigint(20) NOT NULL,
  `timeout` bigint(20) NOT NULL DEFAULT '0',
  `next` int(11) NOT NULL DEFAULT '0',
  `prev` int(11) NOT NULL DEFAULT '0',
  `motif` varchar(50) NOT NULL,
  `imgurl` varchar(100) NOT NULL,
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `id` (`id`),
  KEY `idperson` (`idperson`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=340 ;

-- --------------------------------------------------------

--
-- Table structure for table `cdep_2008_laws`
--

DROP TABLE IF EXISTS `cdep_2008_laws`;
CREATE TABLE IF NOT EXISTS `cdep_2008_laws` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `link` varchar(200) NOT NULL,
  `number` varchar(20) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idp` (`link`,`number`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1743 ;

-- --------------------------------------------------------

--
-- Table structure for table `cdep_2008_laws_proponents`
--

DROP TABLE IF EXISTS `cdep_2008_laws_proponents`;
CREATE TABLE IF NOT EXISTS `cdep_2008_laws_proponents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `iddep` int(11) NOT NULL,
  `idperson` int(11) NOT NULL DEFAULT '0',
  `idlaw` int(11) NOT NULL,
  `authorscount` int(11) NOT NULL,
  `chamber` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `iddep_2` (`iddep`,`idlaw`,`chamber`),
  KEY `iddep` (`iddep`),
  KEY `idlaw` (`idlaw`),
  KEY `chamber` (`chamber`),
  KEY `idperson` (`idperson`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED AUTO_INCREMENT=227 ;

-- --------------------------------------------------------

--
-- Table structure for table `cdep_2008_laws_status`
--

DROP TABLE IF EXISTS `cdep_2008_laws_status`;
CREATE TABLE IF NOT EXISTS `cdep_2008_laws_status` (
  `idlaw` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  UNIQUE KEY `idlaw` (`idlaw`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- --------------------------------------------------------

--
-- Table structure for table `cdep_2008_votes`
--

DROP TABLE IF EXISTS `cdep_2008_votes`;
CREATE TABLE IF NOT EXISTS `cdep_2008_votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `link` varchar(200) NOT NULL,
  `idlaw` int(11) NOT NULL,
  `iddep` int(11) NOT NULL,
  `idperson` int(11) NOT NULL DEFAULT '0',
  `vote` varchar(20) NOT NULL,
  `maverick` tinyint(4) NOT NULL DEFAULT '0',
  `time` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idv_2` (`link`,`iddep`),
  KEY `vote` (`vote`),
  KEY `iddep` (`iddep`),
  KEY `idlaw` (`idlaw`),
  KEY `idv` (`link`),
  KEY `idperson` (`idperson`),
  KEY `time` (`time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=3431939 ;

-- --------------------------------------------------------

--
-- Table structure for table `cdep_2008_votes_agg`
--

DROP TABLE IF EXISTS `cdep_2008_votes_agg`;
CREATE TABLE IF NOT EXISTS `cdep_2008_votes_agg` (
  `iddep` int(11) NOT NULL,
  `idperson` int(11) NOT NULL,
  `vda` int(11) NOT NULL,
  `vnu` int(11) NOT NULL,
  `vab` int(11) NOT NULL,
  `vmi` int(11) NOT NULL,
  `possible` int(11) NOT NULL,
  `percent` float NOT NULL,
  `maverick` float NOT NULL,
  `days_in` int(11) NOT NULL,
  `days_possible` int(11) NOT NULL,
  KEY `iddep` (`iddep`),
  KEY `idperson` (`idperson`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- --------------------------------------------------------

--
-- Table structure for table `cdep_2008_votes_details`
--

DROP TABLE IF EXISTS `cdep_2008_votes_details`;
CREATE TABLE IF NOT EXISTS `cdep_2008_votes_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `link` varchar(200) NOT NULL,
  `idlaw` int(11) NOT NULL,
  `vda` int(11) NOT NULL,
  `vnu` int(11) NOT NULL,
  `vab` int(11) NOT NULL,
  `vmi` int(11) NOT NULL,
  `type` varchar(256) NOT NULL,
  `description` text NOT NULL,
  `time` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idlaw` (`idlaw`),
  KEY `link` (`link`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=73820 ;

-- --------------------------------------------------------

--
-- Table structure for table `counties`
--

DROP TABLE IF EXISTS `counties`;
CREATE TABLE IF NOT EXISTS `counties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=101 ;

-- --------------------------------------------------------

--
-- Table structure for table `euro_2009_candidates`
--

DROP TABLE IF EXISTS `euro_2009_candidates`;
CREATE TABLE IF NOT EXISTS `euro_2009_candidates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idperson` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `birthday` bigint(11) NOT NULL,
  `occupation` varchar(100) NOT NULL,
  `profession` varchar(100) NOT NULL,
  `idparty` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `birthday` (`birthday`),
  KEY `idparty` (`idparty`),
  KEY `idperson` (`idperson`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=290 ;

-- --------------------------------------------------------

--
-- Table structure for table `euro_parliament_2007`
--

DROP TABLE IF EXISTS `euro_parliament_2007`;
CREATE TABLE IF NOT EXISTS `euro_parliament_2007` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `idperson` int(11) NOT NULL DEFAULT '0',
  `time` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idperson` (`idperson`,`time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=62457 ;

-- --------------------------------------------------------

--
-- Table structure for table `euro_parliament_2007_agg`
--

DROP TABLE IF EXISTS `euro_parliament_2007_agg`;
CREATE TABLE IF NOT EXISTS `euro_parliament_2007_agg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idperson` int(11) NOT NULL,
  `present` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `tin` bigint(20) NOT NULL,
  `tout` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idperson` (`idperson`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=38 ;

-- --------------------------------------------------------

--
-- Table structure for table `euro_parliament_2007_qvorum`
--

DROP TABLE IF EXISTS `euro_parliament_2007_qvorum`;
CREATE TABLE IF NOT EXISTS `euro_parliament_2007_qvorum` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `idperson` int(11) NOT NULL,
  `text` text NOT NULL,
  `score` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`,`idperson`),
  KEY `score` (`score`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=30 ;

-- --------------------------------------------------------

--
-- Table structure for table `euro_parliament_2007_times`
--

DROP TABLE IF EXISTS `euro_parliament_2007_times`;
CREATE TABLE IF NOT EXISTS `euro_parliament_2007_times` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `tin` bigint(20) NOT NULL DEFAULT '0',
  `tout` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

-- --------------------------------------------------------

--
-- Table structure for table `govro_people`
--

DROP TABLE IF EXISTS `govro_people`;
CREATE TABLE IF NOT EXISTS `govro_people` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `idperson` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `mintime` bigint(11) NOT NULL,
  `maxtime` bigint(11) NOT NULL,
  `link` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `idperson` (`idperson`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

-- --------------------------------------------------------

--
-- Table structure for table `log_searches`
--

DROP TABLE IF EXISTS `log_searches`;
CREATE TABLE IF NOT EXISTS `log_searches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `query` text NOT NULL,
  `time` bigint(20) NOT NULL,
  `ip` varchar(50) NOT NULL,
  `num_results` tinyint(4) NOT NULL DEFAULT '-1',
  `found` int(11) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`id`),
  KEY `time` (`time`),
  KEY `num_results` (`num_results`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3741 ;

-- --------------------------------------------------------

--
-- Table structure for table `moderation_queue`
--

DROP TABLE IF EXISTS `moderation_queue`;
CREATE TABLE IF NOT EXISTS `moderation_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(15) NOT NULL,
  `idperson` int(11) NOT NULL,
  `value` text NOT NULL,
  `ip` text NOT NULL,
  `time` bigint(20) NOT NULL,
  `state` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `idperson` (`idperson`),
  KEY `state` (`state`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=267 ;

-- --------------------------------------------------------

--
-- Table structure for table `news_articles`
--

DROP TABLE IF EXISTS `news_articles`;
CREATE TABLE IF NOT EXISTS `news_articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` bigint(20) NOT NULL,
  `place` varchar(50) NOT NULL,
  `link` varchar(300) NOT NULL,
  `title` text NOT NULL,
  `source` varchar(40) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `time` (`time`),
  KEY `link` (`link`),
  KEY `source` (`source`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17363 ;

-- --------------------------------------------------------

--
-- Table structure for table `news_people`
--

DROP TABLE IF EXISTS `news_people`;
CREATE TABLE IF NOT EXISTS `news_people` (
  `idperson` int(11) NOT NULL,
  `idarticle` int(11) NOT NULL,
  PRIMARY KEY (`idperson`,`idarticle`),
  KEY `idperson` (`idperson`),
  KEY `idarticle` (`idarticle`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `news_qualifiers`
--

DROP TABLE IF EXISTS `news_qualifiers`;
CREATE TABLE IF NOT EXISTS `news_qualifiers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idarticle` int(11) NOT NULL,
  `idperson` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `qualifier` varchar(200) NOT NULL,
  `approved` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idarticle` (`idarticle`),
  KEY `name` (`name`),
  KEY `idperson` (`idperson`),
  KEY `approved` (`approved`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=33973 ;

-- --------------------------------------------------------

--
-- Table structure for table `parl_tagged_votes`
--

DROP TABLE IF EXISTS `parl_tagged_votes`;
CREATE TABLE IF NOT EXISTS `parl_tagged_votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `votes_table` varchar(30) NOT NULL,
  `idvote` int(11) NOT NULL,
  `link` varchar(200) NOT NULL,
  `idtag` int(11) NOT NULL,
  `inverse` tinyint(4) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL,
  KEY `id` (`id`),
  KEY `idvote` (`idvote`),
  KEY `idtag` (`idtag`),
  KEY `votes_table` (`votes_table`),
  KEY `uid` (`uid`),
  KEY `link` (`link`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=51 ;

-- --------------------------------------------------------

--
-- Table structure for table `parl_tags`
--

DROP TABLE IF EXISTS `parl_tags`;
CREATE TABLE IF NOT EXISTS `parl_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

--
-- Table structure for table `parties`
--

DROP TABLE IF EXISTS `parties`;
CREATE TABLE IF NOT EXISTS `parties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `long_name` varchar(150) NOT NULL,
  `minoritati` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `minoritati` (`minoritati`),
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=46 ;

-- --------------------------------------------------------

--
-- Table structure for table `parties_facts`
--

DROP TABLE IF EXISTS `parties_facts`;
CREATE TABLE IF NOT EXISTS `parties_facts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idparty` int(11) NOT NULL,
  `attribute` varchar(30) NOT NULL,
  `value` text NOT NULL,
  `time_ms` bigint(20) NOT NULL COMMENT 'The time at which this fact was true, in milliseconds',
  PRIMARY KEY (`id`),
  UNIQUE KEY `party_attribute` (`idparty`,`attribute`),
  KEY `attribute` (`attribute`),
  KEY `idparty` (`idparty`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=81 ;

-- --------------------------------------------------------

--
-- Table structure for table `parties_modules`
--

DROP TABLE IF EXISTS `parties_modules`;
CREATE TABLE IF NOT EXISTS `parties_modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idparty` int(11) NOT NULL,
  `what` varchar(124) NOT NULL,
  `url` varchar(250) NOT NULL,
  `time` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idperson_2` (`idparty`,`what`),
  KEY `idperson` (`idparty`),
  KEY `what` (`what`),
  KEY `time` (`time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `people`
--

DROP TABLE IF EXISTS `people`;
CREATE TABLE IF NOT EXISTS `people` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT 'The exhaustive list of all the names that this person has.',
  `display_name` varchar(100) NOT NULL COMMENT 'The name that will be displayed for this person across the system.',
  `ext` varchar(100) NOT NULL COMMENT 'An extended field to distinguish between persons with identical names. Usage TBD.',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=3415 ;

-- --------------------------------------------------------

--
-- Table structure for table `people_ambiguities`
--

DROP TABLE IF EXISTS `people_ambiguities`;
CREATE TABLE IF NOT EXISTS `people_ambiguities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(100) NOT NULL,
  `name` varchar(150) NOT NULL,
  `resolve_to` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=942 ;

-- --------------------------------------------------------

--
-- Table structure for table `people_facts`
--

DROP TABLE IF EXISTS `people_facts`;
CREATE TABLE IF NOT EXISTS `people_facts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idperson` int(11) NOT NULL,
  `attribute` varchar(30) NOT NULL,
  `value` text NOT NULL,
  `time_ms` bigint(20) NOT NULL COMMENT 'The time at which this fact was true, in milliseconds',
  PRIMARY KEY (`id`),
  KEY `idperson` (`idperson`),
  KEY `attribute` (`attribute`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18472 ;

-- --------------------------------------------------------

--
-- Table structure for table `people_history`
--

DROP TABLE IF EXISTS `people_history`;
CREATE TABLE IF NOT EXISTS `people_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idperson` int(11) NOT NULL,
  `what` varchar(124) NOT NULL,
  `url` varchar(250) NOT NULL,
  `time` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idperson_2` (`idperson`,`what`),
  KEY `idperson` (`idperson`),
  KEY `what` (`what`),
  KEY `time` (`time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=7125 ;

-- --------------------------------------------------------

--
-- Table structure for table `pres_2009_people`
--

DROP TABLE IF EXISTS `pres_2009_people`;
CREATE TABLE IF NOT EXISTS `pres_2009_people` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `idperson` int(11) NOT NULL,
  `party` varchar(100) NOT NULL,
  `idparty` int(11) NOT NULL,
  `details` text NOT NULL,
  `retras` int(11) NOT NULL DEFAULT '0',
  KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

-- --------------------------------------------------------

--
-- Table structure for table `results_2008`
--

DROP TABLE IF EXISTS `results_2008`;
CREATE TABLE IF NOT EXISTS `results_2008` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nume` varchar(100) NOT NULL,
  `idcandidat` int(11) NOT NULL,
  `idperson` int(11) NOT NULL,
  `partid` varchar(100) NOT NULL,
  `idpartid` int(11) NOT NULL,
  `colegiu` varchar(40) NOT NULL,
  `voturi` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idcandidat` (`idcandidat`),
  KEY `nume` (`nume`),
  KEY `colegiu` (`colegiu`),
  KEY `idperson` (`idperson`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16328 ;

-- --------------------------------------------------------

--
-- Table structure for table `results_2008_agg`
--

DROP TABLE IF EXISTS `results_2008_agg`;
CREATE TABLE IF NOT EXISTS `results_2008_agg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `college` varchar(40) NOT NULL,
  `county` varchar(50) NOT NULL,
  `college_nr` varchar(5) NOT NULL,
  `winnerid` int(11) NOT NULL,
  `idperson_winner` int(11) NOT NULL,
  `runnerupid` int(11) NOT NULL,
  `idperson_runnerup` int(11) NOT NULL,
  `reason` varchar(150) NOT NULL,
  `total` int(11) NOT NULL,
  `winvotes` int(11) NOT NULL,
  `runvotes` int(11) NOT NULL,
  `difference` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `winnerid` (`winnerid`),
  KEY `runnerupid` (`runnerupid`),
  KEY `difference` (`difference`),
  KEY `idperson_winner` (`idperson_winner`),
  KEY `idperson_runnerup` (`idperson_runnerup`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=451 ;

-- --------------------------------------------------------

--
-- Table structure for table `results_2008_allocated`
--

DROP TABLE IF EXISTS `results_2008_allocated`;
CREATE TABLE IF NOT EXISTS `results_2008_allocated` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partid` varchar(100) NOT NULL,
  `idpartid` int(11) NOT NULL,
  `judet` varchar(40) NOT NULL,
  `room` varchar(1) NOT NULL,
  `numar` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `room` (`room`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=345 ;

-- --------------------------------------------------------

--
-- Table structure for table `results_2008_candidates`
--

DROP TABLE IF EXISTS `results_2008_candidates`;
CREATE TABLE IF NOT EXISTS `results_2008_candidates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nume` varchar(100) NOT NULL,
  `idperson` int(11) NOT NULL,
  `idpartid` int(11) NOT NULL,
  `winner` int(11) NOT NULL,
  `difference` int(11) NOT NULL,
  `college` varchar(40) NOT NULL,
  `reason` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `nume` (`nume`),
  KEY `college` (`college`),
  KEY `idperson` (`idperson`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8599 ;

-- --------------------------------------------------------

--
-- Table structure for table `results_2008_voters`
--

DROP TABLE IF EXISTS `results_2008_voters`;
CREATE TABLE IF NOT EXISTS `results_2008_voters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `college` varchar(50) NOT NULL,
  `possible_votes` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `college` (`college`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=316 ;

-- --------------------------------------------------------

--
-- Table structure for table `senat_2004_belong`
--

DROP TABLE IF EXISTS `senat_2004_belong`;
CREATE TABLE IF NOT EXISTS `senat_2004_belong` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idsen` int(11) NOT NULL,
  `idperson` int(11) NOT NULL,
  `idparty` int(11) NOT NULL,
  `time` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `iddep` (`idsen`,`idparty`,`time`),
  KEY `idperson` (`idperson`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED AUTO_INCREMENT=45653 ;

-- --------------------------------------------------------

--
-- Table structure for table `senat_2004_belong_agg`
--

DROP TABLE IF EXISTS `senat_2004_belong_agg`;
CREATE TABLE IF NOT EXISTS `senat_2004_belong_agg` (
  `idsen` int(11) NOT NULL,
  `idperson` int(11) NOT NULL,
  `idparty` int(11) NOT NULL,
  KEY `iddep` (`idsen`,`idparty`),
  KEY `idperson` (`idperson`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- --------------------------------------------------------

--
-- Table structure for table `senat_2004_laws`
--

DROP TABLE IF EXISTS `senat_2004_laws`;
CREATE TABLE IF NOT EXISTS `senat_2004_laws` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appid` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idp` (`appid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=760 ;

-- --------------------------------------------------------

--
-- Table structure for table `senat_2004_senators`
--

DROP TABLE IF EXISTS `senat_2004_senators`;
CREATE TABLE IF NOT EXISTS `senat_2004_senators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idm` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `idperson` int(11) NOT NULL,
  `timein` bigint(20) NOT NULL DEFAULT '0',
  `timeout` bigint(20) NOT NULL DEFAULT '0',
  `next` int(11) NOT NULL DEFAULT '0',
  `prev` int(11) NOT NULL DEFAULT '0',
  `motif` varchar(50) DEFAULT NULL,
  `imgurl` varchar(100) DEFAULT NULL,
  `name_diacritics` varchar(100) DEFAULT NULL,
  KEY `id` (`id`),
  KEY `name` (`name`),
  KEY `idperson` (`idperson`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=164 ;

-- --------------------------------------------------------

--
-- Table structure for table `senat_2004_votes`
--

DROP TABLE IF EXISTS `senat_2004_votes`;
CREATE TABLE IF NOT EXISTS `senat_2004_votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idv` varchar(100) NOT NULL,
  `idlaw` int(11) NOT NULL,
  `idsen` int(11) NOT NULL,
  `idperson` int(11) NOT NULL,
  `vote` varchar(20) NOT NULL,
  `time` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `vote` (`vote`),
  KEY `iddep` (`idsen`),
  KEY `idlaw` (`idlaw`),
  KEY `idv` (`idv`),
  KEY `idperson` (`idperson`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=45653 ;

-- --------------------------------------------------------

--
-- Table structure for table `senat_2004_votes_agg`
--

DROP TABLE IF EXISTS `senat_2004_votes_agg`;
CREATE TABLE IF NOT EXISTS `senat_2004_votes_agg` (
  `idsen` int(11) NOT NULL,
  `idperson` int(11) NOT NULL,
  `vda` int(11) NOT NULL,
  `vnu` int(11) NOT NULL,
  `vab` int(11) NOT NULL,
  `vmi` int(11) NOT NULL,
  `possible` int(11) NOT NULL,
  `percent` float NOT NULL,
  KEY `iddep` (`idsen`),
  KEY `idperson` (`idperson`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- --------------------------------------------------------

--
-- Table structure for table `senat_2008_belong`
--

DROP TABLE IF EXISTS `senat_2008_belong`;
CREATE TABLE IF NOT EXISTS `senat_2008_belong` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `iddep` int(11) NOT NULL,
  `idperson` int(11) NOT NULL DEFAULT '0',
  `idparty` int(11) NOT NULL,
  `time` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `iddep` (`iddep`,`idparty`,`time`),
  KEY `idperson` (`idperson`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED AUTO_INCREMENT=2012376 ;

-- --------------------------------------------------------

--
-- Table structure for table `senat_2008_belong_agg`
--

DROP TABLE IF EXISTS `senat_2008_belong_agg`;
CREATE TABLE IF NOT EXISTS `senat_2008_belong_agg` (
  `iddep` int(11) NOT NULL,
  `idperson` int(11) NOT NULL,
  `idparty` int(11) NOT NULL,
  KEY `iddep` (`iddep`,`idparty`),
  KEY `idperson` (`idperson`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- --------------------------------------------------------

--
-- Table structure for table `senat_2008_laws`
--

DROP TABLE IF EXISTS `senat_2008_laws`;
CREATE TABLE IF NOT EXISTS `senat_2008_laws` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `link` varchar(200) NOT NULL,
  `number` varchar(20) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idp` (`link`,`number`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=3137 ;

-- --------------------------------------------------------

--
-- Table structure for table `senat_2008_senators`
--

DROP TABLE IF EXISTS `senat_2008_senators`;
CREATE TABLE IF NOT EXISTS `senat_2008_senators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idm` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `idperson` int(11) NOT NULL,
  `timein` bigint(20) NOT NULL DEFAULT '0',
  `timeout` bigint(20) NOT NULL DEFAULT '0',
  `next` int(11) NOT NULL DEFAULT '0',
  `prev` int(11) NOT NULL DEFAULT '0',
  `motif` varchar(50) DEFAULT NULL,
  `imgurl` varchar(100) DEFAULT NULL,
  KEY `id` (`id`),
  KEY `name` (`name`),
  KEY `idperson` (`idperson`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=138 ;

-- --------------------------------------------------------

--
-- Table structure for table `senat_2008_votes`
--

DROP TABLE IF EXISTS `senat_2008_votes`;
CREATE TABLE IF NOT EXISTS `senat_2008_votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `link` varchar(200) NOT NULL,
  `idlaw` int(11) NOT NULL,
  `idsen` int(11) NOT NULL,
  `idperson` int(11) NOT NULL DEFAULT '0',
  `vote` varchar(20) NOT NULL,
  `maverick` tinyint(4) NOT NULL DEFAULT '0',
  `time` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `vote` (`vote`),
  KEY `idlaw` (`idlaw`),
  KEY `idv` (`link`),
  KEY `idperson` (`idperson`),
  KEY `idsen` (`idsen`),
  KEY `link` (`link`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=2018589 ;

-- --------------------------------------------------------

--
-- Table structure for table `senat_2008_votes_agg`
--

DROP TABLE IF EXISTS `senat_2008_votes_agg`;
CREATE TABLE IF NOT EXISTS `senat_2008_votes_agg` (
  `iddep` int(11) NOT NULL,
  `idperson` int(11) NOT NULL,
  `vda` int(11) NOT NULL,
  `vnu` int(11) NOT NULL,
  `vab` int(11) NOT NULL,
  `vmi` int(11) NOT NULL,
  `possible` int(11) NOT NULL,
  `percent` float NOT NULL,
  `maverick` float NOT NULL,
  `days_in` int(11) NOT NULL,
  `days_possible` int(11) NOT NULL,
  KEY `iddep` (`iddep`),
  KEY `idperson` (`idperson`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- --------------------------------------------------------

--
-- Table structure for table `senat_2008_votes_details`
--

DROP TABLE IF EXISTS `senat_2008_votes_details`;
CREATE TABLE IF NOT EXISTS `senat_2008_votes_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `link` varchar(200) NOT NULL,
  `idlaw` int(11) NOT NULL,
  `vda` int(11) NOT NULL,
  `vnu` int(11) NOT NULL,
  `vab` int(11) NOT NULL,
  `vmi` int(11) NOT NULL,
  `type` varchar(256) NOT NULL,
  `description` text NOT NULL,
  `time` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idlaw` (`idlaw`),
  KEY `link` (`link`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=43753 ;

-- --------------------------------------------------------

--
-- Table structure for table `wiki_edits`
--

DROP TABLE IF EXISTS `wiki_edits`;
CREATE TABLE IF NOT EXISTS `wiki_edits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `query` varchar(400) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

-- --------------------------------------------------------

--
-- Table structure for table `wp_commentmeta`
--

DROP TABLE IF EXISTS `wp_commentmeta`;
CREATE TABLE IF NOT EXISTS `wp_commentmeta` (
  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `comment_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`meta_id`),
  KEY `comment_id` (`comment_id`),
  KEY `meta_key` (`meta_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Table structure for table `wp_comments`
--

DROP TABLE IF EXISTS `wp_comments`;
CREATE TABLE IF NOT EXISTS `wp_comments` (
  `comment_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `comment_post_ID` bigint(20) unsigned NOT NULL DEFAULT '0',
  `comment_author` tinytext NOT NULL,
  `comment_author_email` varchar(100) NOT NULL DEFAULT '',
  `comment_author_url` varchar(200) NOT NULL DEFAULT '',
  `comment_author_IP` varchar(100) NOT NULL DEFAULT '',
  `comment_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment_content` text NOT NULL,
  `comment_karma` int(11) NOT NULL DEFAULT '0',
  `comment_approved` varchar(20) NOT NULL DEFAULT '1',
  `comment_agent` varchar(255) NOT NULL DEFAULT '',
  `comment_type` varchar(20) NOT NULL DEFAULT '',
  `comment_parent` bigint(20) unsigned NOT NULL DEFAULT '0',
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `comment_subscribe` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`comment_ID`),
  KEY `comment_approved` (`comment_approved`),
  KEY `comment_post_ID` (`comment_post_ID`),
  KEY `comment_approved_date_gmt` (`comment_approved`,`comment_date_gmt`),
  KEY `comment_date_gmt` (`comment_date_gmt`),
  KEY `comment_parent` (`comment_parent`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=492 ;

-- --------------------------------------------------------

--
-- Table structure for table `wp_links`
--

DROP TABLE IF EXISTS `wp_links`;
CREATE TABLE IF NOT EXISTS `wp_links` (
  `link_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `link_url` varchar(255) NOT NULL DEFAULT '',
  `link_name` varchar(255) NOT NULL DEFAULT '',
  `link_image` varchar(255) NOT NULL DEFAULT '',
  `link_target` varchar(25) NOT NULL DEFAULT '',
  `link_category` bigint(20) NOT NULL DEFAULT '0',
  `link_description` varchar(255) NOT NULL DEFAULT '',
  `link_visible` varchar(20) NOT NULL DEFAULT 'Y',
  `link_owner` bigint(20) unsigned NOT NULL DEFAULT '1',
  `link_rating` int(11) NOT NULL DEFAULT '0',
  `link_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `link_rel` varchar(255) NOT NULL DEFAULT '',
  `link_notes` mediumtext NOT NULL,
  `link_rss` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`link_id`),
  KEY `link_category` (`link_category`),
  KEY `link_visible` (`link_visible`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

-- --------------------------------------------------------

--
-- Table structure for table `wp_options`
--

DROP TABLE IF EXISTS `wp_options`;
CREATE TABLE IF NOT EXISTS `wp_options` (
  `option_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `blog_id` int(11) NOT NULL DEFAULT '0',
  `option_name` varchar(64) NOT NULL DEFAULT '',
  `option_value` longtext NOT NULL,
  `autoload` varchar(20) NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`option_id`),
  UNIQUE KEY `option_name` (`option_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1185 ;

-- --------------------------------------------------------

--
-- Table structure for table `wp_postmeta`
--

DROP TABLE IF EXISTS `wp_postmeta`;
CREATE TABLE IF NOT EXISTS `wp_postmeta` (
  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`meta_id`),
  KEY `post_id` (`post_id`),
  KEY `meta_key` (`meta_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=549 ;

-- --------------------------------------------------------

--
-- Table structure for table `wp_posts`
--

DROP TABLE IF EXISTS `wp_posts`;
CREATE TABLE IF NOT EXISTS `wp_posts` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_author` bigint(20) unsigned NOT NULL DEFAULT '0',
  `post_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content` longtext NOT NULL,
  `post_title` text NOT NULL,
  `post_category` int(4) NOT NULL DEFAULT '0',
  `post_excerpt` text NOT NULL,
  `post_status` varchar(20) NOT NULL DEFAULT 'publish',
  `comment_status` varchar(20) NOT NULL DEFAULT 'open',
  `ping_status` varchar(20) NOT NULL DEFAULT 'open',
  `post_password` varchar(20) NOT NULL DEFAULT '',
  `post_name` varchar(200) NOT NULL DEFAULT '',
  `to_ping` text NOT NULL,
  `pinged` text NOT NULL,
  `post_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_modified_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content_filtered` text NOT NULL,
  `post_parent` bigint(20) unsigned NOT NULL DEFAULT '0',
  `guid` varchar(255) NOT NULL DEFAULT '',
  `menu_order` int(11) NOT NULL DEFAULT '0',
  `post_type` varchar(20) NOT NULL DEFAULT 'post',
  `post_mime_type` varchar(100) NOT NULL DEFAULT '',
  `comment_count` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `post_name` (`post_name`),
  KEY `type_status_date` (`post_type`,`post_status`,`post_date`,`ID`),
  KEY `post_parent` (`post_parent`),
  KEY `post_author` (`post_author`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4922 ;

-- --------------------------------------------------------

--
-- Table structure for table `wp_terms`
--

DROP TABLE IF EXISTS `wp_terms`;
CREATE TABLE IF NOT EXISTS `wp_terms` (
  `term_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL DEFAULT '',
  `slug` varchar(200) NOT NULL DEFAULT '',
  `term_group` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`term_id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Table structure for table `wp_term_relationships`
--

DROP TABLE IF EXISTS `wp_term_relationships`;
CREATE TABLE IF NOT EXISTS `wp_term_relationships` (
  `object_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `term_taxonomy_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `term_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`object_id`,`term_taxonomy_id`),
  KEY `term_taxonomy_id` (`term_taxonomy_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `wp_term_taxonomy`
--

DROP TABLE IF EXISTS `wp_term_taxonomy`;
CREATE TABLE IF NOT EXISTS `wp_term_taxonomy` (
  `term_taxonomy_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `term_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `taxonomy` varchar(32) NOT NULL DEFAULT '',
  `description` longtext NOT NULL,
  `parent` bigint(20) unsigned NOT NULL DEFAULT '0',
  `count` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`term_taxonomy_id`),
  UNIQUE KEY `term_id_taxonomy` (`term_id`,`taxonomy`),
  KEY `taxonomy` (`taxonomy`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

--
-- Table structure for table `wp_usermeta`
--

DROP TABLE IF EXISTS `wp_usermeta`;
CREATE TABLE IF NOT EXISTS `wp_usermeta` (
  `umeta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`umeta_id`),
  KEY `user_id` (`user_id`),
  KEY `meta_key` (`meta_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2700 ;

-- --------------------------------------------------------

--
-- Table structure for table `wp_users`
--

DROP TABLE IF EXISTS `wp_users`;
CREATE TABLE IF NOT EXISTS `wp_users` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_login` varchar(60) NOT NULL DEFAULT '',
  `user_pass` varchar(64) NOT NULL DEFAULT '',
  `user_nicename` varchar(50) NOT NULL DEFAULT '',
  `user_email` varchar(100) NOT NULL DEFAULT '',
  `user_url` varchar(100) NOT NULL DEFAULT '',
  `user_registered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_activation_key` varchar(60) NOT NULL DEFAULT '',
  `user_status` int(11) NOT NULL DEFAULT '0',
  `display_name` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`),
  KEY `user_login_key` (`user_login`),
  KEY `user_nicename` (`user_nicename`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=451 ;

-- --------------------------------------------------------

--
-- Table structure for table `yt_videos`
--

DROP TABLE IF EXISTS `yt_videos`;
CREATE TABLE IF NOT EXISTS `yt_videos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idperson` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `thumb` varchar(200) NOT NULL,
  `duration` int(11) NOT NULL,
  `watch_url` varchar(200) NOT NULL,
  `player_url` varchar(200) NOT NULL,
  `time` bigint(20) NOT NULL,
  `approved` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idperson_2` (`idperson`,`watch_url`),
  KEY `id` (`id`),
  KEY `idperson` (`idperson`),
  KEY `approved` (`approved`),
  KEY `time` (`time`),
  KEY `watch_url` (`watch_url`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3785 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
