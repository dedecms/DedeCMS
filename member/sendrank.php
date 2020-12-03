<?
include("config.php");
$conn=connectMySql();
mysql_query("update dede_member set isup=$rank where ID=".$_COOKIE["cookie_user"],$conn);
setcookie("cookie_isup",$rank,time()+36000,"/");
echo "申请成功，正在返回会员主页....<script>location.href=\"index.php\";</script>";
exit();	
?>