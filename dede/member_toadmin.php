<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('member_Edit');
if(empty($dopost))
{
	$dopost = '';
}
if(empty($fmdo))
{
	$fmdo = '';
}
$ENV_GOBACK_URL = isset($_COOKIE['ENV_GOBACK_URL']) ? 'member_main.php' : '';
$row = array();
/*----------------
function __Toadmin()
升级为管理员
----------------*/
if($dopost == "toadmin")
{
	$pwd = trim($pwd);
	if($pwd!='' && ereg("[^0-9a-zA-Z_@!\.-]",$pwd))
	{
		ShowMsg('密码不合法，请使用[0-9a-zA-Z_@!.-]内的字符！','-1',0,3000);
		exit();
	}
	$safecodeok = substr(md5($cfg_cookie_encode.$randcode),0,24);
	if($safecodeok!=$safecode)
	{
		ShowMsg("请填写正确的安全验证串！","member_toadmin.php?id={$id}");
		exit();
	}
	$pwdm = '';
	if($pwd!='')
	{
		$inputpwd = ",pwd";
		$inputpwdv = ",'".substr(md5($pwd),5,20)."'";
		$pwdm = ",pwd='".md5($pwd)."'";
	}else{
		$row = $dsql->GetOne("select * from #@__member where mid='$id'");
		$password = $row['pwd'];
		$inputpwd = ",pwd";
		$pwd = substr($password,5,20);
		$inputpwdv = ",'".$pwd."'";
		$pwdm = ",pwd='".$password."'";
	}
	$typeids=(empty($typeids))? "" : $typeids;
	if($typeids=='')
	{
		ShowMsg("请为该管理员指定管理栏目！","member_toadmin.php?id={$id}");
		exit();
	}
	$typeid = join(',',$typeids);
	if($typeid=='0') $typeid = '';
	if($id!=1)
	{
		$query = "Insert Into `#@__admin`(id,usertype,userid$inputpwd,uname,typeid,tname,email)
					values('$id','$usertype','$userid'$inputpwdv,'$uname','$typeid','$tname','$email')";
	}
	else
	{
		$query = "Insert Into `#@__admin`(id,userid$inputpwd,uname,typeid,tname,email)
					values('$id','$userid'$inputpwdv,'$uname','$typeid','$tname','$email')";
	}
	$dsql->ExecuteNoneQuery($query);
	$query = "Update `#@__member` set rank='100',uname='$uname',matt='10',email='$email'$pwdm where mid='$id'";
	$dsql->ExecuteNoneQuery($query);
	$row = $dsql->GetOne("Select * From #@__admintype where rank='$usertype'");
	$floginid = $cuserLogin->getUserName();
	$fromid = $cuserLogin->getUserID();
	$subject = "恭喜您已经成功提升为管理员";
	$message = "亲爱的会员{$userid},您已经成功提升为{$row['typename']},具体操作权限请同网站超级管理员联系。";
	$sendtime = $writetime = time();
	$inquery = "INSERT INTO `#@__member_pms` (`floginid`,`fromid`,`toid`,`tologinid`,`folder`,`subject`,`sendtime`,`writetime`,`hasview`,`isadmin`,`message`)
      VALUES ('$floginid','$fromid','$id','$userid','inbox','$subject','$sendtime','$writetime','0','0','$message'); ";
	$dsql->ExecuteNoneQuery($inquery);
	ShowMsg("成功升级一个帐户！","member_main.php");
	exit();
}	
$id = ereg_replace("[^0-9]","",$id);
//显示用户信息
$randcode = mt_rand(10000,99999);
$safecode = substr(md5($cfg_cookie_encode.$randcode),0,24);	
$typeOptions = '';
$typeid=(empty($typeid))? '' : $typeid;
$typeids = explode(',',$typeid);
$dsql->SetQuery("Select id,typename From `#@__arctype` where reid=0 And (ispart=0 Or ispart=1)");
$dsql->Execute('op');
while($nrow = $dsql->GetObject('op'))
{
	$typeOptions .= "<option value='{$nrow->id}' class='btype'".(in_array($nrow->id, $typeids) ? ' selected' : '').">{$nrow->typename}</option>\r\n";
	$dsql->SetQuery("Select id,typename From #@__arctype where reid={$nrow->id} And (ispart=0 Or ispart=1)");
	$dsql->Execute('s');
	while($nrow = $dsql->GetObject('s'))
	{
		$typeOptions .= "<option value='{$nrow->id}' class='stype'".(in_array($nrow->id, $typeids) ? ' selected' : '').">—{$nrow->typename}</option>\r\n";
	}
}
$row = $dsql->GetOne("select * from #@__member where mid='$id'");
include DedeInclude('templets/member_toadmin.htm');
?>