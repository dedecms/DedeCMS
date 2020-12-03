<?
//该页仅用于检测用户登录的情况，如要手工更改系统配置，请更改config_base.php
require_once(dirname(__FILE__)."/../include/inc_userlogin.php");
require_once(dirname(__FILE__)."/../include/config_base.php");

//非超级管理员禁止访问的脚本
$s_exptag = "del_|_del|file_|admin_|sys_";

//获得当前脚本名称，如果你的系统被禁用了$_SERVER变量，请自行更改这个选项
$dedeNowurl = "";
$s_scriptName="";
$isUrlOpen = @ini_get("allow_url_fopen");
 
$dedeNowurl = GetCurUrl();
$dedeNowurls = explode("?",$dedeNowurl);
$s_scriptName = $dedeNowurls[0];

//检验用户登录状态
$cuserLogin = new userLogin();
if($cuserLogin->getUserID()==-1)
{
	header("location:login.php?gotopage=$s_scriptName");
	exit();
}

//检验用户是否访问被禁止的脚本
if(eregi($s_exptag,$s_scriptName) && $cuserLogin->getUserType()<5)
{
	ShowMsg(" 对不起，你没有权限访问本页。","-1");
	exit();
}

//限制用户访问某页面
function SetPageRank($pagerank)
{
	global $cuserLogin;
	if($cuserLogin->getUserRank()<$pagerank)
	{
		ShowMsg("对不起，你没有权限访问本页。","-1");
		exit();
	}
}
?>