<?php
$sql4 = "
DROP TABLE IF EXISTS `#@__ask`;
CREATE TABLE IF NOT EXISTS `#@__ask` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tid` mediumint(8) unsigned NOT NULL default '0',
  `tidname` char(50) NOT NULL default '',
  `tid2` mediumint(8) unsigned NOT NULL default '0',
  `tid2name` char(50) NOT NULL default '',
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `anonymous` tinyint(1) NOT NULL default '0',
  `title` char(80) NOT NULL default '',
  `digest` tinyint(1) NOT NULL default '0',
  `reward` smallint(6) unsigned NOT NULL default '0',
  `dateline` int(10) unsigned NOT NULL default '0',
  `expiredtime` int(10) unsigned NOT NULL default '0',
  `solvetime` int(10) unsigned NOT NULL default '0',
  `bestanswer` int(10) unsigned NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '0',
  `disorder` smallint(6) NOT NULL default '0',
  `views` int(10) unsigned NOT NULL default '0',
  `replies` mediumint(8) unsigned NOT NULL default '0',
  `ip` char(15) NOT NULL default '',
  `content` mediumtext NOT NULL,
  `extra` mediumtext NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `disorder` (`tid`,`tid2`,`status`,`dateline`),
  KEY `digest` (`digest`),
  KEY `expiredtime` (`expiredtime`),
  KEY `reward` (`reward`),
  KEY `uid` (`uid`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__askanswer`;
CREATE TABLE IF NOT EXISTS `#@__askanswer` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `askid` mediumint(8) unsigned NOT NULL default '0',
  `ifanswer` tinyint(1) NOT NULL default '0',
  `tid` smallint(6) unsigned NOT NULL default '0',
  `tid2` smallint(6) unsigned NOT NULL default '0',
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `username` char(32) NOT NULL default '',
  `anonymous` tinyint(1) NOT NULL default '0',
  `goodrate` smallint(6) unsigned NOT NULL default '0',
  `badrate` smallint(6) unsigned NOT NULL default '0',
  `userip` char(15) NOT NULL default '',
  `dateline` int(10) unsigned NOT NULL default '0',
  `brief` char(200) NOT NULL default '',
  `content` mediumtext NOT NULL,
  `ifcheck` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `askid` (`askid`),
  KEY `uid` (`uid`),
  KEY `dateline` (`askid`,`dateline`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__asktype`;
CREATE TABLE IF NOT EXISTS `#@__asktype` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `name` char(50) NOT NULL default '',
  `reid` int(10) unsigned NOT NULL default '0',
  `disorder` int(10) unsigned NOT NULL default '0',
  `asknum` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `disorder` (`disorder`)
) TYPE=MyISAM;

REPLACE INTO `#@__sysconfig` (`aid` ,`varname` ,`info` ,`value` ,`type` ,`group`)
VALUES
(200, 'cfg_ask', '是否启用问答模块', 'Y', 'bool', 6),
(201, 'cfg_ask_ifcheck', '问答模块提问是否需要审核', 'N', 'bool', 6),
(202, 'cfg_ask_dateformat', '问答模块日期格式', 'Y-n-j', 'string', 6),
(203, 'cfg_ask_timeformat', '问答模块时间格式', 'H:i', 'string', 6),
(204, 'cfg_ask_timeoffset', '问答模块时区设定', '8', 'string', 6),
(205, 'cfg_ask_gzipcompress', '是否启用gzip压缩', '1', 'string', 6),
(206, 'cfg_ask_authkey', '问答模块会员key', 'AeN896fG', 'string', 6),
(207, 'cfg_ask_cookiepre', '问答模块cookie前缀', 'deask_', 'string', 6),
(208, 'cfg_answer_ifcheck', '问答模块回答问题是否需要审核', 'N', 'bool', 6),
(209, 'cfg_ask_expiredtime', '问答模块问题有效期（天）', '20', 'string', 6),
(210, 'cfg_ask_tpp', '问答模块列表显示问题数', '14', 'string', 6),
(211, 'cfg_ask_sitename', '问答系统名称', '织梦问答', 'string', 6),
(212, 'cfg_ask_symbols', '问答模块导航间隔符', '>', 'string', 6),
(213, 'cfg_ask_answerscore', ' 会员回答问题就增加积分', '2', 'string', 6),
(214, 'cfg_ask_bestanswer', '最佳答案系统奖励积分', '20', 'string', 6),
(215, 'cfg_ask_subtypenum', '首页显示子类数据', '10', 'string', 6);
";

$sql41 = "
DROP TABLE IF EXISTS `#@__ask`;
CREATE TABLE IF NOT EXISTS `#@__ask` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tid` mediumint(8) unsigned NOT NULL default '0',
  `tidname` char(50) NOT NULL default '',
  `tid2` mediumint(8) unsigned NOT NULL default '0',
  `tid2name` char(50) NOT NULL default '',
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `anonymous` tinyint(1) NOT NULL default '0',
  `title` char(80) NOT NULL default '',
  `digest` tinyint(1) NOT NULL default '0',
  `reward` smallint(6) unsigned NOT NULL default '0',
  `dateline` int(10) unsigned NOT NULL default '0',
  `expiredtime` int(10) unsigned NOT NULL default '0',
  `solvetime` int(10) unsigned NOT NULL default '0',
  `bestanswer` int(10) unsigned NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '0',
  `disorder` smallint(6) NOT NULL default '0',
  `views` int(10) unsigned NOT NULL default '0',
  `replies` mediumint(8) unsigned NOT NULL default '0',
  `ip` char(15) NOT NULL default '',
  `content` mediumtext NOT NULL,
  `extra` mediumtext NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `disorder` (`tid`,`tid2`,`status`,`dateline`),
  KEY `digest` (`digest`),
  KEY `expiredtime` (`expiredtime`),
  KEY `reward` (`reward`),
  KEY `uid` (`uid`)
) TYPE=MyISAM  DEFAULT CHARSET={$cfg_db_language};

DROP TABLE IF EXISTS `#@__askanswer`;
CREATE TABLE IF NOT EXISTS `#@__askanswer` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `askid` mediumint(8) unsigned NOT NULL default '0',
  `ifanswer` tinyint(1) NOT NULL default '0',
  `tid` smallint(6) unsigned NOT NULL default '0',
  `tid2` smallint(6) unsigned NOT NULL default '0',
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `username` char(32) NOT NULL default '',
  `anonymous` tinyint(1) NOT NULL default '0',
  `goodrate` smallint(6) unsigned NOT NULL default '0',
  `badrate` smallint(6) unsigned NOT NULL default '0',
  `userip` char(15) NOT NULL default '',
  `dateline` int(10) unsigned NOT NULL default '0',
  `brief` char(200) NOT NULL default '',
  `content` mediumtext NOT NULL,
  `ifcheck` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `askid` (`askid`),
  KEY `uid` (`uid`),
  KEY `dateline` (`askid`,`dateline`)
) TYPE=MyISAM  DEFAULT CHARSET={$cfg_db_language};

DROP TABLE IF EXISTS `#@__asktype`;
CREATE TABLE IF NOT EXISTS `#@__asktype` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `name` char(50) NOT NULL default '',
  `reid` int(10) unsigned NOT NULL default '0',
  `disorder` int(10) unsigned NOT NULL default '0',
  `asknum` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `disorder` (`disorder`)
) TYPE=MyISAM  DEFAULT CHARSET={$cfg_db_language};

REPLACE INTO `#@__sysconfig` (`aid` ,`varname` ,`info` ,`value` ,`type` ,`group`)
VALUES
(200, 'cfg_ask', '是否启用问答模块', 'Y', 'bool', 6),
(201, 'cfg_ask_ifcheck', '问答模块提问是否需要审核', 'N', 'bool', 6),
(202, 'cfg_ask_dateformat', '问答模块日期格式', 'Y-n-j', 'string', 6),
(203, 'cfg_ask_timeformat', '问答模块时间格式', 'H:i', 'string', 6),
(204, 'cfg_ask_timeoffset', '问答模块时区设定', '8', 'string', 6),
(205, 'cfg_ask_gzipcompress', '是否启用gzip压缩', '1', 'string', 6),
(206, 'cfg_ask_authkey', '问答模块会员key', 'AeN896fG', 'string', 6),
(207, 'cfg_ask_cookiepre', '问答模块cookie前缀', 'deask_', 'string', 6),
(208, 'cfg_answer_ifcheck', '问答模块回答问题是否需要审核', 'N', 'bool', 6),
(209, 'cfg_ask_expiredtime', '问答模块问题有效期（天）', '20', 'string', 6),
(210, 'cfg_ask_tpp', '问答模块列表显示问题数', '14', 'string', 6),
(211, 'cfg_ask_sitename', '问答系统名称', '织梦问答', 'string', 6),
(212, 'cfg_ask_symbols', '问答模块导航间隔符', '>', 'string', 6),
(213, 'cfg_ask_answerscore', ' 会员回答问题就增加积分', '2', 'string', 6),
(214, 'cfg_ask_bestanswer', '最佳答案系统奖励积分', '20', 'string', 6),
(215, 'cfg_ask_subtypenum', '首页显示子类数据', '10', 'string', 6);
";

$db = new DedeSql(false);
$mysql_version = $db->GetVersion();
if($mysql_version < 4.1) $sqls = explode(';', $sql4);
else $sqls = explode(';', $sql41);
foreach($sqls as $sql){
	if(trim($sql)!=''){
		if(!$db->executenonequery($sql));
	}
}
$db->Close();

//后台菜单
$menuold = '';
$menufile = DEDEROOT.'/dede/inc/inc_menu.php';
$fp = fopen($menufile,'r');
while(!feof($fp)){ $menuold .= fread($fp, 8192); }
fclose($fp);

if(false === strpos($menuold,'问答模块管理'))
{
	$menuadd = "~~addmenu~~
	#ask_menu_start#<!-- do not modify this line -->
	<m:top name='问答模块管理' c='6,' display='block' rank=''>
	  <m:item name='问答栏目管理' link='asktype.php' rank='ask_All' target='main' />
	  <m:item name='问答问题管理' link='askadmin.php' rank='ask_All' target='main' />
	  <m:item name='问答答案管理' link='answeradmin.php' rank='ask_All' target='main' />
	</m:top>
	<!-- do not modify this line -->#ask_menu_end#
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
if(false === strpos($membermenuold,'问答管理')){
	$membermenuadd = '<!-- add -->
	<!--#ask_menu_start#--><!-- do not modify this line -->
	<div class="manage_company_title">
	<div class="manage_company_title_bg">问答管理</div>
	<div class="manage_company_main_text">
	<ul>
	<li><a href="myask.php">我的提问</a></li>
	</ul>
	</div>
	</div>
	<!-- do not modify this line --><!--#ask_menu_end#-->';
	$membermenunew = str_replace('<!-- add -->',$membermenuadd,$membermenuold);
	$errstr = GetBackAlert("写入菜单失败，请检查 /member/templets/menu.php 目录是否可写入！",1);
	$fp = fopen($membermenufile,'w') or die($errstr);
	fwrite($fp,$membermenunew);
	fclose($fp);
}

//企业会员菜单
$membermenuold = '';
$membermenufile = DEDEROOT.'/member/templets/commenu.php';
$fp = fopen($membermenufile,'r');
while(!feof($fp)) $membermenuold .= fread($fp, 8192);
fclose($fp);
if(false === strpos($membermenuold,'问答管理')){
	$membermenuadd = '<!-- add -->
	<!--#ask_menu_start#--><!-- do not modify this line -->
	<div class="manage_company_title">
	<div class="manage_company_title_bg">问答管理</div>
	<div class="manage_company_main_text">
	<ul>
	<li><a href="myask.php">我的提问</a></li>
	</ul>
	</div>
	</div>
	<!-- do not modify this line --><!--#ask_menu_end#-->';
	$membermenunew = str_replace('<!-- add -->',$membermenuadd,$membermenuold);
	$errstr = GetBackAlert("写入菜单失败，请检查 /member/templets/commenu.php 目录是否可写入！",1);
	$fp = fopen($membermenufile,'w') or die($errstr);
	fwrite($fp,$membermenunew);
	fclose($fp);
}

?>