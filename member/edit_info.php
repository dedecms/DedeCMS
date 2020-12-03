<?
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);
$dsql=new DedeSql();
$row=$dsql->GetOne("select  * from #@__member where ID='".$cfg_ml->M_ID."'");
$dsql->Close();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<title>更改会员资料</title>
<link href="base.css" rel="stylesheet" type="text/css">
<script language='javascript'src='area.js'></script>
<script>
function checkSubmit()
{
if(document.form2.userpwdok.value!=document.form2.userpwd.value)
{
  document.form2.userpwdok.focus();
  alert("两次密码不一致！");
  return false;
}
if(document.form2.email.value=="")
{
  document.form2.email.focus();
  alert("Email不能为空！");
  return false;
}
if(document.form2.uname.value=="")
{
  document.form2.uname.focus();
  alert("用户昵称不能为空！");
  return false;
}
if(document.form2.vdcode.value=="")
{
  document.form2.vdcode.focus();
  alert("验证码不能为空！");
  return false;
}
}
</script>	
</head>
<body leftmargin="0" topmargin="0">
<table  width="760"  border="0"  align="center"  cellpadding="0"  cellspacing="0" >
<tr bgcolor="#FFFFFF" >
<td  height="50" colspan="3" ><img src="img/member.gif"  width="320"  height="46" ></td>
</tr>
<tr>
<td width="17" rowspan="2" bordercolor="#FFFFFF" bgcolor="#808DB5" >&nbsp;</td>
<td bordercolor="#FFFFFF" bgcolor="#808DB5" width="168" >&nbsp;</td>
<td width="575" align="right" bordercolor="#FFFFFF" bgcolor="#FFFFFF" >
<?=$cfg_member_menu?>
</td>
</tr>
<tr>
<td colspan="2" valign="top" ><table width="100%" height="300" border="0" cellpadding="1" cellspacing="1" bgcolor="#000000" >
<tr>
<td height="194" align="center" valign="top" bgcolor="#FFFFFF" >
<table width="98%" border="0" cellspacing="0" cellpadding="0" >
<tr>
<td colspan="2" height="10" ></td>
</tr>
<form name="form2" action="index_do.php" method="post" onSubmit="return checkSubmit();">
<input type="hidden" name="fmdo" value="user" />
<input type="hidden" name="dopost" value="editUser" />
<input type="hidden" name="oldprovince" value="<?=$row['province']?>" />
<input type="hidden" name="oldcity" value="<?=$row['city']?>" />
<tr valign="bottom" >
<td height="21" background="img/tbg.gif" colspan="2" ><strong>　&nbsp;更改个人资料：</strong></td>
</tr>
<tr>
<td width="17%" height="25" align="right" >登陆用户名：</td>
<td width="83%" height="25" >
<?=$row['userid']?>
</td>
</tr>
<tr>
<td height="25" align="right" >原登录密码：</td>
<td height="25" ><input type="password" name="oldpwd" style="width:150;height:20" >
*（不正确不允许更改任何资料）</td>
</tr>
<tr>
<td height="25" align="right" >新密码：</td>
<td height="25" ><input name="userpwd" type="password" id="userpwd" size="18" style="width:150;height:20" >
&nbsp;确认密码：
<input name="userpwdok" type="password" id="userpwdok" value="" size="18" style="width:150;height:20" >
&nbsp;（不更改则留空）</td>
</tr>
<tr>
<td height="25" align="right" >你的Email：</td>
<td height="25" ><input name="email" type="text" id="email" value="<?=$row['email']?>" style="width:150;height:20" >
&nbsp;*</td>
</tr>
<tr>
<td height="25" align="right" >网上昵称：</td>
<td height="25" ><input name="uname" type="text" value="<?=$row['uname']?>" id="uname" size="20" style="width:150;height:20" >
&nbsp;*性别：
<input type="radio" name="sex" value="男"<?if($row['sex']=="男" ) echo" checked" ;?>>
男&nbsp;<input type="radio" name="sex" value="女"<?if($row['sex']=="女" ) echo" checked" ;?>>
女</td>
</tr>
<tr>
<td height="25" align="right" >验证码：</td>
<td height="25" ><table width="200" border="0" cellspacing="0" cellpadding="0" >
<tr>
<td width="84" ><input name="vdcode" type="text" id="vdcode" size="10" ></td>
<td width="116" ><img src='../include/validateimg.php'width='50'height='20'></td>
</tr>
</table></td>
</tr>
<tr>
<td height="25" colspan="2" ><hr width="80%" size="1" noshade>
</td>
</tr>
<tr>
<td height="25" align="right" >你的生日：</td>
<td height="25" ><input name="birthday" type="text" id="birthday" size="20" value="<?=$row['birthday']?>" >
</td>
</tr>
<tr>
<td height="25" align="right" >你的体型：</td>
<td height="25" >
<select name="weight" >
<option value='<?=$row['weight']?>'><?=$row['weight']?></option>
<option value='平均'>平均</option>
<option value='苗条/纤细'>苗条/纤细</option>
<option value='健壮'>健壮</option>
<option value='略胖'>略胖</option>
<option value='大型'>大型</option>
</select>&nbsp;身高：
<input name="height" value="<?=$row['height']?>" type="text" id="height" size="5" >
厘米</td>
</tr>
<tr>
<td height="25" align="right" >你的职业：</td>
<td height="25" ><input type="radio" name="job" value="学生" <?if($row['job']=="学生" ) echo" checked" ;?>>
学生
<input type="radio" name="job" value="职员" <?if($row['job']=="职员" ) echo" checked" ;?>>
职员
<input type="radio" name="job" value="白领" <?if($row['job']=="白领" ) echo" checked" ;?>>
白领
<input type="radio" name="job" value="失业中" <?if($row['job']=="失业中" ) echo" checked" ;?>>
失业中</td>
</tr>
<tr>
<td height="25" align="right" >你所在的地区：</td>
<td height="25" >
<select name="province" size="1" id="province" width="4" onchange="javascript:selNext(this.document.form2.city,this.value)" style="width:85">
<option value="0" selected>--不限--</option>
</select>
<script language='javascript'>
selTop(this.document.form2.province);
</script>&nbsp;城市：
<select id="city" name="city" width="4" style="width:85" >
<option value="0" selected>--不限--</option>
</select>
</td>
</tr>
<tr>
<td height="25" align="right" >自我介绍：</td>
<td height="25" >[少于是125中文字]&nbsp;</td>
</tr>
<tr>
<td height="25" align="right" >&nbsp;</td>
<td height="25" ><textarea name="myinfo" cols="40" rows="3" id="myinfo" ><?=$row['myinfo']?></textarea></td>
</tr>
<tr>
<td height="25" align="right" >个人签名：</td>
<td height="25" >[在论坛中使用，少于是125中文字]</td>
</tr>
<tr>
<td height="25" align="right" >&nbsp;</td>
<td height="25" ><textarea name="mybb" cols="40" rows="3" id="mybb" ><?=$row['mybb']?></textarea></td>
</tr>
<tr>
<td height="25" colspan="2" ><hr width="80%" size="1" noshade></td>
</tr>
<tr>
<td height="25" align="right" >OICQ号码：</td>
<td height="25" ><input name="oicq" type="text" value="<?=$row['oicq']?>" id="oicq" size="20" style="width:150;height:20" >
</td>
</tr>
<tr>
<td height="25" align="right" >联系电话：</td>
<td height="25" ><input name="tel" type="text" value="<?=$row['tel']?>" id="tel" size="20" style="width:150;height:20" >
&nbsp;[本站会员的联系电话一律对外保密]</td>
</tr>
<tr>
<td height="25" align="right" >个人主页：</td>
<td height="25" ><input name="homepage" value="<?=$row['homepage']?>" type="text" id="homepage" size="25" >
</td>
</tr>
<tr>
<td height="67" align="right" >&nbsp;</td>
<td height="67" >
<input type="submit" name="Submit" value="确定修改" >
&nbsp;&nbsp;
<input type="reset" name="Submit22" value="重置" >
</td>
</tr>
</form>
</table></td>
</tr>
</table></td>
</tr>
</table>
<p align='center'>
<?=$cfg_powerby?>
</p>
</body>
</html>
