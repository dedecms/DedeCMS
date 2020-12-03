<?php
$sql4 = "
DROP TABLE IF EXISTS `#@__groups`;
CREATE TABLE IF NOT EXISTS `#@__groups` (
  `groupid` int(10) unsigned NOT NULL auto_increment,
  `groupname` varchar(75) NOT NULL default '',
  `des` text,
  `groupimg` varchar(200) NOT NULL default '0',
  `rootstoreid` int(10) unsigned NOT NULL default '0',
  `storeid` int(10) unsigned NOT NULL default '0',
  `smalltype` text,
  `uid` int(10) unsigned NOT NULL default '0',
  `creater` char(15) NOT NULL default '',
  `ismaster` text,
  `issystem` tinyint(1) unsigned NOT NULL default '0',
  `isindex` tinyint(1) unsigned NOT NULL default '0',
  `ishidden` tinyint(1) unsigned NOT NULL default '0',
  `hits` int(13) unsigned NOT NULL default '0',
  `threads` int(13) unsigned NOT NULL default '0',
  `members` int(13) unsigned NOT NULL default '0',
  `stime` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`groupid`),
  KEY `uid` (`uid`),
  KEY `stime` (`stime`),
  KEY `storeid` (`storeid`,`rootstoreid`),
  KEY `hits` (`hits`),
  KEY `threads` (`threads`,`members`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__group_guestbook`;
CREATE TABLE IF NOT EXISTS `#@__group_guestbook` (
  `bid` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(80) NOT NULL default '',
  `uname` char(15) NOT NULL default '',
  `userid` int(11) unsigned NOT NULL default '0',
  `gid` int(11) unsigned NOT NULL default '0',
  `stime` int(10) unsigned NOT NULL default '0',
  `message` text NOT NULL,
  `ip` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`bid`),
  KEY `userid` (`userid`,`gid`),
  KEY `sitme` (`stime`)
) TYPE=MyISAM;


DROP TABLE IF EXISTS `#@__group_notice`;
CREATE TABLE IF NOT EXISTS `#@__group_notice` (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `uname` char(15) NOT NULL default '',
  `userid` int(11) unsigned NOT NULL default '0',
  `title` varchar(80) NOT NULL default '',
  `notice` text NOT NULL,
  `stime` int(10) unsigned NOT NULL default '0',
  `gid` mediumint(8) unsigned NOT NULL default '0',
  `ip` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `stime` (`stime`),
  KEY `userid` (`userid`),
  KEY `gid` (`gid`)
) TYPE=MyISAM;


DROP TABLE IF EXISTS `#@__group_posts`;
CREATE TABLE IF NOT EXISTS `#@__group_posts` (
  `pid` int(10) unsigned NOT NULL auto_increment,
  `gid` smallint(6) unsigned NOT NULL default '0',
  `tid` mediumint(8) unsigned NOT NULL default '0',
  `first` tinyint(1) NOT NULL default '0',
  `author` char(15) NOT NULL default '',
  `subject` varchar(80) NOT NULL default '',
  `dateline` int(10) unsigned NOT NULL default '0',
  `message` mediumtext NOT NULL,
  `useip` varchar(15) NOT NULL default '',
  `anonymous` tinyint(1) unsigned NOT NULL default '0',
  `status` tinyint(1) unsigned NOT NULL default '0',
  `authorid` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`pid`),
  KEY `gid` (`gid`,`tid`,`author`),
  KEY `dateline` (`dateline`)
) TYPE=MyISAM;


DROP TABLE IF EXISTS `#@__group_threads`;
CREATE TABLE IF NOT EXISTS `#@__group_threads` (
  `tid` mediumint(8) unsigned NOT NULL auto_increment,
  `gid` smallint(6) unsigned NOT NULL default '0',
  `smalltype` smallint(6) unsigned NOT NULL default '0',
  `subject` char(80) NOT NULL default '',
  `displayorder` tinyint(1) unsigned NOT NULL default '0',
  `author` char(15) NOT NULL default '',
  `authorid` mediumint(8) unsigned NOT NULL default '0',
  `views` int(10) unsigned NOT NULL default '0',
  `dateline` int(10) unsigned NOT NULL default '0',
  `lastpost` int(10) unsigned NOT NULL default '0',
  `lastposter` char(15) NOT NULL default '',
  `replies` mediumint(8) unsigned NOT NULL default '0',
  `digest` tinyint(1) unsigned NOT NULL default '0',
  `closed` mediumint(8) NOT NULL default '0',
  PRIMARY KEY  (`tid`),
  KEY `digest` (`digest`),
  KEY `authorid` (`authorid`,`dateline`),
  KEY `displayorder` (`gid`,`lastpost`,`displayorder`,`smalltype`)
) TYPE=MyISAM;


DROP TABLE IF EXISTS `#@__group_user`;
CREATE TABLE IF NOT EXISTS `#@__group_user` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `uid` int(11) unsigned NOT NULL default '0',
  `username` varchar(15) NOT NULL default '',
  `gid` int(11) unsigned NOT NULL default '0',
  `posts` int(10) unsigned NOT NULL default '0',
  `replies` int(10) unsigned NOT NULL default '0',
  `jointime` int(10) unsigned NOT NULL default '0',
  `isjoin` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `uid` (`uid`,`gid`),
  KEY `jointime` (`jointime`),
  KEY `posts` (`posts`),
  KEY `replies` (`replies`)
) TYPE=MyISAM;


DROP TABLE IF EXISTS `#@__store_groups`;
CREATE TABLE IF NOT EXISTS `#@__store_groups` (
  `storeid` mediumint(8) unsigned NOT NULL auto_increment,
  `storename` char(20) NOT NULL default '',
  `tops` mediumint(8) unsigned NOT NULL default '0',
  `orders` smallint(6) unsigned NOT NULL default '0',
  `nums` int(13) unsigned NOT NULL default '0',
  PRIMARY KEY  (`storeid`),
  KEY `orders` (`orders`,`tops`),
  KEY `nums` (`nums`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__group_smalltypes`;
CREATE TABLE IF NOT EXISTS `#@__group_smalltypes` (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `gid` int(10) unsigned NOT NULL default '0',
  `userid` int(11) unsigned NOT NULL default '0',
  `smalltypes` char(15) NOT NULL,
  `disorder` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `disorder` (`disorder`),
  KEY `userid` (`userid`,`gid`)
) TYPE=MyISAM;

REPLACE INTO `#@__sysconfig` (`aid` , `varname` , `info` , `value` ,`type` ,`group`)
VALUES
(301, 'cfg_group_creators', '圈子模块,要达多该积分数才可以创建圈子', '1000', 'string', 6),
(302, 'cfg_group_max', '用户最多可圈子数量,0不限制.', '0', 'string', 6),
(303, 'cfg_group_maxuser', '圈子可接受用户数量,0不限制', '0', 'string', 6),
(304, 'cfg_group_click', '加入圈子用户是否审核,0不;1要', '1', 'string', 6),
(305, 'cfg_group_words', '圈子一贴子可发字符数', '1000', 'string', 6);
";

$sql41 = "
DROP TABLE IF EXISTS `#@__groups`;
CREATE TABLE IF NOT EXISTS `#@__groups` (
  `groupid` int(10) unsigned NOT NULL auto_increment,
  `groupname` varchar(75) NOT NULL default '',
  `des` text,
  `groupimg` varchar(200) NOT NULL default '0',
  `rootstoreid` int(10) unsigned NOT NULL default '0',
  `storeid` int(10) unsigned NOT NULL default '0',
  `smalltype` text,
  `uid` int(10) unsigned NOT NULL default '0',
  `creater` char(15) NOT NULL default '',
  `ismaster` text,
  `issystem` tinyint(1) unsigned NOT NULL default '0',
  `isindex` tinyint(1) unsigned NOT NULL default '0',
  `ishidden` tinyint(1) unsigned NOT NULL default '0',
  `hits` int(13) unsigned NOT NULL default '0',
  `threads` int(13) unsigned NOT NULL default '0',
  `members` int(13) unsigned NOT NULL default '0',
  `stime` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`groupid`),
  KEY `uid` (`uid`),
  KEY `stime` (`stime`),
  KEY `storeid` (`storeid`,`rootstoreid`),
  KEY `hits` (`hits`),
  KEY `threads` (`threads`,`members`)
) TYPE=MyISAM  DEFAULT CHARSET={$cfg_db_language};

DROP TABLE IF EXISTS `#@__group_guestbook`;
CREATE TABLE IF NOT EXISTS `#@__group_guestbook` (
  `bid` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(80) NOT NULL default '',
  `uname` char(15) NOT NULL default '',
  `userid` int(11) unsigned NOT NULL default '0',
  `gid` int(11) unsigned NOT NULL default '0',
  `stime` int(10) unsigned NOT NULL default '0',
  `message` text NOT NULL,
  `ip` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`bid`),
  KEY `userid` (`userid`,`gid`),
  KEY `sitme` (`stime`)
) TYPE=MyISAM DEFAULT CHARSET={$cfg_db_language};


DROP TABLE IF EXISTS `#@__group_notice`;
CREATE TABLE IF NOT EXISTS `#@__group_notice` (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `uname` char(15) NOT NULL default '',
  `userid` int(11) unsigned NOT NULL default '0',
  `title` varchar(80) NOT NULL default '',
  `notice` text NOT NULL,
  `stime` int(10) unsigned NOT NULL default '0',
  `gid` mediumint(8) unsigned NOT NULL default '0',
  `ip` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `stime` (`stime`),
  KEY `userid` (`userid`),
  KEY `gid` (`gid`)
) TYPE=MyISAM DEFAULT CHARSET={$cfg_db_language};


DROP TABLE IF EXISTS `#@__group_posts`;
CREATE TABLE IF NOT EXISTS `#@__group_posts` (
  `pid` int(10) unsigned NOT NULL auto_increment,
  `gid` smallint(6) unsigned NOT NULL default '0',
  `tid` mediumint(8) unsigned NOT NULL default '0',
  `first` tinyint(1) NOT NULL default '0',
  `author` char(15) NOT NULL default '',
  `subject` varchar(80) NOT NULL default '',
  `dateline` int(10) unsigned NOT NULL default '0',
  `message` mediumtext NOT NULL,
  `useip` varchar(15) NOT NULL default '',
  `anonymous` tinyint(1) unsigned NOT NULL default '0',
  `status` tinyint(1) unsigned NOT NULL default '0',
  `authorid` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`pid`),
  KEY `gid` (`gid`,`tid`,`author`),
  KEY `dateline` (`dateline`)
) TYPE=MyISAM  DEFAULT CHARSET={$cfg_db_language};


DROP TABLE IF EXISTS `#@__group_threads`;
CREATE TABLE IF NOT EXISTS `#@__group_threads` (
  `tid` mediumint(8) unsigned NOT NULL auto_increment,
  `gid` smallint(6) unsigned NOT NULL default '0',
  `smalltype` smallint(6) unsigned NOT NULL default '0',
  `subject` char(80) NOT NULL default '',
  `displayorder` tinyint(1) unsigned NOT NULL default '0',
  `author` char(15) NOT NULL default '',
  `authorid` mediumint(8) unsigned NOT NULL default '0',
  `views` int(10) unsigned NOT NULL default '0',
  `dateline` int(10) unsigned NOT NULL default '0',
  `lastpost` int(10) unsigned NOT NULL default '0',
  `lastposter` char(15) NOT NULL default '',
  `replies` mediumint(8) unsigned NOT NULL default '0',
  `digest` tinyint(1) unsigned NOT NULL default '0',
  `closed` mediumint(8) NOT NULL default '0',
  PRIMARY KEY  (`tid`),
  KEY `digest` (`digest`),
  KEY `authorid` (`authorid`,`dateline`),
  KEY `displayorder` (`gid`,`lastpost`,`displayorder`,`smalltype`)
) TYPE=MyISAM  DEFAULT CHARSET={$cfg_db_language};


DROP TABLE IF EXISTS `#@__group_user`;
CREATE TABLE IF NOT EXISTS `#@__group_user` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `uid` int(11) unsigned NOT NULL default '0',
  `username` varchar(15) NOT NULL default '',
  `gid` int(11) unsigned NOT NULL default '0',
  `posts` int(10) unsigned NOT NULL default '0',
  `replies` int(10) unsigned NOT NULL default '0',
  `jointime` int(10) unsigned NOT NULL default '0',
  `isjoin` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `uid` (`uid`,`gid`),
  KEY `jointime` (`jointime`),
  KEY `posts` (`posts`),
  KEY `replies` (`replies`)
) TYPE=MyISAM  DEFAULT CHARSET={$cfg_db_language};


DROP TABLE IF EXISTS `#@__store_groups`;
CREATE TABLE IF NOT EXISTS `#@__store_groups` (
  `storeid` mediumint(8) unsigned NOT NULL auto_increment,
  `storename` char(20) NOT NULL default '',
  `tops` mediumint(8) unsigned NOT NULL default '0',
  `orders` smallint(6) unsigned NOT NULL default '0',
  `nums` int(13) unsigned NOT NULL default '0',
  PRIMARY KEY  (`storeid`),
  KEY `orders` (`orders`,`tops`),
  KEY `nums` (`nums`)
) TYPE=MyISAM  DEFAULT CHARSET={$cfg_db_language};

DROP TABLE IF EXISTS `#@__group_smalltypes`;
CREATE TABLE IF NOT EXISTS `#@__group_smalltypes` (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `gid` int(10) unsigned NOT NULL default '0',
  `userid` int(11) unsigned NOT NULL default '0',
  `smalltypes` char(15) NOT NULL,
  `disorder` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `disorder` (`disorder`),
  KEY `userid` (`userid`,`gid`)
) TYPE=MyISAM DEFAULT CHARSET={$cfg_db_language};

REPLACE INTO `#@__sysconfig` (`aid` , `varname` , `info` , `value` ,`type` ,`group`)
VALUES
(301, 'cfg_group_creators', '圈子模块,要达多该积分数才可以创建圈子', '1000', 'string', 6),
(302, 'cfg_group_max', '用户最多可圈子数量,0不限制.', '0', 'string', 6),
(303, 'cfg_group_maxuser', '圈子可接受用户数量,0不限制', '0', 'string', 6),
(304, 'cfg_group_click', '加入圈子用户是否审核,0 不,1 要', '1', 'string', 6),
(305, 'cfg_group_words', '圈子一贴子可发字符数', '1000', 'string', 6);
";

$db = new DedeSql(false);
$mysql_version = $db->GetVersion();
if($mysql_version < 4.1) $sqls = explode(';', $sql4);
else $sqls = explode(';', $sql41);
foreach($sqls as $sql){
	if(trim($sql)!='') $db->executenonequery($sql);
}
$db->Close();

//后台菜单
$menuold = '';
$menufile = DEDEROOT.'/dede/inc/inc_menu.php';
$fp = fopen($menufile,'r');
while(!feof($fp)){ $menuold .= fread($fp, 8192); }
fclose($fp);

if(false === strpos($menuold,'圈子管理'))
{
	$menuadd = "~~addmenu~~
	#group_menu_start#<!-- do not modify this line -->
	<m:top name='圈子管理' c='6,' display='block'>
		<m:item name='分类设置' link='group_store.php' rank='group_All' target='main'/>
		<m:item name='圈子列表' link='group_main.php' rank='group_All' target='main'/>
		<m:item name='主题管理' link='group_threads.php' rank='group_All' target='main'/>
	</m:top>
	<!-- do not modify this line -->#group_menu_end#
	";
	$menunew = str_replace('~~addmenu~~',$menuadd,$menuold);
	$errstr = GetBackAlert("写入菜单失败，请检查 /dede/inc 目录是否可写入！",1);
	$fp = fopen($menufile,"w") or die($errstr);
	fwrite($fp,$menunew);
	fclose($fp);
}

//个人会员菜单
$membermenuold = '';
$membermenufile = DEDEROOT.'/member/templets/menu.php';
$fp = fopen($membermenufile,'r');
while(!feof($fp)) $membermenuold .= fread($fp, 8192);
fclose($fp);
if(false === strpos($membermenuold,'圈子管理')){
	$membermenuadd = '<!-- add -->
	<!--#group_menu_start#--><!-- do not modify this line -->
    <div class="manage_company_title">
        <div class="manage_company_title_bg">圈子管理</div>
        <div class="manage_company_main_text">
            <ul>
            <li><a href="mygroup.php">我创建的圈子</a></li>
            <li><a href="myjoin.php">我加入的圈子</a></li>
            </ul>
        </div>
    </div>
	<!-- do not modify this line --><!--#group_menu_end#-->';
	$membermenunew = str_replace('<!-- add -->',$membermenuadd,$membermenuold);
	$errstr = GetBackAlert("写入菜单失败，请检查 /member/templets/menu.php 目录是否可写入！",1);
	$fp = fopen($membermenufile,'w') or die($errstr);
	fwrite($fp,$membermenunew);
	fclose($fp);
}

?>