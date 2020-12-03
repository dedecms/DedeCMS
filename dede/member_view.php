<?
require(dirname(__FILE__)."/config.php");
CheckPurview('member_Edit');
if(!isset($_COOKIE['ENV_GOBACK_URL'])) $ENV_GOBACK_URL = "";
else $ENV_GOBACK_URL="member_main.php";
$ID = ereg_replace("[^0-9]","",$ID);
$dsql = new DedeSql(false);
$row=$dsql->GetOne("select  * from #@__member where ID='$ID'");
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>查看会员</title>
<link href='base.css' rel='stylesheet' type='text/css'>
<script language='javascript'src='area.js'></script>
<script>
function checkSubmit()
{
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
}
</script>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#666666">
  <tr>
    <td height="19" background="img/tbg.gif"><a href='<?=$ENV_GOBACK_URL?>'><b>会员管理</b></a>&gt;&gt;查看会员</td>
</tr>
<tr>
<td height="200" bgcolor="#FFFFFF" valign="top"><table width="98%" border="0" cellspacing="0" cellpadding="0" >
        <tr> 
          <td colspan="2" height="10" ></td>
        </tr>
        <form name="form2" action="member_do.php" method="post" onSubmit="return checkSubmit();">
          <input type="hidden" name="dopost" value="edituser" />
          <input type="hidden" name="ID" value="<?=$ID?>" />
          <tr> 
            <td width="17%" height="25" align="right" >用户名：</td>
            <td width="83%" height="25" > 
              <?=$row['userid']?>
            </td>
          </tr>
          <tr> 
            <td height="25" align="right" >密　码：</td>
            <td height="25" > 
              <?=$row['pwd']?>
            </td>
          </tr>
          <tr> 
            <td height="25" align="right" >注册</td>
            <td height="25" >时间： 
              <?=GetDateTimeMk($row['jointime'])?>
              　ＩＰ： 
              <?=$row['joinip']?>
            </td>
          </tr>
          <tr> 
            <td height="25" align="right" >最近登录</td>
            <td height="25" >时间： 
              <?=GetDateTimeMk($row['logintime'])?>
              　ＩＰ： 
              <?=$row['loginip']?>
            </td>
          </tr>
          <tr> 
            <td height="25" align="right" >电子邮箱：</td>
            <td height="25" ><input name="email" type="text" id="email" value="<?=$row['email']?>" style="width:150;height:20" > 
            </td>
          </tr>
          <tr> 
            <td height="25" align="right" >昵　称：</td>
            <td height="25" ><input name="uname" type="text" value="<?=$row['uname']?>" id="uname" size="20" style="width:150;height:20" ></td>
          </tr>
          <tr> 
            <td height="25" align="right" >性　别：</td>
            <td height="25" > <input type="radio" name="sex" class="np" value="男"<?if($row['sex']=="男" ) echo" checked" ;?>>
              男 &nbsp; <input type="radio" name="sex" class="np" value="女"<?if($row['sex']=="女" ) echo" checked" ;?>>
              女 </td>
          </tr>
          <tr> 
            <td height="25" align="right" >推荐级别：</td>
            <td height="25" ><input name="matt" type="text" id="matt" value="<?=$row['matt']?>" size="10"></td>
          </tr>
          <tr> 
            <td height="25" align="right" >生日：</td>
            <td height="25" ><input name="birthday" type="text" id="birthday" size="20" value="<?=$row['birthday']?>" > 
            </td>
          </tr>
          <tr> 
            <td height="25" align="right" >体型：</td>
            <td height="25" > <select name="weight" >
                <option value='<?=$row['weight']?>'> 
                <?=$row['weight']?>
                </option>
                <option value='平均'>平均</option>
                <option value='苗条/纤细'>苗条/纤细</option>
                <option value='健壮'>健壮</option>
                <option value='略胖'>略胖</option>
                <option value='大型'>大型</option>
              </select> &nbsp;身高： 
              <input name="height" value="<?=$row['height']?>" type="text" id="height" size="5" >
              厘米</td>
          </tr>
          <tr> 
            <td height="25" align="right" >职业：</td>
            <td height="25" > <input type="radio" class="np" name="job" value="学生" <?if($row['job']=="学生" ) echo" checked" ;?>>
              学生 
              <input type="radio" class="np" name="job" value="职员" <?if($row['job']=="职员" ) echo" checked" ;?>>
              职员 
              <input type="radio" class="np" name="job" value="白领" <?if($row['job']=="白领" ) echo" checked" ;?>>
              白领 
              <input type="radio" class="np" name="job" value="失业中" <?if($row['job']=="失业中" ) echo" checked" ;?>>
              失业中 </td>
          </tr>
          <tr> 
            <td height="25" align="right" >所在在区：</td>
            <td height="25" > <select name="province" size="1" id="province" width="4" onchange="javascript:selNext(this.document.form2.city,this.value)" style="width:85">
                <option value="0">--不限--</option>
                <?
				 $dsql->SetQuery("Select * From #@__area where rid=0");
				 $dsql->Execute();
				 while($rowa = $dsql->GetArray()){
				    if($row['province']==$rowa['eid'])
					{ echo "<option value='".$rowa['eid']."' selected>".$rowa['name']."</option>\r\n"; }
					else
					{ echo "<option value='".$rowa['eid']."'>".$rowa['name']."</option>\r\n"; }
				 }
				 ?>
              </select> &nbsp;城市： 
              <select id="city" name="city" width="4" style="width:85" >
                <option value="0">--不限--</option>
                <?
				 if(!empty($row['province'])){
				 $dsql->SetQuery("Select * From #@__area where rid=".$row['province']);
				 $dsql->Execute();
				 while($rowa = $dsql->GetArray()){
				    if($row['city']==$rowa['eid'])
					{ echo "<option value='".$rowa['eid']."' selected>".$rowa['name']."</option>\r\n"; }
					else
					{ echo "<option value='".$rowa['eid']."'>".$rowa['name']."</option>\r\n"; }
				 }}
				 ?>
              </select> </td>
          </tr>
          <tr align="center"> 
            <td height="25" colspan="2" > 
              <hr width="80%" size="1" noshade>
            </td>
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
            <td height="25" ><input name="homepage" value="<?=$row['homepage']?>" type="text" id="homepage" size="25" ></td>
          </tr>
          <tr> 
            <td height="25" align="right" >联系地址：</td>
            <td height="25" > <input name="address" value="<?=$row['address']?>" type="text" id="address" size="25" > 
            </td>
          </tr>
          <tr> 
            <td height="70" align="right" >自我介绍：</td>
            <td height="70" > <textarea name="myinfo" cols="40" rows="3" id="textarea3" ><?=$row['myinfo']?></textarea></td>
          </tr>
          <tr> 
            <td height="71" align="right" >个人签名：</td>
            <td height="71" > <textarea name="mybb" cols="40" rows="3" id="textarea4" ><?=$row['mybb']?></textarea></td>
          </tr>
          <tr align="center"> 
            <td height="25" colspan="2" > 
              <hr width="80%" size="1" noshade>
            </td>
          </tr>
          <tr> 
            <td height="25" align="right" >空间名称： </td>
            <td height="25" ><input name="spacename" type="text" id="spacename" size="35" value="<?=$row['spacename']?>"></td>
          </tr>
          <tr> 
            <td height="130" align="right" >空间公告：</td>
            <td height="130" ><textarea name="news" cols="50" rows="8" id="textarea7" ><?=$row['news']?></textarea></td>
          </tr>
          <tr> 
            <td height="130" align="right" >详细资料：</td>
            <td height="130" ><textarea name="fullinfo" cols="50" rows="8" id="textarea8" ><?=$row['fullinfo']?></textarea> 
            </td>
          </tr>
          <tr> 
            <td height="67" align="right" >&nbsp;</td>
            <td height="67" > <input type="submit" name="Submit" value="确定修改" > 
              &nbsp;&nbsp; <input type="reset" name="Submit22" value="重置" > </td>
          </tr>
        </form>
      </table> </td>
</tr>
</table>
<?
$dsql->Close();
?>
</body>
</html>