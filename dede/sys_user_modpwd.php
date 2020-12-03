<?
require("config.php");
$conn = connectMySql();
if(empty($ID)) $ID="";
if(empty($job)) $job="";
if(empty($pwd)) $pwd="";
if(empty($usertype)) $usertype="";
if(empty($typeid)) $typeid="";
if(empty($uname)) $uname="";
if($job=="mod")
{
	if($pwd!="")
		$squery = "Update dede_admin set uname='$uname',pwd='".md5(trim($pwd))."',usertype='$usertype',typeid='$typeid' where ID=$ID";
	else
		$squery = "Update dede_admin set uname='$uname',usertype='$usertype',typeid='$typeid' where ID=$ID";
	mysql_query($squery,$conn);
	echo "<script>alert('成功更改一帐号！');</script>";
}
$rs = mysql_query("Select dede_admin.*,dede_arttype.typename From dede_admin left join dede_arttype on dede_admin.typeid=dede_arttype.ID where dede_admin.ID=".$ID,$conn);
$row = mysql_fetch_object($rs);
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
    <td height="19" background="img/tbg.gif"> &nbsp;<strong>管理员帐号--更改帐号</strong>&nbsp;<strong>&nbsp;[<a href="sys_manager.php"><u>管理帐号</u></a>]</strong></td>
</tr>
<tr>
    <td height="215" align="center" valign="top" bgcolor="#FFFFFF">
	<form name="form1" action="sys_user_modpwd.php" method="post">
	<input type="hidden" name="ID" value="<?=$ID?>">
	<input type="hidden" name="job" value="mod">
	<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr> 
          <td height="6"></td>
        </tr>
        <tr> 
          <td height="30">用户登录ID： <?=$row->userid?> </td>
        </tr>
        <tr>
          <td height="30">用户笔名：
            <input name="uname" type="text" id="uname" size="15" value="<?=$row->uname?>">
            &nbsp;（发布文章后显示责任编辑的名字）</td>
        </tr>
        <tr> 
          <td height="30">用户密码： <input name="pwd" type="text" id="pwd" size="16"> 
            &nbsp;（MD5单向加密，只允许更改，无法查询）</td>
        </tr>
        <tr> 
          <td height="30">用户类型：
          <?
          $u10="";
          $u5="";
          $u1="";
          if($row->usertype==10) $u10=" checked";
          if($row->usertype==5) $u5=" checked";
          if($row->usertype==1) $u1=" checked";
          ?> 
          <input name="usertype" type="radio" value="1"<?=$u1?>>
            信息采编
              <input type="radio" name="usertype" value="5"<?=$u5?>>
              频道编辑
              <input type="radio" name="usertype" value="10"<?=$u10?>>
            超级用户</td>
        </tr>
        <tr>
          <td height="41">（不建议建立多个超级管理员，如果你不想用admin作超级用户名，请新建一个超级用户，然后执行delete from dede where userid='admin'的mysql命令删除原来的超级管理员）</td>
        </tr>
        <tr>
          <td height="41">负责频道：
            <select name="typeid" id="typeid">
            <?
            if($row->typeid=="") $typeid_aaa = "0";
            else $typeid_aaa = $row->typeid;
            ////////////////////////////////////////
            if($row->typename=="") $typename_aaa = "所有频道";
            else $typename_aaa = $row->typename;
            ?>
              <option value="<?=$typeid_aaa?>" selected>--<?=$typename_aaa?>--</option>
			  <?
			  $rs = mysql_query("Select * from dede_arttype where reID=0",$conn);
			  while($row=mysql_fetch_object($rs))
			  {
			  		echo "<option value=".$row->ID.">".$row->typename."</option>\r\n";
			  }
			  ?>
            </select>
			</td>
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