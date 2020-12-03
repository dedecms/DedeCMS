<?
require("config.php");
if(!eregi("(.*)@(.*)\.(.*)",$email))
{
	echo "<script>alert('Email不正确!');history.go(-1);</script>";
	exit();
}
$msg = ereg_replace("[><]","",$msg);
$mailtitle = "你的好友给你推荐了一篇文章";
$mailbody = "$msg\r\nPower by http://www.dedecms.com 织梦内容管理系统！";
if(eregi("(.*)@(.*)\.(.*)",$email))
{
	  $headers = "From: ".$admin_email."\r\nReply-To: ".$admin_email;
      @mail($email, $mailtitle, $mailbody, $headers);
}
echo "<script>alert('成功推荐一篇文章!');location='$arturl';</script>";
exit();
?>