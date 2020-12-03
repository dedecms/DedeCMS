<?
//该页仅用于检测用户登录的情况，如要手工更改系统配置，请更改config_base.php
require_once(dirname(__FILE__)."/../config_base.php");
require_once(dirname(__FILE__)."/../inc_userlogin.php");

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
	$gurl = $cfg_cmspath."/include/dialog/"."login.php?gotopage=".urlencode($dedeNowurl);
	echo "<script language='javascript'>location='$gurl';</script>";
	exit();
}

?>