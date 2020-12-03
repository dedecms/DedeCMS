DROP TABLE IF EXISTS `#@__addonarticle`;
CREATE TABLE `#@__addonarticle` (
  `aid` int(11) NOT NULL default '0',
  `typeid` int(11) NOT NULL default '0',
  `body` mediumtext NOT NULL,
  PRIMARY KEY  (`aid`),
  KEY `typeid` (`typeid`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__addonflash`;
CREATE TABLE `#@__addonflash` (
  `aid` int(11) NOT NULL default '0',
  `typeid` int(11) NOT NULL default '0',
  `filesize` varchar(10) NOT NULL default '',
  `playtime` varchar(10) NOT NULL default '',
  `flashtype` varchar(10) NOT NULL default '',
  `flashrank` smallint(6) NOT NULL default '0',
  `width` smallint(6) NOT NULL default '0',
  `height` smallint(6) NOT NULL default '0',
  `flashurl` varchar(80) NOT NULL default '',
  `flashhh` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`aid`),
  KEY `flashMain` (`typeid`,`filesize`,`flashrank`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__addonimages`;
CREATE TABLE `#@__addonimages` (
  `aid` int(11) NOT NULL default '0',
  `typeid` int(11) NOT NULL default '0',
  `pagestyle` smallint(6) default '2',
  `maxwidth` smallint(6) default '800',
  `imgurls` text NOT NULL,
  `row` smallint(6) NOT NULL default '0',
  `col` smallint(6) NOT NULL default '0',
  `isrm` smallint(6) NOT NULL default '0',
  `ddmaxwidth` smallint(6) NOT NULL default '200',
  PRIMARY KEY  (`aid`),
  KEY `imagesMain` (`typeid`,`pagestyle`,`maxwidth`,`row`,`col`,`isrm`,`ddmaxwidth`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__addoninfos`;
CREATE TABLE `#@__addoninfos` (
  `aid` int(11) NOT NULL default '0',
  `typeid` int(11) unsigned NOT NULL default '0',
  `message` mediumtext,
  `contact` varchar(50) default NULL,
  `phone` varchar(15) default NULL,
  `fax` varchar(15) default NULL,
  `email` varchar(50) default NULL,
  `qq` varchar(50) default NULL,
  `msn` varchar(50) default NULL,
  `address` varchar(255) default NULL,
  PRIMARY KEY  (`aid`),
  KEY `typeid` (`typeid`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__addonproduct`;
CREATE TABLE `#@__addonproduct` (
  `aid` int(11) NOT NULL default '0',
  `typeid` int(11) NOT NULL default '0',
  `assprice` float NOT NULL default '0',
  `msg` mediumtext NOT NULL,
  `bigpic` varchar(200) NOT NULL default '',
  `model` varchar(100) NOT NULL default '',
  `sptype` varchar(10) NOT NULL default '',
  `cometime` int(11) NOT NULL default '0',
  `brand` varchar(30) NOT NULL default '',
  `size` varchar(50) NOT NULL default '',
  `stuff` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`aid`),
  KEY `#@__addonproduct_index` (`typeid`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__addonsoft`;
CREATE TABLE `#@__addonsoft` (
  `aid` int(11) NOT NULL default '0',
  `typeid` int(11) NOT NULL default '0',
  `filetype` varchar(10) NOT NULL default '',
  `language` varchar(10) NOT NULL default '',
  `softtype` varchar(10) NOT NULL default '',
  `accredit` varchar(10) NOT NULL default '',
  `os` varchar(30) NOT NULL default '',
  `softrank` int(11) NOT NULL default '0',
  `officialUrl` varchar(30) NOT NULL default '',
  `officialDemo` varchar(50) NOT NULL default '',
  `softsize` varchar(10) NOT NULL default '',
  `softlinks` text NOT NULL,
  `introduce` text NOT NULL,
  PRIMARY KEY  (`aid`),
  KEY `softMain` (`typeid`,`filetype`,`language`,`os`,`softrank`,`softsize`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__addonspec`;
CREATE TABLE `#@__addonspec` (
  `aid` int(11) NOT NULL default '0',
  `typeid` int(11) NOT NULL default '0',
  `note` text NOT NULL,
  PRIMARY KEY  (`aid`),
  KEY `typeid` (`typeid`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__admin`;
CREATE TABLE `#@__admin` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `usertype` int(10) unsigned NOT NULL default '0',
  `userid` varchar(30) NOT NULL default '',
  `pwd` varchar(50) NOT NULL default '',
  `uname` varchar(20) NOT NULL default '',
  `tname` varchar(30) NOT NULL default '',
  `email` varchar(30) NOT NULL default '',
  `typeid` text,
  `logintime` datetime NOT NULL default '0000-00-00 00:00:00',
  `loginip` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`ID`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__admintype`;
CREATE TABLE `#@__admintype` (
  `rank` smallint(6) NOT NULL default '1',
  `typename` varchar(30) NOT NULL default '',
  `system` smallint(6) NOT NULL default '0',
  `purviews` text NOT NULL
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__arcatt`;
CREATE TABLE `#@__arcatt` (
  `att` smallint(6) NOT NULL default '0',
  `attname` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`att`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__archives`;
CREATE TABLE `#@__archives` (
  `ID` int(11) unsigned NOT NULL default '0',
  `typeid` int(11) unsigned NOT NULL default '0',
  `typeid2` int(11) unsigned NOT NULL default '0',
  `sortrank` int(11) NOT NULL default '0',
  `iscommend` smallint(6) NOT NULL default '0',
  `ismake` smallint(6) NOT NULL default '0',
  `channel` int(11) NOT NULL default '1',
  `arcrank` smallint(6) NOT NULL default '0',
  `click` int(11) unsigned NOT NULL default '0',
  `money` smallint(6) NOT NULL default '0',
  `title` varchar(80) NOT NULL default '',
  `shorttitle` varchar(36) NOT NULL default '',
  `color` varchar(10) NOT NULL default '',
  `writer` varchar(30) NOT NULL default '',
  `source` varchar(50) NOT NULL default '',
  `litpic` varchar(100) NOT NULL default '',
  `pubdate` int(11) NOT NULL default '0',
  `senddate` int(11) NOT NULL default '0',
  `arcatt` smallint(6) NOT NULL default '0',
  `adminID` int(11) NOT NULL default '0',
  `memberID` int(11) unsigned NOT NULL default '0',
  `description` varchar(250) NOT NULL default '',
  `keywords` varchar(60) NOT NULL default '',
  `templet` varchar(60) NOT NULL default '',
  `lastpost` int(11) NOT NULL default '0',
  `postnum` int(11) NOT NULL default '0',
  `redirecturl` varchar(150) NOT NULL default '',
  `mtype` int(11) NOT NULL default '0',
  `userip` varchar(20) NOT NULL default '',
  `locklikeid` smallint(6) NOT NULL default '0',
  `likeid` varchar(240) default '',
  `smalltypeid` smallint(6) NOT NULL default '0',
  `areaid` smallint(6) NOT NULL default '0',
  `areaid2` smallint(6) NOT NULL default '0',
  `sectorid` smallint(6) NOT NULL default '0',
  `sectorid2` smallint(6) NOT NULL default '0',
  `endtime` int(11) NOT NULL default '0',
  `digg` int(11) NOT NULL default '0',
  `diggtime` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ID`),
  KEY `typeid` (`typeid`,`typeid2`,`sortrank`,`channel`,`arcrank`,`adminID`,`memberID`,`smalltypeid`,`areaid`,`areaid2`,`sectorid`,`sectorid2`,`senddate`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__archivesspec`;
CREATE TABLE `#@__archivesspec` (
  `ID` int(11) unsigned NOT NULL default '0',
  `typeid` int(11) unsigned NOT NULL default '0',
  `typeid2` int(11) unsigned NOT NULL default '0',
  `sortrank` int(11) NOT NULL default '0',
  `iscommend` smallint(6) NOT NULL default '0',
  `ismake` smallint(6) NOT NULL default '0',
  `channel` int(11) NOT NULL default '1',
  `arcrank` smallint(6) NOT NULL default '0',
  `click` int(11) unsigned NOT NULL default '0',
  `money` smallint(6) NOT NULL default '0',
  `title` varchar(80) NOT NULL default '',
  `shorttitle` varchar(36) NOT NULL default '',
  `color` varchar(10) NOT NULL default '',
  `writer` varchar(30) NOT NULL default '',
  `source` varchar(50) NOT NULL default '',
  `litpic` varchar(100) NOT NULL default '',
  `pubdate` int(11) NOT NULL default '0',
  `senddate` int(11) NOT NULL default '0',
  `arcatt` smallint(6) NOT NULL default '0',
  `adminID` int(11) NOT NULL default '0',
  `memberID` int(11) unsigned NOT NULL default '0',
  `description` varchar(250) NOT NULL default '',
  `keywords` varchar(60) NOT NULL default '',
  `templet` varchar(60) NOT NULL default '',
  `lastpost` int(11) NOT NULL default '0',
  `postnum` int(11) NOT NULL default '0',
  `redirecturl` varchar(150) NOT NULL default '',
  `mtype` int(11) NOT NULL default '0',
  `userip` varchar(20) NOT NULL default '',
  `locklikeid` smallint(6) NOT NULL default '0',
  `likeid` varchar(240) default '',
  `smalltypeid` smallint(6) NOT NULL default '0',
  `areaid` smallint(6) NOT NULL default '0',
  `areaid2` smallint(6) NOT NULL default '0',
  `sectorid` smallint(6) NOT NULL default '0',
  `sectorid2` smallint(6) NOT NULL default '0',
  `endtime` int(11) NOT NULL default '0',
  `digg` int(11) NOT NULL default '0',
  `diggtime` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ID`),
  KEY `typeid` (`typeid`,`typeid2`,`sortrank`,`channel`,`arcrank`,`adminID`,`memberID`,`smalltypeid`,`areaid`,`areaid2`,`sectorid`,`sectorid2`,`senddate`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__arcrank`;
CREATE TABLE `#@__arcrank` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `rank` smallint(10) NOT NULL default '0',
  `membername` varchar(20) NOT NULL default '',
  `adminrank` smallint(6) NOT NULL default '0',
  `money` int(11) NOT NULL default '500',
  PRIMARY KEY  (`ID`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__arctype`;
CREATE TABLE `#@__arctype` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `reID` int(10) unsigned NOT NULL default '0',
  `topID` int(10) unsigned NOT NULL default '0',
  `sortrank` smallint(6) NOT NULL default '50',
  `typename` varchar(30) NOT NULL default '',
  `typedir` varchar(100) NOT NULL default '',
  `isdefault` smallint(6) NOT NULL default '0',
  `defaultname` varchar(20) NOT NULL default 'index.html',
  `issend` smallint(6) NOT NULL default '0',
  `channeltype` smallint(6) NOT NULL default '1',
  `maxpage` int(11) NOT NULL default '-1',
  `ispart` smallint(6) NOT NULL default '0',
  `corank` smallint(6) NOT NULL default '0',
  `tempindex` varchar(60) NOT NULL default '',
  `templist` varchar(60) NOT NULL default '',
  `temparticle` varchar(60) NOT NULL default '',
  `tempone` varchar(60) NOT NULL default '',
  `namerule` varchar(50) NOT NULL default '',
  `namerule2` varchar(50) NOT NULL default '',
  `modname` varchar(30) NOT NULL default '',
  `description` varchar(200) NOT NULL default '',
  `keywords` varchar(100) NOT NULL default '',
  `moresite` smallint(6) NOT NULL default '0',
  `siterefer` smallint(6) NOT NULL default '0',
  `sitepath` varchar(60) NOT NULL default '',
  `siteurl` varchar(60) NOT NULL default '',
  `ishidden` smallint(6) NOT NULL default '0',
  `smalltypes` varchar(250) NOT NULL default '',
  PRIMARY KEY  (`ID`),
  KEY `reID` (`reID`,`topID`,`sortrank`,`issend`,`channeltype`,`moresite`,`ishidden`,`ispart`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__area`;
CREATE TABLE `#@__area` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(20) NOT NULL default '',
  `reid` int(10) unsigned NOT NULL default '0',
  `disorder` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__cache_feedbackurl`;
CREATE TABLE `#@__cache_feedbackurl` (
  `id` int(11) NOT NULL auto_increment,
  `url` varchar(80) NOT NULL default '',
  `title` varchar(80) NOT NULL default '',
  `postnum` int(11) NOT NULL default '0',
  `posttime` int(11) NOT NULL default '0',
  `feedid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `url` (`url`),
  KEY `postnum` (`postnum`,`posttime`,`feedid`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__cache_tagindex`;
CREATE TABLE `#@__cache_tagindex` (
  `id` smallint(6) NOT NULL auto_increment,
  `typeid` smallint(6) NOT NULL default '0',
  `channelid` smallint(6) NOT NULL default '0',
  `uptime` int(11) NOT NULL default '0',
  `hash` char(32) NOT NULL default '-',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__cache_value`;
CREATE TABLE `#@__cache_value` (
  `cid` smallint(11) default '0',
  `value` text NOT NULL,
  KEY `cid` (`cid`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__channeltype`;
CREATE TABLE `#@__channeltype` (
  `ID` smallint(6) NOT NULL default '0',
  `nid` varchar(10) NOT NULL default '',
  `typename` varchar(30) NOT NULL default '',
  `maintable` varchar(20) NOT NULL default '#@__archives ',
  `addtable` varchar(30) NOT NULL default '',
  `addcon` varchar(30) NOT NULL default '',
  `mancon` varchar(30) NOT NULL default '',
  `editcon` varchar(30) NOT NULL default '',
  `useraddcon` varchar(30) NOT NULL default '',
  `usermancon` varchar(30) NOT NULL default '',
  `usereditcon` varchar(30) NOT NULL default '',
  `fieldset` text NOT NULL,
  `listadd` varchar(250) NOT NULL default '',
  `issystem` smallint(6) NOT NULL default '0',
  `isshow` smallint(6) NOT NULL default '1',
  `issend` smallint(6) NOT NULL default '0',
  `arcsta` smallint(6) NOT NULL default '-1',
  `sendrank` smallint(6) NOT NULL default '10',
  `sendmember` int(11) NOT NULL default '0',
  `isdefault` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`ID`),
  KEY `channelIndex` (`nid`,`typename`,`addtable`,`isshow`,`arcsta`,`sendrank`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__co_dataswitch`;
CREATE TABLE `#@__co_dataswitch` (
  `aid` int(11) NOT NULL auto_increment,
  `notename` varchar(100) NOT NULL default '',
  `channelid` int(11) NOT NULL default '0',
  `description` varchar(250) NOT NULL default '',
  `addtime` int(11) NOT NULL default '0',
  `notes` text NOT NULL,
  PRIMARY KEY  (`aid`),
  KEY `notename` (`notename`,`channelid`,`description`,`addtime`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__co_exrule`;
CREATE TABLE `#@__co_exrule` (
  `aid` int(11) NOT NULL auto_increment,
  `channelid` int(11) NOT NULL default '0',
  `rulename` varchar(100) NOT NULL default '',
  `etype` varchar(10) NOT NULL default '',
  `dtime` int(11) NOT NULL default '0',
  `ruleset` text NOT NULL,
  PRIMARY KEY  (`aid`),
  KEY `rulename` (`rulename`,`etype`,`dtime`,`channelid`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__co_listenurl`;
CREATE TABLE `#@__co_listenurl` (
  `nid` int(11) NOT NULL default '0',
  `url` varchar(150) NOT NULL default ''
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__co_mediaurl`;
CREATE TABLE `#@__co_mediaurl` (
  `aid` int(11) NOT NULL auto_increment,
  `nid` int(11) NOT NULL default '0',
  `rurl` varchar(150) NOT NULL default '',
  `nurl` varchar(80) NOT NULL default '',
  PRIMARY KEY  (`aid`),
  KEY `nurl` (`nurl`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__conote`;
CREATE TABLE `#@__conote` (
  `nid` int(11) NOT NULL auto_increment,
  `typeid` int(11) NOT NULL default '0',
  `gathername` varchar(50) NOT NULL default '',
  `language` varchar(10) NOT NULL default 'utf-8',
  `arcsource` varchar(50) NOT NULL,
  `savetime` int(11) NOT NULL default '0',
  `lasttime` int(11) NOT NULL default '0',
  `noteinfo` text NOT NULL,
  PRIMARY KEY  (`nid`),
  KEY `conote` (`typeid`,`gathername`,`lasttime`,`savetime`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__courl`;
CREATE TABLE `#@__courl` (
  `aid` int(11) NOT NULL auto_increment,
  `nid` int(11) NOT NULL default '0',
  `title` varchar(60) NOT NULL default '',
  `url` varchar(150) NOT NULL default '',
  `dtime` int(11) NOT NULL default '0',
  `isdown` smallint(6) NOT NULL default '0',
  `isex` smallint(6) NOT NULL default '0',
  `result` mediumtext NOT NULL,
  PRIMARY KEY  (`aid`),
  KEY `nid` (`nid`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__error`;
CREATE TABLE `#@__error` (
  `aid` int(11) NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `ip` varchar(20) NOT NULL default '',
  `dtime` int(11) NOT NULL default '0',
  `url` varchar(100) NOT NULL default '',
  `info` mediumtext NOT NULL,
  PRIMARY KEY  (`aid`),
  KEY `errIndex` (`title`,`ip`,`dtime`,`url`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__feedback`;
CREATE TABLE `#@__feedback` (
  `ID` int(11) unsigned NOT NULL auto_increment,
  `aid` int(11) unsigned NOT NULL default '0',
  `typeid` int(11) NOT NULL default '0',
  `username` varchar(30) NOT NULL default '',
  `arctitle` varchar(60) NOT NULL default '',
  `urlindex` int(11) NOT NULL default '0',
  `url` varchar(80) NOT NULL default '',
  `ip` varchar(20) NOT NULL default '',
  `msg` text NOT NULL,
  `ischeck` smallint(6) NOT NULL default '0',
  `dtime` int(11) NOT NULL default '0',
  `email` varchar(60) NOT NULL default '',
  `mid` int(11) NOT NULL default '0',
  `rank` smallint(6) NOT NULL default '0',
  `good` int(11) NOT NULL default '0',
  `bad` int(11) NOT NULL default '0',
  `face` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`ID`),
  KEY `mid` (`mid`),
  KEY `feedbackindex` (`aid`,`typeid`,`urlindex`,`ischeck`,`dtime`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__flink`;
CREATE TABLE `#@__flink` (
  `ID` int(11) NOT NULL auto_increment,
  `sortrank` int(11) NOT NULL default '0',
  `url` varchar(100) NOT NULL default '',
  `webname` varchar(30) NOT NULL default '',
  `msg` varchar(250) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `logo` varchar(100) NOT NULL default '',
  `dtime` datetime NOT NULL default '0000-00-00 00:00:00',
  `typeid` int(11) NOT NULL default '0',
  `ischeck` smallint(6) NOT NULL default '1',
  PRIMARY KEY  (`ID`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__flinktype`;
CREATE TABLE `#@__flinktype` (
  `ID` int(11) NOT NULL auto_increment,
  `typename` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`ID`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__freelist`;
CREATE TABLE `#@__freelist` (
  `aid` int(11) NOT NULL auto_increment,
  `title` varchar(50) NOT NULL default '',
  `namerule` varchar(50) NOT NULL default '',
  `listdir` varchar(60) NOT NULL default '',
  `defaultpage` varchar(20) NOT NULL default '',
  `nodefault` smallint(6) NOT NULL default '0',
  `templet` varchar(50) NOT NULL default '',
  `edtime` int(11) NOT NULL default '0',
  `click` int(11) NOT NULL default '1',
  `listtag` mediumtext NOT NULL,
  `keyword` varchar(100) NOT NULL default '',
  `description` varchar(250) NOT NULL default '',
  PRIMARY KEY  (`aid`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__friends`;
CREATE TABLE `#@__friends` (
  `friend_id` int(11) NOT NULL auto_increment,
  `friend_from` bigint(20) NOT NULL default '0',
  `friend_to` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`friend_id`),
  UNIQUE KEY `friends_from_to` (`friend_from`,`friend_to`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__full_search`;
CREATE TABLE `#@__full_search` (
  `aid` int(11) NOT NULL auto_increment,
  `typeid` int(11) NOT NULL default '0',
  `channelid` int(11) NOT NULL default '0',
  `adminid` smallint(6) NOT NULL default '0',
  `mid` int(11) NOT NULL default '0',
  `att` smallint(6) NOT NULL default '0',
  `arcrank` smallint(6) NOT NULL default '0',
  `uptime` int(11) NOT NULL default '0',
  `pubdate` int(11) NOT NULL default '0',
  `title` varchar(80) NOT NULL,
  `url` varchar(100) default NULL,
  `litpic` varchar(100) NOT NULL,
  `keywords` varchar(60) default NULL,
  `addinfos` varchar(250) NOT NULL,
  `digg` int(11) NOT NULL default '0',
  `diggtime` int(11) NOT NULL default '0',
  `click` int(11) default '0',
  `mtype` int(11) NOT NULL default '0',
  PRIMARY KEY  (`aid`),
  KEY `arcindex` (`typeid`,`channelid`,`mid`,`att`,`arcrank`,`adminid`,`mtype`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__guestbook`;
CREATE TABLE `#@__guestbook` (
  `ID` int(11) NOT NULL auto_increment,
  `uname` varchar(30) NOT NULL default '',
  `email` varchar(80) NOT NULL default '',
  `homepage` varchar(80) NOT NULL default '',
  `qq` varchar(20) NOT NULL default '',
  `face` varchar(10) NOT NULL default '',
  `msg` text NOT NULL,
  `ip` varchar(20) NOT NULL default '',
  `dtime` datetime NOT NULL default '0000-00-00 00:00:00',
  `ischeck` smallint(6) NOT NULL default '1',
  PRIMARY KEY  (`ID`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__homepageset`;
CREATE TABLE `#@__homepageset` (
  `templet` varchar(100) NOT NULL default '',
  `position` varchar(50) NOT NULL default ''
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__infos`;
CREATE TABLE `#@__infos` (
  `ID` int(11) unsigned NOT NULL default '0',
  `typeid` int(11) unsigned NOT NULL default '0',
  `typeid2` int(11) unsigned NOT NULL default '0',
  `sortrank` int(11) NOT NULL default '0',
  `iscommend` smallint(6) NOT NULL default '0',
  `ismake` smallint(6) NOT NULL default '0',
  `channel` int(11) NOT NULL default '1',
  `arcrank` smallint(6) NOT NULL default '0',
  `click` int(11) unsigned NOT NULL default '0',
  `money` smallint(6) NOT NULL default '0',
  `title` varchar(80) NOT NULL default '',
  `shorttitle` varchar(36) NOT NULL default '',
  `color` varchar(10) NOT NULL default '',
  `writer` varchar(30) NOT NULL default '',
  `source` varchar(50) NOT NULL default '',
  `litpic` varchar(100) NOT NULL default '',
  `pubdate` int(11) NOT NULL default '0',
  `senddate` int(11) NOT NULL default '0',
  `arcatt` smallint(6) NOT NULL default '0',
  `adminID` int(11) NOT NULL default '0',
  `memberID` int(11) unsigned NOT NULL default '0',
  `description` varchar(250) NOT NULL default '',
  `keywords` varchar(60) NOT NULL default '',
  `templet` varchar(60) NOT NULL default '',
  `lastpost` int(11) NOT NULL default '0',
  `postnum` int(11) NOT NULL default '0',
  `redirecturl` varchar(150) NOT NULL default '',
  `mtype` int(11) NOT NULL default '0',
  `userip` varchar(20) NOT NULL default '',
  `locklikeid` smallint(6) NOT NULL default '0',
  `likeid` varchar(240) default '',
  `smalltypeid` smallint(6) NOT NULL default '0',
  `areaid` smallint(6) NOT NULL default '0',
  `areaid2` smallint(6) NOT NULL default '0',
  `sectorid` smallint(6) NOT NULL default '0',
  `sectorid2` smallint(6) NOT NULL default '0',
  `endtime` int(11) NOT NULL default '0',
  `digg` int(11) NOT NULL default '0',
  `diggtime` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ID`),
  KEY `typeid` (`typeid`,`typeid2`,`sortrank`,`channel`,`arcrank`,`adminID`,`memberID`,`smalltypeid`,`areaid`,`areaid2`,`sectorid`,`sectorid2`,`senddate`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__jobs`;
CREATE TABLE `#@__jobs` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(100) default NULL,
  `job` varchar(100) default NULL,
  `nums` int(10) unsigned NOT NULL default '0',
  `department` varchar(100) default NULL,
  `address` varchar(200) default NULL,
  `pubdate` int(10) unsigned NOT NULL default '0',
  `endtime` int(10) unsigned NOT NULL default '0',
  `salaries` int(10) unsigned NOT NULL default '0',
  `message` mediumtext,
  `memberID` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `memberID` (`memberID`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__keywords`;
CREATE TABLE `#@__keywords` (
  `aid` int(11) NOT NULL auto_increment,
  `keyword` varchar(20) NOT NULL default '',
  `rank` int(11) NOT NULL default '0',
  `sta` smallint(6) NOT NULL default '1',
  `rpurl` varchar(60) NOT NULL default '',
  PRIMARY KEY  (`aid`),
  KEY `keyword` (`keyword`,`rank`,`sta`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__log`;
CREATE TABLE `#@__log` (
  `lid` int(11) NOT NULL auto_increment,
  `adminid` int(11) NOT NULL default '0',
  `filename` varchar(100) NOT NULL default '',
  `method` varchar(10) NOT NULL default '',
  `query` varchar(250) NOT NULL default '',
  `cip` varchar(20) NOT NULL default '',
  `dtime` int(11) NOT NULL default '0',
  PRIMARY KEY  (`lid`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__member`;
CREATE TABLE `#@__member` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `userid` varchar(32) default NULL,
  `pwd` varchar(64) default NULL,
  `spaceurl` varchar(20) default NULL,
  `type` tinyint(3) unsigned NOT NULL default '0',
  `uname` varchar(20) default NULL,
  `sex` char(2) NOT NULL default '',
  `membertype` int(11) NOT NULL default '0',
  `uptime` int(11) NOT NULL default '0',
  `exptime` smallint(6) NOT NULL default '0',
  `money` int(11) NOT NULL default '0',
  `email` varchar(50) NOT NULL default '',
  `jointime` int(11) NOT NULL default '0',
  `joinip` varchar(20) default NULL,
  `logintime` int(11) NOT NULL default '0',
  `loginip` varchar(20) default NULL,
  `c1` int(11) NOT NULL default '0',
  `c2` int(11) NOT NULL default '0',
  `c3` int(11) NOT NULL default '0',
  `matt` smallint(6) NOT NULL default '0',
  `guestbook` int(11) NOT NULL default '0',
  `spaceshow` int(11) NOT NULL default '0',
  `pageshow` int(11) NOT NULL default '0',
  `spacestyle` varchar(20) default NULL,
  `spacename` varchar(50) default NULL,
  `spaceimage` varchar(100) default NULL,
  `news` text NOT NULL,
  `mybb` varchar(250) NOT NULL default '',
  `listnum` smallint(6) NOT NULL default '20',
  `scores` int(10) NOT NULL default '0',
  `newpm` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`ID`),
  KEY `userid` (`sex`,`userid`),
  KEY `setype` (`type`,`membertype`),
  KEY `pagetj` (`money`,`spaceshow`,`pageshow`,`scores`,`newpm`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__member_arctype`;
CREATE TABLE `#@__member_arctype` (
  `aid` int(11) NOT NULL auto_increment,
  `typename` varchar(50) NOT NULL default '',
  `memberid` int(11) NOT NULL default '0',
  `channelid` smallint(6) NOT NULL default '0',
  `rank` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`aid`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__member_cominfo`;
CREATE TABLE `#@__member_cominfo` (
  `id` mediumint(8) unsigned NOT NULL default '0',
  `truename` varchar(20) default NULL,
  `business` varchar(40) default NULL,
  `phone` varchar(12) default NULL,
  `fax` varchar(12) default NULL,
  `mobi` varchar(11) default NULL,
  `comname` varchar(100) default NULL,
  `regyear` varchar(4) default NULL,
  `areaid` smallint(6) unsigned NOT NULL default '0',
  `areaid2` smallint(6) unsigned NOT NULL default '0',
  `service` varchar(255) default NULL,
  `typeid1` smallint(6) unsigned NOT NULL default '0',
  `typeid2` smallint(6) unsigned NOT NULL default '0',
  `comaddr` varchar(100) default NULL,
  `cominfo` mediumtext,
  `postid` varchar(6) default NULL,
  `website` varchar(100) default NULL,
  `culture` mediumtext,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__member_flink`;
CREATE TABLE `#@__member_flink` (
  `aid` int(11) NOT NULL auto_increment,
  `mid` int(11) NOT NULL default '0',
  `title` varchar(30) NOT NULL default '',
  `url` varchar(100) NOT NULL default '',
  `linktype` smallint(6) NOT NULL default '0',
  `imgurl` varchar(100) NOT NULL default '',
  `imgwidth` smallint(6) NOT NULL default '0',
  `imgheight` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`aid`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__member_guestbook`;
CREATE TABLE `#@__member_guestbook` (
  `aid` int(11) NOT NULL auto_increment,
  `mid` int(11) NOT NULL default '0',
  `gid` varchar(20) NOT NULL default '0',
  `title` varchar(60) NOT NULL default '',
  `msg` text NOT NULL,
  `uname` varchar(50) NOT NULL default '',
  `email` varchar(50) NOT NULL default '',
  `qq` varchar(50) NOT NULL default '',
  `tel` varchar(50) NOT NULL default '',
  `ip` varchar(20) NOT NULL default '',
  `dtime` int(11) NOT NULL default '0',
  PRIMARY KEY  (`aid`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__member_operation`;
CREATE TABLE `#@__member_operation` (
  `aid` int(11) NOT NULL auto_increment,
  `buyid` varchar(30) NOT NULL default '',
  `pname` varchar(50) NOT NULL default '',
  `product` varchar(10) NOT NULL default '',
  `money` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `pid` int(11) NOT NULL default '0',
  `mid` int(11) NOT NULL default '0',
  `sta` int(11) NOT NULL default '0',
  `oldinfo` varchar(200) NOT NULL default '',
  PRIMARY KEY  (`aid`),
  KEY `buyid` (`buyid`),
  KEY `pid` (`pid`,`mid`,`sta`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__member_perinfo`;
CREATE TABLE `#@__member_perinfo` (
  `id` int(11) NOT NULL default '0',
  `uname` varchar(20) NOT NULL default '',
  `sex` char(2) NOT NULL default '1',
  `birthday` date NOT NULL default '0000-00-00',
  `weight` varchar(10) NOT NULL default '',
  `height` varchar(10) NOT NULL default '',
  `job` varchar(10) NOT NULL default '',
  `province` smallint(5) unsigned NOT NULL default '1',
  `city` smallint(6) NOT NULL default '0',
  `myinfo` varchar(250) NOT NULL default '',
  `tel` varchar(30) NOT NULL default '',
  `oicq` varchar(15) NOT NULL default '',
  `email` varchar(50) NOT NULL default '',
  `homepage` varchar(50) NOT NULL default '',
  `showaddr` smallint(6) NOT NULL default '0',
  `address` varchar(100) NOT NULL default '',
  `fullinfo` mediumtext,
  PRIMARY KEY  (`id`),
  KEY `sex` (`sex`),
  KEY `birthday` (`birthday`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__member_time`;
CREATE TABLE `#@__member_time` (
  `mday` smallint(6) NOT NULL default '0',
  `tname` varchar(30) NOT NULL default ''
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__member_type`;
CREATE TABLE `#@__member_type` (
  `aid` int(11) NOT NULL auto_increment,
  `rank` int(11) NOT NULL default '0',
  `pname` varchar(50) NOT NULL default '',
  `money` int(11) NOT NULL default '0',
  `exptime` int(11) NOT NULL default '30',
  PRIMARY KEY  (`aid`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__memberstow`;
CREATE TABLE `#@__memberstow` (
  `aid` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL default '0',
  `arcid` int(11) NOT NULL default '0',
  `title` varchar(80) NOT NULL,
  `url` varchar(200) NOT NULL,
  `addtime` int(11) NOT NULL default '0',
  PRIMARY KEY  (`aid`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__modules`;
CREATE TABLE `#@__modules` (
  `id` int(11) NOT NULL auto_increment,
  `modulename` varchar(50) NOT NULL default '-',
  `hash` varchar(50) NOT NULL default '-',
  `filename` varchar(50) NOT NULL default '-',
  `sta` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__moneycard_record`;
CREATE TABLE `#@__moneycard_record` (
  `aid` int(11) NOT NULL auto_increment,
  `ctid` int(11) NOT NULL default '0',
  `cardid` varchar(50) NOT NULL default '',
  `uid` int(11) NOT NULL default '0',
  `isexp` smallint(6) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `utime` int(11) NOT NULL default '0',
  `money` int(11) NOT NULL default '0',
  `num` int(11) NOT NULL default '0',
  PRIMARY KEY  (`aid`),
  KEY `ctid` (`ctid`),
  KEY `cardid` (`cardid`),
  KEY `uid` (`uid`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__moneycard_type`;
CREATE TABLE `#@__moneycard_type` (
  `tid` int(11) NOT NULL auto_increment,
  `num` int(11) NOT NULL default '500',
  `money` int(11) NOT NULL default '50',
  `pname` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`tid`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__moneyrecord`;
CREATE TABLE `#@__moneyrecord` (
  `ID` int(11) NOT NULL auto_increment,
  `aid` int(11) NOT NULL default '0',
  `uid` int(11) NOT NULL default '0',
  `title` varchar(60) NOT NULL default '',
  `money` int(11) NOT NULL default '0',
  `dtime` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ID`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__myad`;
CREATE TABLE `#@__myad` (
  `aid` int(11) NOT NULL auto_increment,
  `typeid` int(11) NOT NULL default '0',
  `tagname` varchar(30) NOT NULL default '',
  `adname` varchar(100) NOT NULL default '',
  `timeset` smallint(6) NOT NULL default '0',
  `starttime` int(11) NOT NULL default '0',
  `endtime` int(11) NOT NULL default '0',
  `normbody` text NOT NULL,
  `expbody` text NOT NULL,
  PRIMARY KEY  (`aid`),
  KEY `tagname` (`tagname`,`adname`,`typeid`,`timeset`,`endtime`,`starttime`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__mynews`;
CREATE TABLE `#@__mynews` (
  `aid` int(11) NOT NULL auto_increment,
  `typeid` int(11) NOT NULL default '0',
  `title` varchar(60) NOT NULL default '',
  `writer` varchar(50) NOT NULL default '',
  `senddate` int(11) NOT NULL default '0',
  `body` text NOT NULL,
  PRIMARY KEY  (`aid`),
  KEY `mynewsindex` (`typeid`,`title`,`writer`,`senddate`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__mytag`;
CREATE TABLE `#@__mytag` (
  `aid` int(11) NOT NULL auto_increment,
  `typeid` int(11) NOT NULL default '0',
  `tagname` varchar(30) NOT NULL default '',
  `timeset` smallint(6) NOT NULL default '0',
  `starttime` int(11) NOT NULL default '0',
  `endtime` int(11) NOT NULL default '0',
  `normbody` text NOT NULL,
  `expbody` text NOT NULL,
  PRIMARY KEY  (`aid`),
  KEY `tagname` (`tagname`,`typeid`,`timeset`,`endtime`,`starttime`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__orders`;
CREATE TABLE `#@__orders` (
  `orderid` int(10) unsigned NOT NULL auto_increment,
  `touid` mediumint(8) unsigned NOT NULL default '0',
  `company` varchar(100) NOT NULL default '',
  `username` varchar(20) NOT NULL default '',
  `phone` varchar(12) NOT NULL default '',
  `fax` varchar(12) NOT NULL default '',
  `email` varchar(50) NOT NULL default '',
  `qq` varchar(50) NOT NULL default '',
  `msn` varchar(50) NOT NULL default '',
  `address` varchar(200) NOT NULL default '',
  `products` varchar(200) NOT NULL default '',
  `nums` int(10) unsigned NOT NULL default '0',
  `content` mediumtext NOT NULL,
  `ip` varchar(15) NOT NULL default '',
  `dateline` int(10) unsigned NOT NULL default '0',
  `status` tinyint(1) unsigned NOT NULL default '0',
  `homepage` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`orderid`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__plus`;
CREATE TABLE `#@__plus` (
  `aid` int(11) NOT NULL auto_increment,
  `plusname` varchar(30) NOT NULL default '',
  `menustring` varchar(150) NOT NULL default '',
  `writer` varchar(60) NOT NULL default '',
  `isshow` smallint(6) NOT NULL default '1',
  `filelist` text NOT NULL,
  PRIMARY KEY  (`aid`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__pms`;
CREATE TABLE `#@__pms` (
  `pmid` int(10) unsigned NOT NULL auto_increment,
  `msgfrom` varchar(15) NOT NULL default '',
  `msgfromid` mediumint(8) unsigned NOT NULL default '0',
  `msgtoid` mediumint(8) unsigned NOT NULL default '0',
  `folder` enum('inbox','outbox','track') NOT NULL default 'inbox',
  `new` tinyint(1) NOT NULL default '0',
  `subject` varchar(75) NOT NULL default '',
  `dateline` int(10) unsigned NOT NULL default '0',
  `message` text NOT NULL,
  `delstatus` tinyint(1) unsigned NOT NULL default '0',
  `isadmin` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`pmid`),
  KEY `msgtoid` (`msgtoid`,`folder`,`dateline`),
  KEY `msgfromid` (`msgfromid`,`folder`,`dateline`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__score2money_logs`;
CREATE TABLE `#@__score2money_logs` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `uid` int(10) unsigned NOT NULL default '0',
  `username` char(32) NOT NULL,
  `dateline` int(10) unsigned NOT NULL default '0',
  `type` enum('score2money','money2score') NOT NULL default 'money2score',
  `ratio` int(10) NOT NULL default '0',
  `score` int(10) NOT NULL default '0',
  `money` int(10) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__scores`;
CREATE TABLE `#@__scores` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `titles` char(15) NOT NULL default '',
  `icon` smallint(6) unsigned default '0',
  `integral` int(10) NOT NULL default '0',
  `isdefault` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `integral` (`integral`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__search_cache`;
CREATE TABLE `#@__search_cache` (
  `cacheid` int(10) unsigned NOT NULL auto_increment,
  `nums` smallint(6) unsigned NOT NULL default '0',
  `md5` char(32) NOT NULL,
  `result` mediumtext NOT NULL,
  `addtime` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`cacheid`),
  UNIQUE KEY `md5` (`md5`),
  KEY `addtime` (`addtime`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__search_keywords`;
CREATE TABLE `#@__search_keywords` (
  `aid` int(11) NOT NULL auto_increment,
  `keyword` varchar(50) NOT NULL default '',
  `spwords` varchar(100) NOT NULL default '',
  `count` int(11) NOT NULL default '1',
  `result` int(11) NOT NULL default '0',
  `lasttime` int(11) NOT NULL default '0',
  `istag` smallint(6) NOT NULL default '0',
  `weekcc` int(11) NOT NULL default '0',
  `monthcc` int(11) NOT NULL default '0',
  `starttime` int(11) NOT NULL default '0',
  `weekup` int(11) NOT NULL default '0',
  `monthup` int(11) NOT NULL default '0',
  PRIMARY KEY  (`aid`),
  KEY `count` (`count`,`result`,`lasttime`,`istag`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__search_rule`;
CREATE TABLE `#@__search_rule` (
  `id` int(11) NOT NULL auto_increment,
  `rulename` varchar(50) default ' ',
  `iscompare` smallint(6) NOT NULL default '1',
  `isuse` smallint(6) NOT NULL default '1',
  `iscache` smallint(6) NOT NULL default '0',
  `exptime` smallint(6) default '24',
  `ruletext` text,
  `uptime` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__sectors`;
CREATE TABLE `#@__sectors` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `reid` int(10) unsigned NOT NULL default '0',
  `disorder` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__sgpage`;
CREATE TABLE `#@__sgpage` (
  `aid` int(11) NOT NULL auto_increment,
  `title` varchar(60) NOT NULL default '',
  `ismake` smallint(6) NOT NULL default '1',
  `filename` varchar(150) NOT NULL default '',
  `uptime` int(11) NOT NULL default '0',
  `body` text NOT NULL,
  PRIMARY KEY  (`aid`),
  KEY `title` (`title`,`ismake`,`filename`,`uptime`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__smalltypes`;
CREATE TABLE `#@__smalltypes` (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '0',
  `disorder` tinyint(3) unsigned NOT NULL default '0',
  `description` varchar(255) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__softconfig`;
CREATE TABLE `#@__softconfig` (
  `downtype` smallint(6) NOT NULL default '0',
  `ismoresite` smallint(6) NOT NULL default '0',
  `gotojump` smallint(6) NOT NULL default '0',
  `showlocal` smallint(6) default '1',
  `sites` text NOT NULL,
  KEY `downtype` (`downtype`,`ismoresite`,`gotojump`,`showlocal`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__sysconfig`;
CREATE TABLE `#@__sysconfig` (
  `aid` int(11) NOT NULL default '0',
  `varname` varchar(20) NOT NULL default '',
  `info` varchar(100) NOT NULL default '',
  `value` varchar(250) NOT NULL default '',
  `type` varchar(10) NOT NULL default 'string',
  `group` int(11) NOT NULL default '1',
  PRIMARY KEY  (`varname`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__syspassport`;
CREATE TABLE `#@__syspassport` (
  `varname` varchar(30) NOT NULL default '0',
  `value` varchar(100) NOT NULL default '0',
  PRIMARY KEY  (`varname`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__tag_index`;
CREATE TABLE `#@__tag_index` (
  `id` int(11) NOT NULL auto_increment,
  `tagname` varchar(30) default NULL,
  `count` int(11) NOT NULL default '0',
  `result` int(11) NOT NULL default '0',
  `weekcc` int(11) NOT NULL default '0',
  `monthcc` int(11) NOT NULL default '0',
  `monthup` int(11) NOT NULL default '0',
  `weekup` int(11) NOT NULL default '0',
  `addtime` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__tag_list`;
CREATE TABLE `#@__tag_list` (
  `tid` int(11) default '0',
  `aid` int(11) NOT NULL default '0',
  `typeid` smallint(6) default '0',
  `arcrank` int(11) NOT NULL default '0',
  KEY `tid` (`tid`,`arcrank`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__task`;
CREATE TABLE `#@__task` (
  `id` smallint(6) NOT NULL default '1',
  `usermtools` smallint(6) NOT NULL default '1',
  `rmpwd` varchar(32) NOT NULL,
  `tasks` varchar(100) NOT NULL,
  `typeid` int(11) NOT NULL default '0',
  `startid` int(11) NOT NULL default '0',
  `endid` int(11) NOT NULL default '0',
  `nodes` varchar(250) NOT NULL,
  `dotime` varchar(10) default '00:00:00',
  `degree` varchar(10) NOT NULL,
  UNIQUE KEY `id` (`id`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__uploads`;
CREATE TABLE `#@__uploads` (
  `aid` int(11) NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `url` varchar(100) NOT NULL default '',
  `mediatype` smallint(6) NOT NULL default '1',
  `width` varchar(10) NOT NULL default '',
  `height` varchar(10) NOT NULL default '',
  `playtime` varchar(10) NOT NULL default '',
  `filesize` int(11) NOT NULL default '0',
  `arcid` int(11) NOT NULL default '0',
  `uptime` int(11) NOT NULL default '0',
  `adminid` int(11) NOT NULL default '0',
  `memberid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`aid`),
  KEY `memberid` (`memberid`,`filesize`,`arcid`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__vote`;
CREATE TABLE `#@__vote` (
  `aid` int(11) NOT NULL auto_increment,
  `votename` varchar(50) NOT NULL default '',
  `starttime` int(11) NOT NULL default '0',
  `endtime` int(11) NOT NULL default '0',
  `totalcount` int(11) NOT NULL default '0',
  `ismore` smallint(6) NOT NULL default '0',
  `votenote` text NOT NULL,
  PRIMARY KEY  (`aid`)
) TYPE=MyISAM;      