<?
require("config.php");
$conn = connectMySql();
if(!empty($userid)&&!empty($pwd)&&!empty($usertype))
{
	$rs = mysql_query("Select * from dede_admin where userid='$userid' Or uname='$uname'",$conn);
	$ns = mysql_num_rows($rs);
	if($ns>0)
	{
		echo "<br><br>　";
		ShowMsg("用户名已存在或笔名已存在！","back");
		exit();
	}
	mysql_query("Insert Into dede_admin(usertype,userid,pwd,uname,typeid) values('$usertype','$userid','".md5($pwd)."','$uname',$typeid)",$conn);
	ShowMsg("成功增加一个用户！","sys_manager.php");
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
    <td height="19" background="img/tbg.gif" bgcolor="#E7E7E7"> &nbsp;<strong>管理员帐号--新增帐号</strong>&nbsp;<strong>&nbsp;[<a href="sys_manager.php"><u>管理帐号</u></a>]</strong></td>
</tr>
<tr>
    <td height="215" align="center" valign="top" bgcolor="#FFFFFF">
	<form name="form1">
	<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr> 
          <td height="6"></td>
        </tr>
        <tr> 
          <td height="30">用户登录ID： <input name="userid" type="text" id="userid" size="16">
            （只能用英文字母、@、&quot;.&quot;,&quot;!&quot;,上下划线和数字的组合） </td>
        </tr>
        <tr>
          <td height="30">用户笔名：
            <input name="uname" type="text" id="uname" size="15">
            &nbsp;（发布文章后显示责任编辑的名字）</td>
        </tr>
        <tr> 
          <td height="30">用户密码： <input name="pwd" type="text" id="pwd" size="16"> 
            &nbsp;（MD5单向加密，生成后只可更改，无法查询）</td>
        </tr>
        <tr> 
          <td height="30">用户类型： <input name="usertype" type="radio" value="1" checked>
            信息采编
              <input type="radio" name="usertype" value="5">
              频道编辑
              <input type="radio" name="usertype" value="10">
            超级用户</td>
        </tr>
        <tr>
          <td height="41">（不建议建立多个超级管理员，如果你不想用admin作超级用户名，请新建一个超级用户，然后执行delete from dede where userid='admin'的mysql命令删除原来的超级管理员）</td>
        </tr>
        <tr>
          <td height="41">负责频道：
            <select name="typeid" id="typeid">
              <option value="0" selected>--所有频道--</option>
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