<?
require("config.php");
if(empty($job)) $job="";
if(empty($ID)) $ID="";
if($ID!=""&&$job=="yes")
{
	$ID = ereg_replace("[^0-9]","",$ID);
	$conn = connectMySql();
	mysql_query("delete from dede_admin where ID=$ID",$conn);
	ShowMsg("成功删除一个用户！","sys_manager.php");
	exit();
}
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>管理员帐号--新增帐号</title>
<link href='base.css' rel='stylesheet' type='text/css'>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="90%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#666666">
  <tr>
    <td height="19" background="img/tbg.gif"> &nbsp;<strong>管理员帐号--删除用户</strong>&nbsp;<strong>&nbsp;[<a href="sys_manager.php"><u>管理帐号</u></a>]</strong></td>
</tr>
<tr>
    <td align="center" valign="top" bgcolor="#FFFFFF"> 
      <form name="form1">
	<input type="hidden" name="ID" value="<?=$ID?>">
	<input type="hidden" name="job" value="yes">
	    <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr> 
            <td height="6"></td>
          </tr>
          <tr> 
            <td height="30">你确实要删除用户：&nbsp;<?=$userid?>&nbsp; 吗？</td>
          </tr>
          <tr> 
            <td height="41"><input type="submit" name="Submit" value=" 确 认 "></td>
          </tr>
        </table>
	  </form>
	  </td>
</tr>
</table>
</body>
</html>