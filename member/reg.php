<?
$page="reg";
require("config.php");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>会员注册</title>
<link href="../base.css" rel="stylesheet" type="text/css">
<script>
function popUpWindow(URLStr, left, top, width, height)
{
  window.open(URLStr, 'popcheckWin', 'toolbar=no,location=no,directories=no,status=no,menub ar=no,scrollbar=no,resizable=no,copyhistory=yes,width='+width+',height='+height+',left='+left+', top='+top+',screenX='+left+',screenY='+top+'');
}
function checkSubmit()
{
if(document.form2.userid.value=="")
{
   document.form2.userid.focus();
   alert("用户名不能为空！");
   return false;
}
if(document.form2.userpwd.value=="")
{
   document.form2.userpwd.focus();
   alert("登陆密码不能为空！");
   return false;
}
if(document.form2.userpwdok.value!=document.form2.userpwd.value)
{
   document.form2.userpwdok.focus();
   alert("两次密码不一致！");
   return false;
}
if(document.form2.email.value=="")
{
   document.form2.email.focus();
   alert("Emailgi 不能为空！");
   return false;
}
if(document.form2.uname.value=="")
{
   document.form2.uname.focus();
   alert("用户昵称不能为空！");
   return false;
}
}
</script>	
</head>
<body leftmargin="0" topmargin="0">
<table width="700" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr bgcolor="#FFFFFF"> 
    <td height="50" colspan="4"><img src="img/reg.gif" width="320" height="46"></td>
  </tr>
  <tr> 
    <td bordercolor="#FFFFFF" bgcolor="#808DB5" width="30">&nbsp;</td>
    <td bordercolor="#FFFFFF" bgcolor="#808DB5" width="220">&nbsp;</td>
    <td bordercolor="#FFFFFF" bgcolor="#808DB5" width="250">&nbsp;</td>
    <td width="200">&nbsp;</td>
  </tr>
  <tr> 
    <td width="30" bgcolor="#808DB5">&nbsp;</td>
    <td colspan="3" rowspan="2" valign="top"> <table width="100%" height="300" border="0" cellpadding="1" cellspacing="1" bgcolor="#000000">
        <tr> 
          <td height="194" align="center" valign="top" bgcolor="#FFFFFF"> <table width="100%" border="0" cellspacing="2" cellpadding="0">
              <tr> 
                <td colspan="2"><strong>注册须知：<br>
                  &nbsp;&nbsp;&nbsp;&nbsp;1、在本站注册的会员，必须遵守《互联网电子公告服务管理规定》，不得在本站发表诽谤他人，侵犯他人隐私，侵犯他人知识产权，传播病毒，政治言论，商业讯息等信息。<br>
                  &nbsp;&nbsp;&nbsp;&nbsp;2、在所有在本站发表的文章，本站都具有最终编辑权，并且保留用于印刷或向第三方发表的权利，如果你的资料不齐全，我们将有权不作任何通知使用你在本站发布的作品。</strong></td>
              </tr>
              <form name="form2" action="regok.php" method="post" onSubmit="return checkSubmit();">
                <input type="hidden" name="view" value="<?=$view?>">
                <tr> 
                  <td height="35" colspan="2"><strong>&nbsp;(带*号的表示为必填项目)</strong></td>
                </tr>
                <tr> 
                  <td width="17%" height="25" align="right">登陆用户名：</td>
                  <td width="83%" height="25"><input name="userid" type="text" id="userid" size="20"> 
                    &nbsp;*&nbsp;&nbsp;<a href="#" onClick="popUpWindow('checkuser.php?userid=' + form2.userid.value,200,200,300,60);">[检查是否已被占用]</a></td>
                </tr>
                <tr> 
                  <td height="25" align="right">登陆密码：</td>
                  <td height="25"><input name="userpwd" type="password" id="userpwd" size="18"> 
                    &nbsp;*&nbsp;确认密码： 
                    <input name="userpwdok" type="password" id="userpwdok" value="" size="18"> 
                    &nbsp;*</td>
                </tr>
                <tr> 
                  <td height="25" align="right">你的Email：</td>
                  <td height="25"><input name="email" type="text" id="email"> 
                    &nbsp;*</td>
                </tr>
                <tr> 
                  <td height="25" align="right">你的网上昵称：</td>
                  <td height="25"><input name="uname" type="text" id="uname" size="20">
                    *&nbsp; 性别： 
                    <input type="radio" name="sex" value="1" checked>
                    男 &nbsp; <input type="radio" name="sex" value="0">
                    女 </td>
                </tr>
                <tr> 
                  <td height="25" colspan="2"> <hr width="80%" size="1" noshade> 
                  </td>
                </tr>
                <tr> 
                  <td height="25" align="right">你的年龄：</td>
                  <td height="25"><input name="age" type="text" id="age" size="3"> 
                    &nbsp;&nbsp;生日： 
                    <input name="birthday" type="text" id="birthday" size="15"> 
                    &nbsp;[&quot;年-月-日&quot;或&quot;月-日&quot;或&quot;X月X日&quot;]</td>
                </tr>
                <tr> 
                  <td height="25" align="right">你的体型：</td>
                  <td height="25"> <select name="weight">
                      <option value='平均' selected>平均</option>
                      <option value='苗条/纤细'>苗条/纤细</option>
                      <option value='健壮'>健壮</option>
                      <option value='略胖'>略胖</option>
                      <option value='大型'>大型</option>
                    </select> &nbsp;身高： 
                    <input name="height" type="text" id="height" size="5">
                    厘米</td>
                </tr>
                <tr> 
                  <td height="25" align="right">你的职业：</td>
                  <td height="25"><input type="radio" name="job" value="学生">
                    学生&nbsp; <input name="job" type="radio" value="职员" checked>
                    职员 
                    <input type="radio" name="job" value="白领">
                    白领 
                    <input type="radio" name="job" value="失业中">
                    失业中</td>
                </tr>
                <tr> 
                  <td height="25" align="right">你所在的地区：</td>
                  <td height="25"> <select name="aera" id="aera">
                      <?
$ds=file("aera.txt");
foreach($ds as $bb)
{
	$aa=split("\|",ereg_replace("[\r\n]","",$bb));
	
	echo "<option value='".$aa[0]."'>".$aa[1]."</option>\r\n";
}
?>
                    </select> &nbsp;城市： 
                    <input name="city" type="text" id="city" size="10"> &nbsp;</td>
                </tr>
                <tr> 
                  <td height="25" align="right">自我介绍：</td>
                  <td height="25">[少于是125中文字]&nbsp;</td>
                </tr>
                <tr> 
                  <td height="25" align="right">&nbsp;</td>
                  <td height="25"><textarea name="myinfo" cols="40" rows="3" id="textarea2"></textarea></td>
                </tr>
                <tr> 
                  <td height="25" align="right">个人签名：</td>
                  <td height="25">[在论坛中使用，少于是125中文字] </td>
                </tr>
                <tr> 
                  <td height="25" align="right">&nbsp;</td>
                  <td height="25"><textarea name="mybb" cols="40" rows="3" id="textarea3"></textarea></td>
                </tr>
                <tr> 
                  <td height="25" colspan="2"> <hr width="80%" size="1" noshade></td>
                </tr>
                <tr> 
                  <td height="25" align="right">OICQ号码：</td>
                  <td height="25"><input name="oicq" type="text" id="birthday3" size="20"></td>
                </tr>
                <tr> 
                  <td height="25" align="right">联系电话：</td>
                  <td height="25"><input name="tel" type="text" id="oicq" size="20"> 
                    &nbsp; [本站会员的联系电话一律对外保密]</td>
                </tr>
                <tr> 
                  <td height="25" align="right">个人主页：</td>
                  <td height="25"><input name="homepage" type="text" id="oicq2" size="25"></td>
                </tr>
                <tr> 
                  <td height="67" align="right">&nbsp;</td>
                  <td height="67"> <input type="submit" name="Submit" value=" 确定注册 "> 
                    &nbsp;&nbsp; <input type="reset" name="Submit22" value=" 重 置 "></td>
                </tr>
              </form>
            </table></td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td bgcolor="#808DB5">&nbsp;</td>
  </tr>
</table>
</body>
</html>
