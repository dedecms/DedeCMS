<?
$page="login";
require("config.php");
$conn = connectMySql();
if(empty($email)) $email="";
if(empty($msg)) $msg="";
$email = str_replace(" ","",$email);
if($email!="")
{
	$rs = mysql_query("Select userid,pwd From dede_member where email='$email' limit 0,1",$conn);
	$row = mysql_fetch_object($rs,$conn);
	$userid=$row->userid;
	$pwd=$row->pwd;
	if($userid=="") $msg = "<br><font color='red'>你的Email不存在数据库中，请重新输入或<a href='/member/reg.php'>[<u>注册新会员</u>]</a></font><br>";
	else 
	{
		$msg = "你的用户名和密码已发送到你的邮箱中，请查收！";
		$mailtitle = "你在 $webname 的用户名和密码";
		$mailbody = "\r\n用户名：'$userid'  密码：'$pwd'\r\n\r\nDede编织梦幻之旅!";
	    if(eregi("(.*)@(.*)\.(.*)",$email))
	     {
	       $headers = "From: $admin_email\r\nReply-To: $admin_email";
           @mail($email, $mailtitle, $mailbody, $headers);
	     }
	}
	
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>会员登录</title>
<link href="../base.css" rel="stylesheet" type="text/css">	
</head>
<body leftmargin="0" topmargin="0">
<table width="700" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr bgcolor="#FFFFFF"> 
    <td height="50" colspan="4"><img src="img/member.gif" width="320" height="46"></td>
  </tr>
  <tr> 
    <td bordercolor="#FFFFFF" bgcolor="#808DB5" width="30">&nbsp;</td>
    <td bordercolor="#FFFFFF" bgcolor="#808DB5" width="220">&nbsp;</td>
    <td bordercolor="#FFFFFF" bgcolor="#808DB5" width="250">&nbsp;</td>
    <td width="200" align="right"><a href='/'><u>返回首页</u></a></td>
  </tr>
  <tr> 
    <td width="30" bgcolor="#808DB5">&nbsp;</td>
    <td colspan="3" rowspan="2" valign="top">
    <table width="100%" height="200" border="0" cellpadding="1" cellspacing="1" bgcolor="#000000">
        <tr> 
          <td height="100" align="center" valign="top" bgcolor="#FFFFFF"><table width="98%" border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td height="6"></td>
              </tr>
              <tr> 
                <td><font color="#333333"> <strong>会员登录：</strong></font></td>
              </tr>
			  <form name='form1' method='POST' action='loginok.php'>
              <tr> 
                <td height="85"> 
                  <table width='100%' border='0' cellspacing='0' cellpadding='0'>   
		              <tr>
                        <td width='51%' height="35">用户名： 
                          <input name='username' type='text' size='12' style='height:18'> &nbsp;密码： 
                        <input name='password' type='password' size='12' style='height:18'> </td>
                      <td width='49%'><input name='imageField2' type='image' src='img/log.gif' width='48' height='18' border='0' class='input_img'>
                          　　<a href='reg.php'>[<u>注册新会员</u>]</a>　　<a href='/'>[<u>返回首页</u>]</a> 
                        </td>
                    </tr>
		          </table>
				  <hr size="1">
				  </td>
              </tr>
			  </form>
			  <form name='form2'>
              <tr> 
                <td height="57">我忘记了我的密码，请输入你的Email： 
                  <input name="email" type="text" id="email">
                  &nbsp; 
                  <input type="submit" name="Submit" value="取回">
                  <br>
                  <?=$msg?>
                  </td>
              </tr>
              </form>
            </table> </td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td bgcolor="#808DB5">&nbsp;</td>
  </tr>
</table>
<p align='center'><a href='http://www.dedecms.com'target='_blank'>Power by DedeCms 织梦内容管理系统</a></p>
</body></html>
