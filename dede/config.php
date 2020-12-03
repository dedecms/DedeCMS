<?
//该页仅用于检测用户登录的情况，如要手工更改系统配置，请更改config_base.php
require_once("inc_userLogin.php");
require_once("config_base.php");

//非超级管理员禁止访问的脚本
$s_exptag = "del_|_del|file_|admin_|sys_";

//获得当前脚本名称，如果你的系统被禁用了$_SERVER变量，请自行更改这个选项
$dedeNowurl = "";
$s_scriptName="";
$qstr="";
$isUrlOpen = @ini_get("allow_url_fopen");
if(!empty($_SERVER["REQUEST_URI"]))
{
	$s_scriptName = $_SERVER["REQUEST_URI"];
	$dedeNowurl = $s_scriptName;
}

//检验用户登录状态
$cuserLogin = new userLogin();
if($cuserLogin->getUserID()==-1)
{
	header("location:login.php?gotopage=$s_scriptName");
	exit();
}

//检验用户是否访问被禁止的脚本
if(ereg($s_exptag,$s_scriptName)&&$cuserLogin->getUserType()!=10)
{
	ShowMsg(" 你不是超级管理员，文件或系统操作以及大部份删除的操作均被限制，\\n\\n 如果你确实要获得这些权限，请与超级管理员进行删除。","-1");
	exit();
}
?>