<?
require("config.php");
if(isset($ID))
{
	$conn = connectMySql();
	mysql_query("Delete From dede_partmode where ID=$ID",$conn);
	ShowMsg("成功删除一个板块模板！","web_type_web.php");
	exit();
}
?>