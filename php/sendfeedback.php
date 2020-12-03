<?
require("config.php");
if(empty($artID)){ echo "文章ID为空!";exit();}
$conn = connectMySql();
if(empty($notuser)) $notuser="";
else $notuser = $notuser[0];
$username = ereg_replace("['\"\=]","",$username);
if(!isset($_COOKIE["cookie_user"]))
{
	if($notuser!="1"&&isset($username))
	{
		$rs = mysql_query("Select ID,uname,pwd,rank From dede_member where userid='$username'",$conn);
		$row = mysql_fetch_object($rs);
		if($row->ID!="")
		{
			$myID = $row->ID;
			$uname = $row->uname;
			$rank = $row->rank;
			$tt=time();
    		setcookie("cookie_user",$myID,$tt+36000,"/");
			setcookie("cookie_username",$uname,$tt+36000,"/");
			setcookie("cookie_rank",$rank,$tt+36000,"/");
			mysql_free_result($rs);
		}
		else
		{
			$myID= "0";
			$uname = "guest";
		}	
	}
	else
	{
		$myID= "0";
		$uname = "guest";
	}
}
else
{
	$myID = $_COOKIE["cookie_user"];
	$uname = $_COOKIE["cookie_username"];
	$rank = $_COOKIE["cookie_rank"];
}
$artID = ereg_replace("[^0-9]","",$artID);
$msg = cn_substr(trim($msg),1000);
$msg = str_replace("<","&lt;",$msg);
$msg = str_replace(">","&gt;",$msg);
$msg = str_replace("  ","&nbsp;&nbsp;",$msg);
$msg = str_replace("\r\n","<br>\n",$msg);
$msg = trim($msg);
$ip = $_SERVER["REMOTE_ADDR"];
$dtime = strftime("%Y-%m-%d %H:%M:%S",time());
if($msg!="") mysql_query("Insert Into dede_feedback(artID,userid,username,ip,msg,dtime) values('$artID','$myID','$uname','$ip','$msg','$dtime')",$conn);
echo "<br>　　<a href='feedback.php?id=$artID'>成功发表一则评论，请等待审核!点击此<u>查看以往的评论</u></a>";
?>
<script language="javascript">
function gotoView()
{
	location.href="feedback.php?id=<?=$artID?>";
}
setTimeout("gotoView()",3000);
</script>