<?
require("config.php");
$conn = connectMySql();
if(empty($job)) $job="movelist";
$isok="";
if($job=="movelist")
{
	if($typeid!=$gototype)
	{
		if((!IsParent($typeid,$gototype)&&!IsParent($gototype,$typeid))||$gototype==0)
		{
			$isok="操作成功，请更新移动后的类目的列表！";
			mysql_query("Update dede_arttype set reID=$gototype where ID=$typeid",$conn);
		}
		else
		{
			echo "<script>alert('不允许从父级移动到子级目录，只允许子级到更高级或同级或不同父级的情况!');</script>";
			$isok="操作失败：不允许从父级移动到子级目录，只允许子级到更高级或同级或不同父级的情况！";
		}
	}
	else
	{
		$isok="操作失败：移对对象和目标位置相同！";
	}
}
////////////////////////////////
function IsParent($nid,$gtype)
{
	global $conn;
	$rs = mysql_query("select ID,reID from dede_arttype where ID=$nid",$conn);
	$row = mysql_fetch_object($rs);
	if($row->reID==$gtype) return true;
	else if($row->reID==0) return false;
	else return IsParent($row->reID,$gtype);
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>移动栏目</title>
<style type="text/css">
<!--
body {
	background-image: url(img/allbg.gif);
}
-->
</style>
<link href="base.css" rel="stylesheet" type="text/css">
</head>
<body topmargin="8">
<table width="98%"  border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#666666">
  <tr>
    <td width="100%" height="24" colspan="2" background="img/tbg.gif">
    &nbsp;<b><a href="list_type.php"><u>频道管理</u></a>&gt;&gt;移动栏目</b>
    </td>
  </tr>
  <tr>
    <td height="123" colspan="2" bgcolor="#FFFFFF" align="center">
   	<?=$isok?>
   	<br>
   	<br>
   	<input name="Submit11" type="button" id="Submit11" value="返回频道管理页" onClick="location.href='list_type.php';">
    </td>
  </tr>
</table>
</body>
</html>