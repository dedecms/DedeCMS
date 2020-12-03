<?
$page="reg";
require("config.php");
if(ereg(" ",$userid))
{
	echo "<script>alert('用户名不能有空格!');history.go(-1);</script>";
	exit();
}
$dtime = strftime("%Y-%m-%d %H:%M:%S",time());
if(!eregi("(.*)@(.*)\.(.*)",$email))
{
	echo "<script>alert('Email不正确!');history.go(-1);</script>";
	exit();
}
$conn = @connectMySql();
$rs = mysql_query("Select userid From dede_member where userid='$userid' limit 0,1",$conn);
$row = mysql_fetch_object($rs);
if($userid==""||$userid==$row->userid)
{
	echo "<script>alert('用户名已存在!');history.go(-1);</script>";
	exit();
}
if(!isset($_SERVER["REMOTE_ADDR"])) $cip = @getenv("REMOTE_ADDR");
else $cip = $_SERVER["REMOTE_ADDR"];
$query = "INSERT INTO dede_member(userid,pwd,uname,sex,age,birthday,weight,height,job,aera,city,myinfo,mybb,tel,oicq,email,homepage,jointime,joinip,logintime,loginip,rank) VALUES ('$userid','$userpwd','$uname',$sex,'$age','$birthday','$weight','$height','$job',$aera,'$city','$myinfo','$mybb','$tel','$oicq','$email','$homepage','$dtime','$cip','$dtime','$cip',0)"; 
mysql_query($query,$conn);
$myID=mysql_insert_id($conn);
if($myID!=0&&$myID!="")
{
    $tt=time();
    setcookie("cookie_user",$myID,$tt+36000,"/");
	setcookie("cookie_username",$uname,$tt+36000,"/");
	setcookie("cookie_rank","0",$tt+36000,"/");
	setcookie("cookie_isup","0",$tt+36000,"/");
    echo "注册成功，正在转到会员主页....<script>location.href=\"index.php\";</script>";
    exit();	
}
else
{
   echo "<script>alert('注册失败，请查检你的资料是否有问题!');history.go(-1);</script>";
}    
?>