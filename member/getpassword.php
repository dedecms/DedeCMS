<?
$page="login";
include("config.php");
$conn=connectMySql();
$email = str_replace(" ","",$email);
if($email!="")
{
	$rs = mysql_query("Select userid,pwd From dede_member where email='$email' limit 0,1",$conn);
	$row = mysql_fetch_object($rs,$conn);
	$userid=$row->userid;
	$pwd=$row->pwd;
	if($userid=="") $msg = "你的Email不存在数据库中，请<a href='getpassword.php'>重新输入</a>或<a href='reg.php'>新注册</a><br><br>";
	else 
	{
		$msg = "你的用户名和密码已发送到你的邮箱中，请查收！";
		$mailtitle = "你在[".$webname."]的用户名和密码";
		$mailbody = "\r\n用户名：'$userid'  密码：'$pwd'\r\n\r\n，Power by www.dedecms.com 织梦内容管理系统！";
	        if(eregi("(.*)@(.*)\.(.*)",$email))
	        {
	        	 $headers = "From: ".$admin_email."\r\nReply-To: $admin_email";
                         @mail($email, $mailtitle, $mailbody, $headers);
	        }
	}
	
}
?>