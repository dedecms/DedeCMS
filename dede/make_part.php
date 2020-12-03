<?
require("config.php");
require("inc_makepartcode.php");
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>更新所有版块</title>
<link href='base.css' rel='stylesheet' type='text/css'>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="90%" border="0" cellpadding="3" cellspacing="1" bgcolor="#666666" align="center">
  <tr>
    <td height="19" background="img/tbg.gif"> &nbsp;<b>更新所有版块</b></td>
</tr>
<tr>
    <td height="242" bgcolor="#FFFFFF">
<?
$conn = connectMySql();
$mp = new MakePartCode();
$rs = mysql_query("Select * From dede_partmode",$conn);
while($row=mysql_fetch_object($rs))
{
	$uname = "/".ereg_replace("^/{1,}","",$row->fname);
	$fname = $base_dir.$uname;
	$body = $row->body;
	$mp->MakeMode($body,$fname);
	echo "<a href='$uname' target='_blank'>".$row->pname."  $uname</a> OK <br>\r\n";
}
?>
    </td>
</tr>
</table>
</body>
</html>