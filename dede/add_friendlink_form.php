<?
require("config.php");
$conn = connectMySql();
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>友情链接管理</title>
<script language='javascript'>
function CheckSubmit()
{
	if(document.form1.url.value=="http://"||document.form1.url.value=="")
	{
   		document.form1.url.focus();
   		alert("网址不能为空！");
   		return false;
	}
	if(document.form1.fwebname.value=="")
	{
   		document.form1.fwebname.focus();
   		alert("网站名称不能为空！");
   		return false;
	}
	return true;
}
</script>
<link href='base.css' rel='stylesheet' type='text/css'>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#666666">
  <tr>
    <td height="19" background="img/tbg.gif"><b><a href="add_friendlink.php"><u>友情链接管理</u></a></b>&gt;&gt;增加链接</td>
</tr>
<tr>
    <td height="200" bgcolor="#FFFFFF" valign="top">
	<form action="add_friendlink_ok.php" method="post" enctype="multipart/form-data" name="form1" onSubmit="return CheckSubmit();";>
	<table width="80%"  border="0" cellspacing="1" cellpadding="3">
	  <tr>
        <td width="19%" height="25">网址：</td>
        <td width="81%"><input name="url" type="text" id="url" value="http://" size="30"></td>
      </tr>
      <tr>
        <td height="25">网站名称：</td>
        <td><input name="fwebname" type="text" id="fwebname" size="30"></td>
      </tr>
      <tr>
        <td height="25">网站Logo：</td>
        <td><input name="logo" type="text" id="logo" size="30">
          (88*31 gif或jpg)</td>
      </tr>
      <tr>
        <td height="25">上传Logo：</td>
        <td><input name="logoimg" type="file" id="logoimg" size="30"></td>
      </tr>
      <tr>
        <td height="25">网站简况：</td>
        <td><textarea name="msg" cols="50" rows="4" id="msg"></textarea></td>
      </tr>
      <tr>
        <td height="25">站长Email：</td>
        <td><input name="email" type="text" id="email" size="30"></td>
      </tr>
      <tr>
        <td height="25">网站类型：</td>
        <td>
        <select name="typeid" id="typeid">
        <?
        $rs = mysql_query("select * from dede_flinktype",$conn);
        while($row=mysql_fetch_object($rs))
        {
        	echo "	<option value='".$row->ID."'>".$row->typename."</option>\r\n";
        }
        ?>
        </select>
        </td>
      </tr>
      <tr>
        <td height="51">&nbsp;</td>
        <td><input type="submit" name="Submit" value=" 提 交 ">　 　
          <input type="reset" name="Submit" value=" 重 置 "></td>
      </tr>
    </table>
	</form>
    </td>
</tr>
</table>
</body>
</html>