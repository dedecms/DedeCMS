<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_User');
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
if(empty($dopost)) $dopost = "";
$ID = ereg_replace("[^0-9]","",$ID);
//----------------------------
if($dopost=="saveedit")
{
	$pwd = trim($pwd);
	if($pwd!=''){
	   if(ereg("[^0-9a-zA-Z_@\!\.-]",$pwd)){
		   ShowMsg("密码不合法！","-1",0,300);
		   exit();
		 }
		 $pwd = ",pwd='".substr(md5($pwd),0,24)."'";
	}
	$dsql = new DedeSql(false);
	$ks = Array();
	if(is_array($typeid)){
		foreach($typeid as $v){
			$vs = explode('-',$v);
			if(isset($vs[1])) $t = $vs[1];
			else $t = $vs[0];
			if(!isset($ks[$vs[0]])) $ks[$t] = 1;
		}
	}

	$typeid = '';
	foreach($ks as $k=>$v){
		if($k>0) $typeid .= ($typeid=='' ? $k : ','.$k);
	}
	$q = "Update `#@__admin` set uname='$uname',usertype='$usertype',tname='$tname',email='$email',typeid='$typeid' $pwd where ID='$ID'";
	$dsql->ExecuteNoneQuery($q);
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
	$dsql = new DedeSql(false);
	$dsql->SetQuery("Delete From `#@__admin` where ID='$ID' And usertype<>'10' ");
	$dsql->Execute();
	$dsql->Close();
	ShowMsg("成功删除一个帐户！","sys_admin_user.php");
	exit();
}
//--------------------------
$dsql = new DedeSql(false);
$row = $dsql->GetOne("Select * From #@__admin where ID='$ID'");
require_once(dirname(__FILE__)."/templets/sys_admin_user_edit.htm");
ClearAllLink();
?>