<?
require("config.php");
$conn = connectMySql();
if(!empty($_SERVER["REMOTE_ADDR"])) $ip = $_SERVER["REMOTE_ADDR"];
else $ip = "无法获取";
$dtime = strftime("%Y-%m-%d %H:%M:%S",time());
$uname = trimMsg($uname);
$email = trimMsg($email);
$homepage = trimMsg($homepage);
$homepage = eregi_replace("http://","",$homepage);
$qq = trimMsg($qq);
$msg = trimMsg($msg,1);
$msg = cn_substr($msg,2000);
if($msg==""||$uname=="")
{
	showMsg("你的姓名和留言内容不能为空!",-1);
	exit();
}
$query = "INSERT INTO 
dede_guestbook(uname,email,homepage,qq,face,msg,ip,dtime,ischeck) 
VALUES ('$uname','$email','$homepage','$qq','$img','$msg','$ip','$dtime','1')";
mysql_query($query);
showMsg("成功发送一则留言!","index.php");
?>
