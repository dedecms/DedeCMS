<?
$page="reg";
require("config.php");
if(ereg(" ",$userid))
{
	echo "<script>alert('用户名不能有空格!');window.close();</script>";
	exit();
}
$conn = @connectMySql();
$rs = mysql_query("Select userid From `dede_member` where userid='$userid' limit 0,1",$conn);
$row = mysql_fetch_object($rs);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>检测用户名</title>
</head>
<body bgcolor="#FBFBF2">
<center>
<p><br>
<?
if($userid=="")
{
	echo "用户名不能为空!";
}
else if($userid==$row->userid)
{
	echo "用户名 <font color='red'>".$userid."</font> 已存在，请使用另外一个!";
}
else
{
	echo "用户名 <font color='red'>".$userid."</font> 尚未被人使用，请放心注册!";
}
?>
<p>
</body>
</html>