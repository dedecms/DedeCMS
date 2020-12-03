<?php
$sql4 = "
DROP TABLE IF EXISTS `#@__story_books`;
CREATE TABLE IF NOT EXISTS `#@__story_books` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `catid` smallint(6) default '0',
  `bcatid` smallint(6) NOT NULL default '0',
  `ischeck` smallint(6) NOT NULL default '1',
  `status` tinyint(1) NOT NULL default '0',
  `booktype` smallint(6) NOT NULL default '0',
  `iscommend` smallint(6) NOT NULL default '0',
  `click` int(11) unsigned NOT NULL default '0',
  `freenum` smallint(6) NOT NULL default '0',
  `bookname` varchar(40) NOT NULL default '',
  `author` varchar(30) NOT NULL default '',
  `memberid` int(11) unsigned NOT NULL default '0',
  `adminid` int(11) NOT NULL default '0',
  `litpic` varchar(100) NOT NULL default '',
  `pubdate` int(11) NOT NULL default '0',
  `lastpost` int(11) NOT NULL default '0',
  `postnum` int(11) NOT NULL default '0',
  `lastfeedback` int(11) NOT NULL default '0',
  `feedbacknum` int(11) NOT NULL default '0',
  `weekcc` int(11) NOT NULL default '0',
  `monthcc` int(11) NOT NULL default '0',
  `weekup` int(11) NOT NULL default '0',
  `monthup` int(11) NOT NULL default '0',
  `description` varchar(250) NOT NULL default '',
  `body` mediumtext,
  `keywords` varchar(60) NOT NULL default '',
  `userip` varchar(20) NOT NULL default '',
  `senddate` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `catid` (`catid`,`bcatid`,`ischeck`),
  KEY `click` (`click`,`weekcc`,`monthcc`,`weekup`,`monthup`),
  KEY `booktype` (`booktype`,`iscommend`,`freenum`,`bookname`,`memberid`,`litpic`,`pubdate`,`lastpost`,`postnum`,`senddate`,`adminid`,`author`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__story_catalog`;
CREATE TABLE IF NOT EXISTS `#@__story_catalog` (
  `id` int(11) NOT NULL auto_increment,
  `classname` varchar(30) NOT NULL default '',
  `pid` int(11) NOT NULL default '0',
  `rank` smallint(6) NOT NULL default '0',
  `listrule` varchar(30) NOT NULL default '',
  `viewrule` varchar(30) NOT NULL default '',
  `booktype` smallint(6) NOT NULL default '0',
  `keywords` varchar(50) NOT NULL default '',
  `description` varchar(200) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `classname` (`classname`,`pid`,`rank`,`booktype`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__story_chapter`;
CREATE TABLE IF NOT EXISTS `#@__story_chapter` (
  `id` int(11) NOT NULL auto_increment,
  `bookid` int(11) NOT NULL default '0',
  `catid` int(11) NOT NULL default '0',
  `chapnum` smallint(6) NOT NULL default '1',
  `postnum` smallint(6) NOT NULL default '0',
  `memberid` int(11) NOT NULL default '0',
  `chaptername` varchar(40) NOT NULL default '',
  `bookname` varchar(40) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__story_content`;
CREATE TABLE IF NOT EXISTS `#@__story_content` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(40) NOT NULL default '0',
  `bookname` varchar(40) NOT NULL default '',
  `chapterid` int(11) NOT NULL default '0',
  `bookid` int(11) NOT NULL default '0',
  `catid` smallint(6) NOT NULL default '0',
  `bcatid` smallint(6) NOT NULL default '0',
  `booktype` int(11) NOT NULL default '0',
  `memberid` int(11) NOT NULL default '0',
  `adminid` smallint(6) NOT NULL default '0',
  `addtime` int(11) NOT NULL default '0',
  `sortid` smallint(6) NOT NULL default '0',
  `sortbook` smallint(6) NOT NULL default '0',
  `ischeck` smallint(6) NOT NULL default '1',
  `bigpic` varchar(80) NOT NULL default '',
  `body` mediumtext NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `title` (`sortbook`,`chapterid`,`bookid`,`catid`,`bcatid`,`memberid`,`adminid`,`addtime`,`ischeck`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `#@__story_viphistory`;
CREATE TABLE IF NOT EXISTS `#@__story_viphistory` (
  `cid` int(11) NOT NULL default '0',
  `mid` int(11) NOT NULL default '0'
) TYPE=MyISAM;

REPLACE INTO `#@__sysconfig` (
`aid` ,
`varname` ,
`info` ,
`value` ,
`type` ,
`group`
)
VALUES
(701, 'cfg_book_freenum', '连载内容默认免费章节(-1为全开放)', '6', 'string', 7),
(702, 'cfg_book_pay', '收费图书计费形式(1按图书，2按章节)', '1', 'string', 7),
(703, 'cfg_book_money', '收费图书花费金币数', '1', 'string', 7),
(704, 'cfg_book_freerank', '免费阅读所有内容会员级别值', '100', 'string', 7),
(705, 'cfg_book_ifcheck', '会员发布图书是否需要审核', 'Y', 'bool', 3);
";

$sql41 = "
DROP TABLE IF EXISTS `#@__story_books`;
CREATE TABLE IF NOT EXISTS `#@__story_books` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `catid` smallint(6) default '0',
  `bcatid` smallint(6) NOT NULL default '0',
  `ischeck` smallint(6) NOT NULL default '1',
  `status` tinyint(1) NOT NULL default '0',
  `booktype` smallint(6) NOT NULL default '0',
  `iscommend` smallint(6) NOT NULL default '0',
  `click` int(11) unsigned NOT NULL default '0',
  `freenum` smallint(6) NOT NULL default '0',
  `bookname` varchar(40) NOT NULL default '',
  `author` varchar(30) NOT NULL default '',
  `memberid` int(11) unsigned NOT NULL default '0',
  `adminid` int(11) NOT NULL default '0',
  `litpic` varchar(100) NOT NULL default '',
  `pubdate` int(11) NOT NULL default '0',
  `lastpost` int(11) NOT NULL default '0',
  `postnum` int(11) NOT NULL default '0',
  `lastfeedback` int(11) NOT NULL default '0',
  `feedbacknum` int(11) NOT NULL default '0',
  `weekcc` int(11) NOT NULL default '0',
  `monthcc` int(11) NOT NULL default '0',
  `weekup` int(11) NOT NULL default '0',
  `monthup` int(11) NOT NULL default '0',
  `description` varchar(250) NOT NULL default '',
  `body` mediumtext,
  `keywords` varchar(60) NOT NULL default '',
  `userip` varchar(20) NOT NULL default '',
  `senddate` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `catid` (`catid`,`bcatid`,`ischeck`),
  KEY `click` (`click`,`weekcc`,`monthcc`,`weekup`,`monthup`),
  KEY `booktype` (`booktype`,`iscommend`,`freenum`,`bookname`,`memberid`,`litpic`,`pubdate`,`lastpost`,`postnum`,`senddate`,`adminid`,`author`)
) TYPE=MyISAM  DEFAULT CHARSET={$cfg_db_language};

DROP TABLE IF EXISTS `#@__story_catalog`;
CREATE TABLE IF NOT EXISTS `#@__story_catalog` (
  `id` int(11) NOT NULL auto_increment,
  `classname` varchar(30) NOT NULL default '',
  `pid` int(11) NOT NULL default '0',
  `rank` smallint(6) NOT NULL default '0',
  `listrule` varchar(30) NOT NULL default '',
  `viewrule` varchar(30) NOT NULL default '',
  `booktype` smallint(6) NOT NULL default '0',
  `keywords` varchar(50) NOT NULL default '',
  `description` varchar(200) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `classname` (`classname`,`pid`,`rank`,`booktype`)
) TYPE=MyISAM  DEFAULT CHARSET={$cfg_db_language};

DROP TABLE IF EXISTS `#@__story_chapter`;
CREATE TABLE IF NOT EXISTS `#@__story_chapter` (
  `id` int(11) NOT NULL auto_increment,
  `bookid` int(11) NOT NULL default '0',
  `catid` int(11) NOT NULL default '0',
  `chapnum` smallint(6) NOT NULL default '1',
  `postnum` smallint(6) NOT NULL default '0',
  `memberid` int(11) NOT NULL default '0',
  `chaptername` varchar(40) NOT NULL default '',
  `bookname` varchar(40) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM  DEFAULT CHARSET={$cfg_db_language};

DROP TABLE IF EXISTS `#@__story_content`;
CREATE TABLE IF NOT EXISTS `#@__story_content` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(40) NOT NULL default '0',
  `bookname` varchar(40) NOT NULL default '',
  `chapterid` int(11) NOT NULL default '0',
  `bookid` int(11) NOT NULL default '0',
  `catid` smallint(6) NOT NULL default '0',
  `bcatid` smallint(6) NOT NULL default '0',
  `booktype` int(11) NOT NULL default '0',
  `memberid` int(11) NOT NULL default '0',
  `adminid` smallint(6) NOT NULL default '0',
  `addtime` int(11) NOT NULL default '0',
  `sortid` smallint(6) NOT NULL default '0',
  `sortbook` smallint(6) NOT NULL default '0',
  `ischeck` smallint(6) NOT NULL default '1',
  `bigpic` varchar(80) NOT NULL default '',
  `body` mediumtext NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `title` (`sortbook`,`chapterid`,`bookid`,`catid`,`bcatid`,`memberid`,`adminid`,`addtime`,`ischeck`)
) TYPE=MyISAM  DEFAULT CHARSET={$cfg_db_language};

DROP TABLE IF EXISTS `#@__story_viphistory`;
CREATE TABLE IF NOT EXISTS `#@__story_viphistory` (
  `cid` int(11) NOT NULL default '0',
  `mid` int(11) NOT NULL default '0'
) TYPE=MyISAM DEFAULT CHARSET={$cfg_db_language};

REPLACE INTO `#@__sysconfig` (
`aid` ,
`varname` ,
`info` ,
`value` ,
`type` ,
`group`
)
VALUES
(701, 'cfg_book_freenum', '连载内容默认免费章节(-1为全开放)', '6', 'string', 7),
(702, 'cfg_book_pay', '收费图书计费形式(1按图书，2按章节)', '1', 'string', 7),
(703, 'cfg_book_money', '收费图书花费金币数', '1', 'string', 7),
(704, 'cfg_book_freerank', '免费阅读所有内容会员级别值', '100', 'string', 7),
(705, 'cfg_book_ifcheck', '会员发布图书是否需要审核', 'Y', 'bool', 3);
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
//后台菜单
$menuold = '';
$menufile = DEDEROOT.'/dede/inc/inc_menu.php';
$fp = fopen($menufile,'r');
while(!feof($fp)){ $menuold .= fread($fp, 8192); }
fclose($fp);

if(false === strpos($menuold,'连载管理'))
{
	$menuadd = "~~addmenu~~
	#story_menu_start#<!-- do not modify this line -->
	<m:top name='连载管理' display='block' c='6,' rank=''>
	  <m:item name='连载栏目管理' link='story_catalog.php' rank='story_catalog' target='main' />
	  <m:item name='连载图书' link='story_books.php' rank='story_list' target='main' />
	  <m:item name='连载内容' link='story_list_content.php' rank='story_list' target='main' />
	  <m:item name='章节管理' link='story_list_chapter.php' rank='story_list' target='main' />
	  <m:item name='更新小说HTML' link='makehtml_story.php' rank='sys_MakeHtml' target='main' />
	</m:top>
	<!-- do not modify this line -->#story_menu_end#
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
if(false === strpos($membermenuold,'连载管理')){
	$membermenuadd = '<!-- add -->
	<!--#story_menu_start#--><!-- do not modify this line -->
    <div class="manage_company_title">
        <div class="manage_company_title_bg">连载管理</div>
        <div class="manage_company_main_text">
            <ul>
            <li><a href="story_books.php">连载图书</a></li>
            <li><a href="story_list_content.php">连载内容</a></li>
            <li><a href="story_list_chapter.php">章节管理</a></li>
            </ul>
        </div>
    </div>
	<!-- do not modify this line --><!--#story_menu_end#-->';
	$membermenunew = str_replace('<!-- add -->',$membermenuadd,$membermenuold);
	$errstr = GetBackAlert("写入菜单失败，请检查 /member/templets/menu.php 目录是否可写入！",1);
	$fp = fopen($membermenufile,'w') or die($errstr);
	fwrite($fp,$membermenunew);
	fclose($fp);
}
?>