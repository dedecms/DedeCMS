<?
require("config.php");
$conn = connectMySql();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>会员资料更改</title>
<link href="../base.css" rel="stylesheet" type="text/css">
<script>
function checkSubmit()
{
if(document.form2.email.value=="")
{
 document.form2.email.focus();
 alert("Email 不能为空！");
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
<?
$result = mysql_query("Select * From dede_member where ID=".$_COOKIE["cookie_user"],$conn);
$row = mysql_fetch_object($result);
?>
<table width="700" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr bgcolor="#FFFFFF"> 
    <td height="30" colspan="4"><img src="img/member.gif" width="320" height="46"></td>
  </tr>
  <tr> 
    <td bordercolor="#FFFFFF" bgcolor="#808DB5" width="30">&nbsp;</td>
    <td bordercolor="#FFFFFF" bgcolor="#808DB5" width="220">&nbsp;</td>
    <td bordercolor="#FFFFFF" bgcolor="#808DB5" width="250">&nbsp;</td>
    <td width="200" align="right"><a href="index.php"><u>管理中心</u></a> <a href="/"><u>网站首页</u></a> 
      <a href="exit.php?job=all"><u>退出登录</u></a></td>
  </tr>
  <tr> 
    <td width="30" bgcolor="#808DB5">&nbsp;</td>
    <td colspan="3" rowspan="2" valign="top"> <table width="100%" height="300" border="0" cellpadding="1" cellspacing="1" bgcolor="#000000">
        <form action="user_modok.php" name="form2" method="POST" onSubmit="return checkSubmit();">
          <tr> 
            <td height="194" align="center" bgcolor="#FFFFFF"> <table width="90%" border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <td height="25" colspan="2"><table width="60%" border="0" cellspacing="1" cellpadding="1">
                      <tr> 
                        <td bgcolor="#CCCCCC"><strong>&nbsp;安全选项</strong> <input name="ismodpwd" type="radio" value="no" checked>
                          不改密码 
                          <input name="ismodpwd" type="radio" value="yes">
                          更改改密码</td>
                      </tr>
                    </table></td>
                </tr>
                <tr> 
                  <td width="18%" height="25" align="right">旧密码：</td>
                  <td width="82%"><input name="oldpwd" type="oldpwd" id="password" size="15"> 
                    &nbsp;*</td>
                </tr>
                <tr> 
                  <td height="25" align="right">新密码：</td>
                  <td><input name="newpwd" type="password" id="newpwd" size="15"> 
                    &nbsp;*</td>
                </tr>
                <tr> 
                  <td height="25" align="right">确认新密码：</td>
                  <td><input name="newpwd2" type="password" id="newpwd2" size="15"> 
                    &nbsp;* &nbsp; <input type="submit" name="Submit" value=" 确定更改 "></td>
                </tr>
                <tr> 
                  <td height="25" colspan="2"><table width="100%" border="0" cellspacing="2" cellpadding="0">
                      <tr> 
                        <td height="25" colspan="2"><table width="60%" border="0" cellspacing="0" cellpadding="0">
                            <tr> 
                              <td bgcolor="#CCCCCC"><strong>&nbsp;资料更改</strong></td>
                            </tr>
                          </table></td>
                      </tr>
                      <tr> 
                        <td width="17%" height="25" align="right">你的Email：</td>
                        <td width="83%" height="25"><input name="email" type="text" id="email" value="<?=$row->email?>">
                          *</td>
                      </tr>
                      <tr> 
                        <td height="25" align="right">你的网上昵称：</td>
                        <td height="25"><input name="uname" type="text" id="uname" size="20" value="<?=$row->uname?>">
                          *&nbsp; 性别： 
                          <input type="radio" name="sex" value="1" <?if($row->sex==1) echo "checked"?>>
                          男 &nbsp; <input type="radio" name="sex" value="0" <?if($row->sex==0) echo "checked"?>>
                          女 </td>
                      </tr>
                      <tr> 
                        <td height="25" colspan="2"> <hr width="80%" size="1" noshade> 
                        </td>
                      </tr>
                      <tr> 
                        <td height="25" align="right">你的年龄：</td>
                        <td height="25"><input name="age" type="text" id="age" size="3" value="<?=$row->age?>"> 
                          &nbsp;&nbsp;生日： 
                          <input name="birthday" type="text" id="birthday" size="15" value="<?=$row->birthday?>"> 
                          &nbsp;[&quot;年-月-日&quot;或&quot;月-日&quot;或&quot;X月X日&quot;]</td>
                      </tr>
                      <tr> 
                        <td height="25" align="right">你的体型：</td>
                        <td height="25"> <select name="weight">
                            <option value='平均'<?if($row->weight=="平均") echo " selected"?>>平均</option>
                            <option value='苗条/纤细'<?if($row->weight=="苗条/纤细") echo " selected"?>>苗条/纤细</option>
                            <option value='健壮'<?if($row->weight=="健壮") echo " selected"?>>健壮</option>
                            <option value='略胖'<?if($row->weight=="略胖") echo " selected"?>>略胖</option>
                            <option value='大型'<?if($row->weight=="大型") echo " selected"?>>大型</option>
                          </select> &nbsp;身高： 
                          <input name="height" type="text" id="height" size="5" value="<?=$row->height?>">
                          厘米</td>
                      </tr>
                      <tr> 
                        <td height="25" align="right">你的职业：</td>
                        <td height="25"><input type="radio" name="job" value="学生"<?if($row->job=="学生") echo " checked"?>>
                          学生&nbsp; <input name="job" type="radio" value="职员"<?if($row->job=="职员") echo " checked"?>>
                          职员 
                          <input type="radio" name="job" value="白领"<?if($row->job=="白领") echo " checked"?>>
                          白领 
                          <input type="radio" name="job" value="失业中"<?if($row->job=="失业中") echo " checked"?>>
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
	if($aa[0]==$row->aera)
	   echo "<option value='".$aa[0]."' selected>".$aa[1]."</option>\r\n";
	else
	   echo "<option value='".$aa[0]."'>".$aa[1]."</option>\r\n";
}
?>
                          </select> &nbsp;城市： 
                          <input name="city" type="text" id="city" size="10" value="<?=$row->city;?>"> 
                          &nbsp;</td>
                      </tr>
                      <tr> 
                        <td height="25" align="right">自我介绍：</td>
                        <td height="25">[少于是125中文字]</td>
                      </tr>
                      <tr> 
                        <td height="25" align="right">&nbsp;</td>
                        <td height="25"><textarea name="myinfo" cols="40" rows="3"><?=$row->myinfo;?></textarea></td>
                      </tr>
                      <tr> 
                        <td height="25" align="right">个人签名：</td>
                        <td height="25">[在论坛中使用，少于是125中文字] </td>
                      </tr>
                      <tr> 
                        <td height="25" align="right">&nbsp;</td>
                        <td height="25"><textarea name="mybb" cols="40" rows="3"><?=$row->mybb;?></textarea></td>
                      </tr>
                      <tr> 
                        <td height="25" colspan="2"> <hr width="80%" size="1" noshade></td>
                      </tr>
                      <tr> 
                        <td height="25" align="right">OICQ号码：</td>
                        <td height="25"><input name="oicq" type="text" size="20" value="<?=$row->oicq?>"></td>
                      </tr>
                      <tr> 
                        <td height="25" align="right">联系电话：</td>
                        <td height="25"><input name="tel" type="text" size="20" value="<?=$row->tel?>"> 
                          &nbsp; [本站会员的联系电话一律对外保密]</td>
                      </tr>
                      <tr> 
                        <td height="25" align="right">个人主页：</td>
                        <td height="25"><input name="homepage" type="text" size="25" value="<?=$row->homepage?>"></td>
                      </tr>
                      <tr> 
                        <td height="67" align="right">&nbsp;</td>
                        <td height="67"> <input type="submit" name="Submit" value=" 确定更改 "> 
                          &nbsp;&nbsp; <input type="reset" name="Submit22" value=" 重 置 "></td>
                      </tr>
                    </table></td>
                </tr>
              </table></td>
          </tr>
        </form>
      </table></td>
  </tr>
  <tr> 
    <td bgcolor="#808DB5">&nbsp;</td>
  </tr>
</table>
<br>
</body>
</html>
