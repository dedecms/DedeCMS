<?php
require(dirname(__FILE__)."/config.php");
CheckPurview('sys_MakeHtml');
$dsql = new DedeSql(false);
$cids = '';
$dsql->SetQuery("Select ID From `#@__channeltype` ");
$dsql->Execute();
while($row = $dsql->GetArray())
{
	$cids .= ($cids=='' ? $row[0] : ','.$row[0]);
}

if($cids!='')
{
  $rs = $dsql->ExecuteNoneQuery("Delete From `#@__arctype` where NOT (channeltype in ($cids));");
  if($rs>0){
  	$dsql->ExecuteNoneQuery("OPTIMIZE TABLE `#@__arctype`;");
  	UpDateCatCache($dsql);
  }

  $rs = $dsql->ExecuteNoneQuery("Delete From `#@__full_search` where  NOT (channeltype in ($cids));");
  if($rs>0) $dsql->ExecuteNoneQuery("OPTIMIZE TABLE `#@__full_search`;");

  $rs = $dsql->ExecuteNoneQuery("Delete From `#@__archives` where  NOT (channel in ($cids));");
  if($rs>0) $dsql->ExecuteNoneQuery("OPTIMIZE TABLE `#@__archives`;");
  
  $rs = $dsql->ExecuteNoneQuery("Delete From `#@__archivesspec` where  NOT (channel in ($cids));");
  if($rs>0) $dsql->ExecuteNoneQuery("OPTIMIZE TABLE `#@__archivesspec`;");

}

ShowMsg("完成所有信息清理！","javascript:;");
ClearAllLink();

?>