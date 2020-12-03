<?php
require_once(dirname(__FILE__)."/../include/common.inc.php");
require_once(DEDEINC."/arc.searchview.class.php");

$pagesize = (isset($pagesize) && is_numeric($pagesize)) ? $pagesize : 10;
$typeid = (isset($typeid) && is_numeric($typeid)) ? $typeid : 0;
$channeltype = (isset($channeltype) && is_numeric($channeltype)) ? $channeltype : 0;
$kwtype = (isset($kwtype) && is_numeric($kwtype)) ? $kwtype : 1;

if(!isset($orderby)) $orderby='';
else $orderby = eregi_replace('[^a-z]','',$orderby);

if(!isset($searchtype)) $searchtype = 'titlekeyword';
else $searchtype = eregi_replace('[^a-z]','',$searchtype);

if(!isset($keyword)) $keyword = '';

$keyword = FilterSearch(stripslashes($keyword));
$keyword = addslashes(cn_substr($keyword,30));

if($cfg_notallowstr !='' && eregi($cfg_notallowstr,$keyword))
{
	ShowMsg("你的搜索关键字中存在非法内容，被系统禁止！","-1");
	exit();
}

if($keyword=='' || strlen($keyword)<2)
{
	ShowMsg('关键字不能小于2个字节！','-1');
	exit();
}

//检查搜索间隔时间
$lockfile = DEDEROOT.'/data/time.lock.inc';
if(!file_exists($lockfile)) {
	$fp = fopen($lockfile,'w');
	flock($fp,1);
	fwrite($fp,time());
	fclose($fp);
}

//开始时间
if(empty($starttime)) $starttime = -1;
else
{
	$starttime = (is_numeric($starttime) ? $starttime : -1);
	if($starttime>0)
	{
	   $dayst = GetMkTime("2008-1-2 0:0:0") - GetMkTime("2008-1-1 0:0:0");
	   $starttime = time() - ($starttime * $dayst);
  }
}

$t1 = ExecTime();

$sp = new SearchView($typeid,$keyword,$orderby,$channeltype,$searchtype,$starttime,$pagesize,$kwtype);
$sp->Display();

//echo ExecTime() - $t1;
?>