<?
include("config.php");
$conn = connectMySql();

if(!empty($_COOKIE["cookie_user"]))	$user = $_COOKIE["cookie_user"];
else $user = -1000;

if(empty($ismodpwd)) $ismodpwd="";

if($ismodpwd=="yes")
{
	$rs = mysql_query("Select pwd From dede_member where ID=$user",$conn);
    if($row = mysql_fetch_object($rs))
    {
    	if($oldpwd==$row->pwd)
    	{
	    	mysql_query("Update dede_member set pwd='$newpwd' where ID=$user",$conn);
    	}
       	else
       	{
	        echo "<script>\n";
			echo "alert('对不起，你的旧密码错误!');\n";
			echo "history.go(-1);\n";
			echo "</script>\n";
			exit();
    	}
    }
}
$sql = "Update dede_member Set email='$email',uname='$uname',sex=sex,age='$age',birthday='$birthday',weight='$weight',height='$height',job='$job',aera=aera,city='$city',myinfo='$myinfo',mybb='$mybb',oicq='$oicq',tel='$tel',homepage='$homepage' where ID=$user";
@mysql_query($sql,$conn);
echo "<script>\n";
echo "alert('更改资料成功!');\n";
echo "location.href='index.php';\n";
echo "</script>\n";
exit();
?>