<?
require("../dede/config_base.php");
/////////////////////////////////
if(empty($page)) $page="";
if($page!="login"&&$page!="reg")
{
	if(empty($_COOKIE["cookie_user"]))
	{
		echo "<script>//alert('你未登录或已经超时，请重新登录!');\r\nlocation.href='login.php';</script>";
		exit();
	}
}
//---获得会员等级-----------
function getRank($conn,$rank)
{
	if($_COOKIE["cookie_isup"]=="0")
	{
	 $rs = mysql_query("select * From dede_membertype where rank=$rank",$conn);
	 $row = mysql_fetch_object($rs);
	 $member = $row->membername;
	 echo "<table width='80%'><form name='form1' action='sendrank.php' method='post'><tr></td>\r\n";
	 echo "你目前的级别是：".$member;
	 $rs = mysql_query("select * From dede_membertype where rank>$rank And rank>1",$conn);
	 if(mysql_num_rows($rs)>0)
	 {
		echo "，我要升级：<select name='rank' style='font-size:9pt;height:18'>";
		while($row = mysql_fetch_object($rs))
		{
			echo "<option value='".$row->rank."'>".$row->membername."</option>\r\n";
		}
		echo "</select> &nbsp;";
		echo "<input type='submit' name='ss' value='确定申请' style='font-size:9pt;height:22'>\r\n";
		echo "</td></tr></form></table>";
	 }
	}
	else
	{
		$rs = mysql_query("select * From dede_membertype where rank=".$_COOKIE["cookie_isup"],$conn);
	 	$row = mysql_fetch_object($rs);
	 	$member = $row->membername;
	 	echo "你目前的状态是：申请升级至——".$member;
	}
}
//获得文章链接
function GetFileName($ID,$typedir,$stime,$rank=0)
{
	global $art_php_dir;
	global $art_dir;
	global $art_shortname;
	global $art_nametag;
	if($rank>0||$rank==-1) return $art_php_dir."/viewart.php?ID=$ID";
	if($art_nametag=="maketime")
	{
		$ds = split("-",$stime);
		return $art_dir."/".$ds[0]."/".$ds[1].$ds[2]."/".$ID.$art_shortname;			
	}
	else
	return $art_dir."/".$typedir."/".$ID.$art_shortname;
}
?>