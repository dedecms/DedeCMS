<?
require("config.php");
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>会员类别管理</title>
<link href='base.css' rel='stylesheet' type='text/css'>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="80%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#666666">
  <tr>
    <td height="19" background="img/tbg.gif" bgcolor="#E7E7E7"> &nbsp;<strong>会员类别管理&nbsp;&nbsp;[<a href="sys_membertype_add.php"><u>增加会员级别</u></a>]</strong></td>
</tr>
<tr>
    <td height="215" valign="top" bgcolor="#FFFFFF"><table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td>&nbsp;&nbsp;&nbsp;&nbsp;等级代码小于或等于 1 的用户为系统默认帐户，不允许删除或更改。</td>
        </tr>
        <tr> 
          <td><hr size="1"></td>
        </tr>
        <tr> 
          <td>系统已有帐号：</td>
        </tr>
        <tr> 
          <td height="55">
		  <?
		  $conn = connectMySql();
		  $rs = mysql_query("Select * From dede_membertype order by rank",$conn);
		  while($row = mysql_fetch_object($rs))
		  {
		  	if($row->rank>1)
		  		echo "<li><a href='del_rank.php?ID=".$row->ID."'>[<u>删除</u>]</a> &nbsp;".$row->membername." 等级代码：".$row->rank."</li>\r\n";
		  	else
		  		echo "<li>".$row->membername." 等级代码：".$row->rank."</li>\r\n";
		  }
		  ?></td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
        </tr>
      </table> </td>
</tr>
</table>
</body>
</html>