<?
require("config.php");
function enStr($str)
{
	$str = str_replace("<","$lt;",$str);
	$str = str_replace("\r","",$str);
	$str = str_replace("\n","<br>\n",$str);
	$str = trim($str);
	$str = str_replace("  ","&nbsp;&nbsp;",$str);
	return($str);
}
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
$ID=ereg_replace("[^0-9]","",$ID);
if($ID==""){exit();}
$sql = "Select dede_member.*,dede_aera.name as aeraname From dede_member left join dede_aera on dede_aera.ID=dede_member.aera where dede_member.ID=$ID";
$conn = connectMySql();
$rs=mysql_query($sql,$conn);
$row=mysql_fetch_object($rs);
$sex=$row->sex;
if($sex=="1") $sex="男";
else $sex="女";
$mypic = $base_dir."/member/upimg/$ID.jpg";
$mypicurl = "/member/upimg/$ID.jpg";
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>会员管理</title>
<link href='base.css' rel='stylesheet' type='text/css'>
<script src='menu.js' language='JavaScript'></script>
</head>
<body background="img/allbg.gif" leftmargin='6' topmargin='6'>
<form name="f1" method="post" action="user_modok.php">
<input type="hidden" name="ID" value="<?=$row->ID?>">
<table width="96%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#333333">
  <tr> 
    <td height="24" align="center" background='img/tbg.gif'><strong>查看会员资料</strong></td>
  </tr>
  <tr> 
    <td bgcolor="#FFFFFF">
    <table width="96%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td width="18%" height="25" align="right">登录ID：</td>
          <td width="82%">&nbsp;<?=$row->userid?></td>
        </tr>
        <tr> 
          <td height="25" align="right">密&nbsp;&nbsp;码：</td>
          <td>&nbsp;<?=$row->pwd?></td>
        </tr>
        <tr> 
          <td height="25" align="right">用户级别：</td>
          <td> 
		  <?
		  $rs2 = mysql_query("select * from dede_membertype where rank=".$row->rank,$conn);
		  $row2 = mysql_fetch_array($rs2);
		  echo $row2["membername"];
		  ?>
		  &nbsp;&nbsp;<a href="user_check.php?ID=<?=$ID?>&nowrank=<?=$row->rank?>">[<u>点击此更改级别</u>]</a></td>
        </tr>
        <tr> 
          <td height="25" colspan="2"><table width="100%" border="0" cellspacing="2" cellpadding="0">
              <tr> 
                <td width="17%" height="25" align="right">Email：</td>
                <td width="83%" height="25"><?=$row->email?>
                </td>
              </tr>
              <tr> 
                <td height="25" align="right">网上昵称：</td>
                <td height="25"><?=$row->uname?>
                  性别： 
                  <input type="radio" name="sex" value="1" <?if($row->sex==1) echo "checked"?>>
                  男 &nbsp; <input type="radio" name="sex" value="0" <?if($row->sex==0) echo "checked"?>>
                  女 </td>
              </tr>
              <tr> 
                <td height="25" colspan="2"> <hr width="80%" size="1" noshade> 
                </td>
              </tr>
              <tr> 
                <td height="25" align="right">年龄：</td>
                <td height="25"><input name="age" type="text" id="age" size="3" value="<?=$row->age?>"> 
                  &nbsp;&nbsp;生日： 
                  <input name="birthday" type="text" id="birthday" size="15" value="<?=$row->birthday?>"> 
                  &nbsp;[&quot;年-月-日&quot;或&quot;月-日&quot;或&quot;X月X日&quot;]</td>
              </tr>
              <tr> 
                <td height="25" align="right">体型：</td>
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
                <td height="25" align="right">职业：</td>
                <td height="25"><input type="radio" name="job" value="学生"<?if($row->job=="学生") echo " checked"?>>
                  学生&nbsp; <input name="job" type="radio" value="职员"<?if($row->job=="职员") echo " checked"?>>
                  职员 
                  <input type="radio" name="job" value="白领"<?if($row->job=="白领") echo " checked"?>>
                  白领 
                  <input type="radio" name="job" value="失业中"<?if($row->job=="失业中") echo " checked"?>>
                  失业中</td>
              </tr>
              <tr> 
                <td height="25" align="right">所在的地区：</td>
                <td height="25">
				<select name="aera" id="aera">
                    <?
$ds=file("../member/aera.txt");
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
                <td height="67"> <input type="submit" name="Submit" value=" 更改资料  "> 
                  &nbsp;&nbsp; <input type="button" name="Submit22" value=" 返回 " onClick="location.href='<?=$ENV_GOBACK_URL?>';"></td>
              </tr>
            </table>
            </td>
        </tr>
      </table></td>
  </tr>
</table>
</form>
</body>
</html>
