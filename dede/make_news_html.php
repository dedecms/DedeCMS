<?
require("config.php");
require("inc_makeart.php");
if(empty($artids))
{
	ShowMsg("你没选中任何选项！",$ENV_GOBACK_URL);
	exit;
}
$conn = @connectMySql();
$artids=ereg_replace("[^0-9`]","",$artids);
$ids = split("`",$artids);
$j=count($ids);
$mr = new makeArt();
echo "<br>\r\n";
for($i=0;$i<$j;$i++)
{
	$mr->makeArtDone($ids[$i]);
}
echo "<br><br>\r\n";
ShowMsg("成功创建".$i."个文件！",$ENV_GOBACK_URL);
echo "<br><br>\r\n";
exit;
?>