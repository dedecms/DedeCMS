<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(DEDEADMIN."/inc/inc_catalog_options.php");
require_once(DEDEADMIN."/../include/pub_dedetag.php");
require_once(DEDEADMIN."/inc/inc_archives_functions.php");
if(!isset($cid)) $cid=0;
else $cid = trim(ereg_replace("[^0-9]","",$cid));
if(!isset($channelid)) $channelid=0;
else $channelid = trim(ereg_replace("[^0-9]","",$channelid));

if(!$cid>0 && !$channelid>0)
{
	ShowMsg("你没指定栏目ID或频道ID，不允许访问本页面！","-1");
	exit();
}

$dsql = new DedeSql(false);

if($cid>0)
{
  $query = "Select t.typename as arctypename,c.* From #@__arctype t left join #@__channeltype c on c.ID=t.channeltype where t.ID='$cid' ";
  $cInfos = $dsql->GetOne($query);
  $channelid = $cInfos['ID'];
  $addtable = $cInfos['addtable'];
}
else if($channelid>0)
{
	$query = " Select * From  #@__channeltype where ID='$channelid'";
  $cInfos = $dsql->GetOne($query);
  $channelid = $cInfos['ID'];
  $addtable = $cInfos['addtable'];
}

require_once(dirname(__FILE__)."/templets/archives_add.htm");
ClearAllLink();
?>