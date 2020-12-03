<?
require("config.php");
$conn = connectMySQl();
$pname = trim($pname);
//$fname = ereg_replace("\.(htm|html)$","",trim($fname)).".html";
$body = trim($body);
if($pname==""||$fname==""||$body=="")
{
	ShowMsg("所有项目都不能为空！","web_type_web.php#up");
	exit();
}
mysql_query("Insert Into dede_partmode(typeid,pname,fname,body) Values($typeid,'$pname','$fname','$body')",$conn);
ShowMsg("成功上传一个模板！","web_type_web.php#up");
exit();
?>