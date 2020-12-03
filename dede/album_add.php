<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(DEDEADMIN."/inc/inc_catalog_options.php");
require_once(DEDEADMIN."/inc/inc_archives_functions.php");

if(empty($channelid)) $channelid=1;
if(empty($cid)) $cid = 0;

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

require_once(dirname(__FILE__)."/templets/album_add.htm");

ClearAllLink();
?>
