<?php
require_once(dirname(__FILE__)."/include/config_base.php");
if(!isset($action)) $action = '';
if(empty($action)){
  require_once(dirname(__FILE__)."/include/inc_digglist_view.php");
  $typeid = (empty($typeid) ? 0 : intval($typeid));
  $sorttype = (empty($sorttype) ? 'time' : preg_replace('/[^a-z]/isU','',$sorttype));
  $dlist = new DiggList($typeid,$sorttype);
  $dlist->Display();
  exit();
}
else if($action=='digg')
{
  $aid = preg_replace('/[^0-9]/sU','',$aid);
  if(empty($aid)) exit();
  header("Pragma:no-cache");
  header("Cache-Control:no-cache");
  header("Expires:0");
	header("Content-Type: text/html; charset=utf-8");
	
	$dsql = new DedeSql(false);
	$tbs = GetChannelTable($dsql,$aid,'arc');
	$dsql->ExecuteNoneQuery("Update `#@__full_search` set digg=digg+1,diggtime=".time()." where aid='$aid' ");
	$dsql->ExecuteNoneQuery("Update `{$tbs['maintable']}` set digg=digg+1,diggtime=".time()." where ID='$aid' ");
	$row = $dsql->GetOne("Select digg From `{$tbs['maintable']}` where ID='$aid' ");
	
  echo "<div class='diggNum'>{$row['digg']}</div>\r\n";
  echo "<div class='diggLink'><a href='".$cfg_cmspath."/digg.php'>浏览</a></div>";
  
	$dsql->Close();
	
	exit();
}
exit();
?>