<?
$page="login";
include("config.php");
include("inc_unit.php");
if(empty($username)) $username="";
if(ereg(" ",$username))
{echo "<script>alert('用户名不能有空格!');history.go(-1);</script>";exit();}
$conn = connectMySql();
@$result = mysql_query("Select ID,pwd,uname,rank,isup From dede_member where userid='$username' limit 0,1",$conn);
@$row = mysql_fetch_object($result);
$uID = $row->ID;
$dpwd = $row->pwd;
$username = $row->uname;
$rank = $row->rank;
$isup = $row->isup;
if($dpwd=="")
{
	errmsg("对不起，不存在这个用户！");
	exit();
	
}
else if($dpwd!=$password)
{
	errmsg("对不起，你的密码错误，请注意大小写！");
	exit();
	
}
else
{
	
	if(!isset($_SERVER["REMOTE_ADDR"])) $cip = @getenv("REMOTE_ADDR");
	else $cip = $_SERVER["REMOTE_ADDR"];
	$tt = time();
	$dtime = strftime("%Y-%m-%d %H:%M:%S",$tt);
	mysql_query("Update member set logintime='$dtime',loginip='$cip' where ID=$uID",$conn);
	setcookie("cookie_user",$uID,$tt+36000,"/");
	setcookie("cookie_username",$username,$tt+36000,"/");
	setcookie("cookie_rank",$rank,$tt+36000,"/");
	setcookie("cookie_isup",$isup,$tt+36000,"/");
	echo "<script>\n";
	echo "alert('登陆成功！\\n$username 欢迎你的光览!');\n";
	if(isset($view)) echo "location.href='$goto';\n";
	else echo "location.href='index.php';\n";
	echo "</script>\n";
}		
?>