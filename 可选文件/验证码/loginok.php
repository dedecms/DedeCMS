<?
require_once("inc_userLogin.php");
require_once("config_base.php");
$validate = strtolower($validate);
if($validate!=$_SESSION["s_validate"])
{
	ShowMsg("验证码不正确!","-1");
	exit();
}
$cuserLogin = new userLogin();
if(!empty($userid)&&!empty($pwd))
{
	$res = $cuserLogin->checkUser($userid,$pwd);
	if($res==1)
	{
		$cuserLogin->keepUser();
		if(!empty($gotopage)) header("location:$gotopage");
		else header("location:index.php");
	}
	else if($res==-1)
	{
		ShowMsg("你的用户名不存在!","-1");
		exit();
	}
	else
	{
		ShowMsg("你的密码错误!","-1");
		exit();
	}
}
else
{
	ShowMsg("用户和密码没填写完整!","-1");
	exit();
}
?> 
