<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Feedback');
if($fid=="")
{
	ShowMsg("你没选中任何选项！",$_COOKIE['ENV_GOBACK_URL'],0,500);
	exit;
}
$dsql = new DedeSql(false);
$fids=ereg_replace("[^0-9`]","",$fid);
$ids = split("`",$fids);
$msg = "";
if($job=="del")
{
	$wherestr = "(";
	$j=count($ids);
	for($i=0;$i<$j;$i++)
	{
		if($i==0) $wherestr.="ID=".$ids[$i];
		else $wherestr.=" Or ID=".$ids[$i];
	}
	$wherestr .= ")";
	$query = "Delete From #@__feedback where $wherestr";
	$dsql->SetQuery($query);
	$dsql->ExecuteNoneQuery();
	$msg = "成功删除指定的评论!";
}
else
{
	$wherestr = "(";
	$j=count($ids);
	for($i=0;$i<$j;$i++)
	{
		if($i==0) $wherestr.="ID=".$ids[$i];
		else $wherestr.=" Or ID=".$ids[$i];
	}
	$wherestr .= ")";
	$query = "update #@__feedback set ischeck=1 where $wherestr";
	$dsql->SetQuery($query);
	$dsql->ExecuteNoneQuery();
	$msg = "成功审核指定评论!";
}
ShowMsg($msg,$_COOKIE['ENV_GOBACK_URL'],0,500);
ClearAllLink();
exit;
?>