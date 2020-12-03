<?
require("config.php");
if($fid=="")
{
	ShowMsg("你没选中任何选项！",$ENV_GOBACK_URL);
	exit;
}
$conn = @connectMySql();
$fids=ereg_replace("[^0-9`]","",$fid);
$ids = split("`",$fids);
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
	mysql_query("Delete From dede_feedback where $wherestr",$conn);
}
else
{
	$j=count($ids);
	for($i=0;$i<$j;$i++)
	{
		mysql_query("update dede_feedback set ischeck=1 where ID=".$ids[$i],$conn);
	}
}
ShowMsg("成功执行指定操作！",$ENV_GOBACK_URL);
exit;
?>