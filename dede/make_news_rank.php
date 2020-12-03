<?
require("config.php");
if(empty($artids))
{
	ShowMsg("你没选中任何选项！",$ENV_GOBACK_URL);
	exit;
}
if(!empty($tag)&&$tag=="do")
{
	$conn = @connectMySql();
	$ids = split("`",$artids);
	$wherestr = "(";
	$j=count($ids);
	for($i=0;$i<$j;$i++)
	{
		if($i==0) $wherestr.="ID=".ereg_replace("[^0-9]","",$ids[$i]);
		else $wherestr.=" Or ID=".ereg_replace("[^0-9]","",$ids[$i]);
	}
	$wherestr .= ")";
	mysql_query("Update art set rank=$rank where $wherestr",$conn);
	ShowMsg("成功执行指定操作！",$ENV_GOBACK_URL);
	exit;
}
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>更改级别</title>
<link href='base.css' rel='stylesheet' type='text/css'>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="80%" border="0" cellpadding="3" cellspacing="1" bgcolor="#666666" align="center">
  <tr>
    <td height="19"  background="img/tbg.gif"> 自定义文章级别：</td>
</tr>
<tr>
    <td height="100" bgcolor="#FFFFFF">
    <table align="center" width="80%">
    <form name="f1">
    <input type="hidden" name="artids" value="<?=$artids?>">
    <input type="hidden" name="tag" value="do">
    <tr><td height="6"></td></tr>
    <tr><td background="img/tbg.gif">请选择文章的级别：</td></tr>
    <tr><td height="25">
    <?
    $conn = @connectMySql();
    $rs = mysql_query("Select * From dede_membertype where rank>1");
    while($row=mysql_fetch_object($rs))
    {
    	if($row->rank=="0") $checked=" checked";
    	else $checked="";
    ?>
    <input type="radio" name="rank" value="<?=$row->rank?>" class="np"<?=$checked?>><?=$row->membername?>&nbsp;
    <?
    }
    ?>
    </td></tr>
    <tr><td height="35"><input type="submit" name="gg" value="确认更改">&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="gghh" value="不理返回" onClick='location.href="<?=$ENV_GOBACK_URL?>";'></td></tr>
    <tr><td height="20">(有限制类型的文章会被发布为动态形式或PHP文件)</td></tr>
    </form>
    </table>
    </td>
</tr>
</table>
</body>
</html>