<?
require("config.php");
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>查看所有最新信息</title>
<link href='base.css' rel='stylesheet' type='text/css'>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#666666">
  <tr>
    <td height="19" background="img/tbg.gif"><a href="add_news_url.php"><u><strong>新闻信息采编</strong></u></a>&gt;&gt;查看所有最新信息</td>
</tr>
<tr>
    <td height="200" bgcolor="#FFFFFF" valign="top">
    B/S版采编器只捕获源网址的新闻链接，但不自动采编信息，如需要自动采编，请用C/S版的采编器。
    <hr size="1">
	<?
	$conn = connectMySql();
	$rs = mysql_query("Select * From dede_weburl order by ID desc",$conn);
	while($row = mysql_fetch_object($rs))
	{
		$ID = $row->ID;
		$title = $row->title;
		$url = $row->url;
		$msg = $row->msg;
		echo "<li><a href='$url' target='_blank'><u>$title</u></a>　$msg &nbsp;<a href='add_news_url_add.php?job=del&ID=$ID'>[删除]</a></li>\r\n";
	}
	?>
	</td>
</tr>
</table>
</body>
</html>