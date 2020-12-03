<?
if(is_dir("../setup"))
{
  echo "如果你还没安装本程序,请运行<a href='setup/setup.php'>setup/setup.php</a>,否则请删除这个文件夹!";
  exit();
}
require_once("config_base.php");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>织梦内容管理系统(DedeCms)-登录</title>
</head>
<body style='MARGIN: 0px' bgColor='#ffffff' leftMargin='0' topMargin='0' scroll='no'>
<table width="100%" border="0" cellpadding="0" cellspacing="1">
  <tr>
  <form name="form1" id="form1" action="loginok.php" method="post">
  <input type="hidden" name="gotopage" value="<?if(!empty($gotopage)) echo $gotopage;?>">
    <td align="center" valign="top">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td background="img/mydedebg.gif" height="108"><a href="http://www.dedecms.com" target="_blank"><img src="img/mydede.gif" width="776" height="108" border="0"></a></td>
          </tr>
        </table> 
        <br>
        <table width="300" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#666666">
        <tr> 
          <td height="25" align="center" background="img/tbg.gif" style="font-size:10pt"><strong>登录DedeCms系统</strong></td>
        </tr>
        <tr> 
          <td height="31" bgcolor="#FFFFFF">
          <table width="100%" border="0" cellspacing="1" cellpadding="0">
              <tr> 
                <td colspan="3" height="15"></td>
              </tr>
              <tr> 
                <td>&nbsp;</td>
                <td width="23%" height="24" style="font-size:10pt">用户名:</td>
                <td width="72%" height="24"><input name="userid" type="text" id="userid" style="width:160"></td>
              </tr>
              <tr> 
                <td colspan="3" height="8"></td>
              </tr>
              <tr> 
                <td>&nbsp;</td>
                <td height="24" style="font-size:10pt">密　码:</td>
                <td height="24"><input name="pwd" type="password" id="pwd" style="width:160"></td>
              </tr>
              <tr> 
                <td colspan="3" height="8"></td>
              </tr>
              <tr> 
                <td>&nbsp;</td>
                <td height="24" style="font-size:10pt">验证码:</td>
                <td height="24"><input name="validate" type="text" id="validate" style="width:80"> <img src='validateimg.php' width='50' height='20'></td>
              </tr>
              <tr> 
                <td colspan="3" height="8"></td>
              </tr>
              <tr align="center"> 
                <td height="42" colspan="3"><input type="image" id="sb" src="img/login.gif" width="58" height="24"> 
                  &nbsp;&nbsp; <img src="img/cancel.gif" width="58" height="24" style="cursor:hand" onClick="document.form1.reset();"></td>
              </tr>
              <tr> 
                <td colspan="3" height="10"></td>
              </tr>
            </table></td>
        </tr>
      </table>
      </td>
      </form>
  </tr>
</table>
</body>
</html>
