<?
require("config.php");
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>管理员帐号</title>
<link href='base.css' rel='stylesheet' type='text/css'>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="80%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#666666">
  <tr>
    <td height="19" background="img/tbg.gif" bgcolor="#E7E7E7"> &nbsp;<strong>管理员帐号&nbsp;&nbsp;[<a href="sys_manager_add.php"><u>增加管理员</u></a>]</strong></td>
</tr>
<tr>
    <td height="215" valign="top" bgcolor="#FFFFFF"><table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td>&nbsp;&nbsp;&nbsp;&nbsp;帐号分级管理员和普通用户两种，普通用户没有执行MySQL命令、管理用户、删除操作、文件浏览权限。</td>
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
		  $rs = mysql_query("Select dede_admin.*,dede_arttype.typename From dede_admin left join dede_arttype on dede_admin.typeid=dede_arttype.ID",$conn);
		  while($row = mysql_fetch_object($rs))
		  {
		  	if($row->usertype==10)
				$line = "<li>".$row->userid." 笔名：". $row->uname ." 级别： 超级管理员)&nbsp;&nbsp;<a href='sys_user_modpwd.php?ID=".$row->ID."'>[<u>更改</u>]</a><br><font color='#888888'>(最后登录时间：".$row->logintime." IP：".$row->loginip.")</font></li>\r\n";		
			else
			{
				if($row->usertype==5) $utype="频道编辑";
				if($row->usertype==1) $utype="信息采编";
				if($row->typename=="") $utypename="所有";
				else $utypename=$row->typename;
				$line = "<li>".$row->userid." <b>笔名：</b>". $row->uname ." <b>级别：</b> $utype  <b>频道：</b>$utypename  &nbsp;&nbsp;<a href='sys_user_modpwd.php?ID=".$row->ID."'>[<u>更改</u>]</a> <a href='sys_user_del.php?ID=".$row->ID."&userid=".$row->userid."'>[<u>删除</u>]</a><br><font color='#888888'>(最后登录时间：".$row->logintime." IP：".$row->loginip.")</font></li>\r\n";
			}
			echo $line;
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