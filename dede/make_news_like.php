<?
require("config.php");
if($artids=="")
{
	ShowMsg("你没选中任何选项！",$ENV_GOBACK_URL);
	exit;
}
$conn = @connectMySql();
$artids=ereg_replace("[^0-9`]","",$artids);
$ids = split("`",$artids);
$wherestr = "(";
$j=count($ids);
for($i=0;$i<$j;$i++)
{
	if($i==0) $wherestr.="ID=".$ids[$i];
	else $wherestr.=" Or ID=".$ids[$i];
}
$wherestr .= ")";
mysql_query("Update art set likeid='$artids' where $wherestr",$conn);
ShowMsg("成功执行指定操作！",$ENV_GOBACK_URL);
exit;
?>