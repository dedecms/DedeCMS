<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_User');
require_once(dirname(__FILE__)."/../include/inc_typelink.php");

if(empty($dopost)) $dopost="";
if($dopost=="add")
{
	if(ereg("[^0-9a-zA-Z_@!\.-]",$pwd)){
		 ShowMsg("用户密码不合法！","-1",0,300);
		 exit();
	}
	if(ereg("[^0-9a-zA-Z_@!\.-]",$userid)){
		 ShowMsg("用户名不合法！","-1",0,300);
		 exit();
	}
	$dsql = new DedeSql(false);
	$dsql->SetQuery("Select * from `#@__admin` where userid='$userid' Or uname='$uname'");
	$dsql->Execute();
	$ns = $dsql->GetTotalRow();
	if($ns>0){
		$dsql->Close();
		ShowMsg("用户名或笔名已存在，不允许重复使用！","-1");
		exit();
	}
	$ks = Array();
	foreach($typeid as $v){
		$vs = explode('-',$v);
		if(isset($vs[1])) $t = $vs[1];
		else $t = $vs[0];
		if(!isset($ks[$vs[0]])) $ks[$t] = 1;
	}
	$typeid = '';
	foreach($ks as $k=>$v){
		if($k>0) $typeid .=($typeid=='' ? $k : ','.$k);
	}
	$inquery = "
	   Insert Into #@__admin(usertype,userid,pwd,uname,typeid,tname,email)
	   values('$usertype','$userid','".substr(md5($pwd),0,24)."','$uname','$typeid','$tname','$email')
	";
	$dsql->ExecuteNoneQuery($inquery);
	$dsql->Close();
	ShowMsg("成功增加一个用户！","sys_admin_user.php");
	exit();
}
$typeOptions = "";
$dsql = new DedeSql(false);
require_once(dirname(__FILE__)."/templets/sys_admin_user_add.htm");
ClearAllLink();
?>