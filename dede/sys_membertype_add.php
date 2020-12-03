<?
require("config.php");
if(!empty($rank)&&!empty($membername))
{
	if($rank<1)
	{
		ShowMsg("禁止设置级别代码小于或等于零的用户！","");
	}
	else
	{
		$conn = connectMySql();
		mysql_query("Insert Into dede_membertype(rank,membername) values('$rank','$membername')",$conn);
		ShowMsg("成功增加一个级别！","sys_membertype.php");
		exit();
	}
}
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>会员类别管理[增加会员级别]</title>
<link href='base.css' rel='stylesheet' type='text/css'>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="90%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#666666">
  <tr>
    <td height="19" background="img/tbg.gif" bgcolor="#E7E7E7"> &nbsp;<strong>会员类别管理[增加会员级别]</strong>&nbsp;<strong>&nbsp;[<a href="sys_membertype.php"><u>管理已有类别</u></a>]</strong></td>
</tr>
<tr>
    <td height="150" align="center" valign="top" bgcolor="#FFFFFF">
	<form name="form1">
	<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr> 
          <td height="6"></td>
        </tr>
        <tr> 
          <td height="30">级别代码： <input name="rank" type="text" id="rank" size="6">
            (必须大于0)</td>
        </tr>
        <tr> 
          <td height="30">级别名称： <input name="membername" type="text" id="membername" size="16"> 
            &nbsp;（前两个字最好能识别类型，如“初级会员”，在管理文章中，只显示出“初级”的字样）</td>
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