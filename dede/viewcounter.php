<?
require("config.php");
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>审核文章</title>
<link href='base.css' rel='stylesheet' type='text/css'>
<script src='menu.js' language='JavaScript'></script>
</head>
<body background='img/allbg.gif' leftmargin='0' topmargin='0'>
<table width='776' border='0' align='center' cellpadding='0' cellspacing='0'>
<tr>
<td height='26' background='img/menubg.gif'>
<table width='776' border='0' cellspacing='0' cellpadding='0'>
<tr>
<td width='12'>&nbsp;</td>
<td width='764'>
<?require("menu.htm");?>
</td>
</tr>
</table></td>
</tr>
<tr> 
<td height='425' align='center' valign='top' bgcolor='#FFFFFF'><table width="70%" border="0" cellspacing="1" cellpadding="0">
<tr>
<td>&nbsp;</td>
</tr>
<tr>
<td align="center" valign="middle">
<table width="50%" border="0" cellpadding="0" cellspacing="1" bgcolor="#E0E0E0">
<tr>
<td height="22" bgcolor="#EFEFEF"><strong>网站统计信息：</strong></td>
</tr>
<?
$conn = connectMySql();
$rs = mysql_query("Select SUM(click) as dd From art",$conn);
$row = mysql_fetch_object($rs);
$dd = $row->dd;
?>
<tr>
<td height="22" align="center" bgcolor="#FFFFFF">
<table width="90%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td>
文章点击：<?=$dd?><td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table> </td>
</tr>
</table>
</body>

</html>
