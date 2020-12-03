<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_MdPwd');
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
if(empty($dopost)) $dopost = "";
$ID = $cuserLogin->getUserID();
$dsql = new DedeSql(false);
$msg = "";
if($dopost=="saveedit")
{
	$pwd = trim($pwd);
	if($pwd!='')
	{
		if(eregi("[^0-9a-z_@!\.\-]",$pwd))
		{
		   ShowMsg("密码不合法！","-1",0,300);
		   exit();
	  }else{
	  	$pwd = ",pwd='".substr(md5($pwd),0,24)."'";
	  }
  }
	$dsql->ExecuteNoneQuery("Update `#@__admin` set uname='$uname',tname='$tname',email='$email' $pwd where ID='$ID'");
	$msg = "<script>alert('成功更改帐户！');</script>";
}
$row = $dsql->GetOne("Select * From #@__admin where ID='$ID'");
require_once(dirname(__FILE__)."/templets/my_acc_edit.htm");
ClearAllLink();
?>