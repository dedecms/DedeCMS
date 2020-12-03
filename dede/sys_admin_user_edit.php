<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_User');
require_once(dirname(__FILE__)."/../include/inc_typelink.php");

if(empty($dopost)) $dopost = "";
$ID = ereg_replace("[^0-9]","",$ID);
/////////////////////////////////////////////
if($dopost=="saveedit")
{
	$pwd = trim($pwd);
	if($pwd!="" && ereg("[^0-9a-zA-Z_@\!\.-]",$pwd)){
		ShowMsg("密码不合法！","-1",0,300);
		exit();
	}
	$dsql = new DedeSql();
	if($pwd!="") $pwd = ",pwd='".substr(md5($pwd),0,24)."'";
	$dsql->SetQuery("Update #@__admin set uname='$uname',usertype='$usertype',tname='$tname',email='$email',typeid='$typeid' $pwd where ID='$ID'");
	$dsql->Execute();
	$dsql->Close();
	ShowMsg("成功更改一个帐户！","sys_admin_user.php");
	exit();
}
else if($dopost=="delete")
{
	if(empty($userok)) $userok="";
	if($userok!="yes")
	{
	   require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");
	   $wintitle = "删除用户";
	   $wecome_info = "<a href='sys_admin_user.php'>系统帐号管理</a>::删除用户";
	   $win = new OxWindow();
	   $win->Init("sys_admin_user_edit.php","js/blank.js","POST");
	   $win->AddHidden("dopost",$dopost);
	   $win->AddHidden("userok","yes");
	   $win->AddHidden("ID",$ID);
	   $win->AddTitle("系统警告！");
	   $win->AddMsgItem("你确信要删除用户：$userid 吗？","50");
	   $winform = $win->GetWindow("ok");
	   $win->Display();
	   exit();
  }
	$dsql = new DedeSql();
	$dsql->SetQuery("Delete From #@__admin where ID='$ID' And usertype<>'10' ");
	$dsql->Execute();
	$dsql->Close();
	ShowMsg("成功删除一个帐户！","sys_admin_user.php");
	exit();
}
//////////////////////////////////////////
$dsql = new DedeSql();
$row = $dsql->GetOne("Select * From #@__admin where ID='$ID'");
$typeOptions = "";
$dsql->SetQuery("Select ID,typename From #@__arctype where reID=0 And (ispart=0 Or ispart=1)");
$dsql->Execute('op');
while($nrow = $dsql->GetObject('op')){
	if($row['typeid']==$nrow->ID) $typeOptions .= "<option value='{$nrow->ID}' selected>{$nrow->typename}</option>\r\n";
  else $typeOptions .= "<option value='{$nrow->ID}'>{$nrow->typename}</option>\r\n"; 
}
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>管理员帐号--更改帐号</title>
<link href='base.css' rel='stylesheet' type='text/css'>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#98CAEF">
  <tr>
    <td height="19" background="img/tbg.gif" bgcolor="#E7E7E7"> 
      <table width="96%" border="0" cellspacing="1" cellpadding="1">
        <tr> 
          <td width="24%"><b><strong>更改帐号</strong></b> </td>
          <td width="76%" align="right"><strong><a href="sys_admin_user.php"><u>管理帐号</u></a></strong></td>
        </tr>
      </table></td>
</tr>
<tr>
    <td height="215" align="center" valign="top" bgcolor="#FFFFFF">
	<form name="form1" action="sys_admin_user_edit.php" method="post">
	<input type="hidden" name="dopost" value="saveedit">
	<input type="hidden" name="ID" value="<?php echo $row['ID']?>">
        <table width="98%" border="0" cellspacing="1" cellpadding="1">
          <tr> 
            <td width="16%" height="30">用户登录ID：</td>
            <td width="84%"><?php echo $row['userid']?></td>
          </tr>
          <tr> 
            <td height="30">用户笔名：</td>
            <td><input name="uname" type="text" id="uname" size="16" value="<?php echo $row['uname']?>" style="width:150"> &nbsp;（发布文章后显示责任编辑的名字）</td>
          </tr>
          <tr> 
            <td height="30">用户密码：</td>
            <td><input name="pwd" type="text" id="pwd" size="16" style="width:150"> &nbsp;（空不变，只能用'0-9'、'a-z'、'A-Z'、'.'、'@'、'_'、'-'、'!'以内范围的字符）</td>
          </tr>
          <tr> 
            <td height="30">用户类型：</td>
            <td>
			  <select name='usertype' style='width:150'>
			  	<?php 
			  	$dsql->SetQuery("Select * from #@__admintype order by rank asc");
			  	$dsql->Execute("ut");
			  	while($myrow = $dsql->GetObject("ut"))
			  	{
			  		if($row['usertype']==$myrow->rank) echo "<option value='".$myrow->rank."' selected>".$myrow->typename."</option>\r\n";
			  		else echo "<option value='".$myrow->rank."'>".$myrow->typename."</option>\r\n";
			  	}
			  	?>
			  </select>
         </td>
          </tr>
          <tr> 
            <td height="30">负责频道：</td>
            <td>
			<select name="typeid" style="width:160" id="typeid">
        <option value="0">--所有频道--</option>
				<?php echo $typeOptions?>
       </select>
			 </td>
          </tr>
          <tr> 
            <td height="30">真实姓名：</td>
            <td><input name="tname" type="text" id="tname" size="16" style="width:150" value="<?php echo $row['tname']?>"> &nbsp;</td>
          </tr>
          <tr> 
            <td height="30">电子邮箱：</td>
            <td><input name="email" type="text" id="email" size="16" style="width:150" value="<?php echo $row['email']?>"> &nbsp;</td>
          </tr>
          <tr> 
            <td height="30">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr> 
            <td height="30">&nbsp;</td>
            <td><input type="submit" name="Submit" value=" 保存用户 "></td>
          </tr>
        </table>
      </form>
	  </td>
</tr>
</table>
<?php 
$dsql->Close();
?>
</body>
</html>