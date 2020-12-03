<?
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
SetPageRank(10);
if(empty($dopost)) $dopost="";
if($dopost=="add")
{
	if(ereg("[^0-9a-zA-Z_@\!\.-]",$pwd)||ereg("[^0-9a-zA-Z_@\!\.-]",$userid)){
		ShowMsg("密码或或用户名不合法！","-1",0,300);
		exit();
	}
	$dsql = new DedeSql(false);
	$dsql->SetQuery("Select * from #@__admin where userid='$userid' Or uname='$uname'");
	$dsql->Execute();
	$ns = $dsql->GetTotalRow();
	if($ns>0){
		$dsql->Close();
		ShowMsg("用户名已存在或笔名已存在！","-1");
		exit();
	}
	$inquery = "
	Insert Into #@__admin(usertype,userid,pwd,uname,typeid) values('$usertype','$userid','".md5($pwd)."','$uname',$typeid)
	";
	$dsql->SetQuery($inquery);
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg("成功增加一个用户！","sys_admin_user.php");
	exit();
}
$tl = new TypeLink(0);
$typeOptions = $tl->GetOptionArray(0,0,0);
$tl->Close();
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>管理员帐号--新增帐号</title>
<link href='base.css' rel='stylesheet' type='text/css'>
<script language='javascript'>
	function checkSubmit()
  {
     if(document.form1.userid.value==""){
	     alert("用户ID不能为空！");
	     document.form1.userid.focus();
	     return false;
     }
     if(document.form1.uname.value==""){
	     alert("用户名不能为空！");
	     document.form1.uname.focus();
	     return false;
     }
     if(document.form1.pwd.value==""){
	     alert("用户密码不能为空！");
	     document.form1.pwd.focus();
	     return false;
     }
     return true;
 }
</script>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#666666">
  <tr>
    <td height="19" background="img/tbg.gif" bgcolor="#E7E7E7"> 
      <table width="96%" border="0" cellspacing="1" cellpadding="1">
        <tr> 
          <td width="24%"><b><strong>新增帐号</strong></b> </td>
          <td width="76%" align="right"><strong><a href="sys_admin_user.php"><u>管理帐号</u></a></strong></td>
        </tr>
      </table></td>
</tr>
<tr>
    <td height="215" align="center" valign="top" bgcolor="#FFFFFF">
	<form name="form1" action="sys_admin_user_add.php" onSubmit="return checkSubmit();" method="post">
	<input type="hidden" name="dopost" value="add">
  <table width="98%" border="0" cellspacing="1" cellpadding="1">
          <tr> 
            <td width="16%" height="30">用户登录ID：</td>
            <td width="84%"><input name="userid" type="text" id="userid" size="16" style="width:150">
              （只能用'0-9'、'a-z'、'A-Z'、'.'、'@'、'_'、'-'、'!'以内范围的字符）</td>
          </tr>
          <tr> 
            <td height="30">用户笔名：</td>
            <td><input name="uname" type="text" id="uname" size="16" style="width:150"> &nbsp;（发布文章后显示责任编辑的名字）</td>
          </tr>
          <tr> 
            <td height="30">用户密码：</td>
            <td><input name="pwd" type="text" id="pwd" size="16" style="width:150"> &nbsp;（只能用'0-9'、'a-z'、'A-Z'、'.'、'@'、'_'、'-'、'!'以内范围的字符）</td>
          </tr>
          <tr> 
            <td height="30">用户类型：</td>
            <td>
			    <select name='usertype' style='width:150'>
			  	<?
			  	$dsql = new DedeSql(false);
			  	$dsql->SetQuery("Select * from #@__admintype order by rank asc");
			  	$dsql->Execute("ut");
			  	while($myrow = $dsql->GetObject("ut"))
			  	{
			  		echo "<option value='".$myrow->rank."'>".$myrow->typename."</option>\r\n";
			  	}
			  	$dsql->Close();
			  	?>
			  </select>
            </td>
          </tr>
          <tr> 
            <td height="30">负责频道：</td>
            <td>
			<select name="typeid" style="width:300" id="typeid">
                <option value="0" selected>--所有频道--</option>
				<?=$typeOptions?>
             </select>
			 </td>
          </tr>
          <tr> 
            <td height="30">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr> 
            <td height="30">&nbsp;</td>
            <td><input type="submit" name="Submit" value=" 增加用户 "></td>
          </tr>
        </table>
      </form>
	  </td>
</tr>
</table>
</body>
</html>