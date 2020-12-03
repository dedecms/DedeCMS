<?
require("config.php");
//url,webname,logo,msg,email,typeid
$dtime = strftime("%Y-%m-%d %H:%M:%S",time());
$imgurl = $logo;
$query = "Insert Into dede_flink(url,webname,logo,msg,email,typeid,dtime,ischeck) 
Values('$url','$fwebname','$imgurl','$msg','$email',$typeid,'$dtime',0)";
$conn = connectMySql();
mysql_query($query,$conn);
echo "<script>alert('³É¹¦·¢ËÍÉêÇë!');location='/';</script>";
exit();
?>