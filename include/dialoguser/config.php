<?php 
//该页仅用于检测用户登录的情况，如要手工更改系统配置，请更改config_base.php
require_once(dirname(__FILE__)."/../config_base.php");
require_once(dirname(__FILE__)."/../inc_memberlogin.php");

//获得当前脚本名称，如果你的系统被禁用了$_SERVER变量，请自行更改这个选项
$dedeNowurl = "";
$s_scriptName="";
$dedeNowurl = GetCurUrl();
$dedeNowurls = explode("?",$dedeNowurl);
$s_scriptName = $dedeNowurls[0];

//检验用户登录状态
$cfg_ml = new MemberLogin();
if(!$cfg_ml->IsLogin())
{
	$gurl = $cfg_memberurl."/login.php?gourl=".urlencode($dedeNowurl);
	echo "<script language='javascript'>location='$gurl';</script>";
	exit();
}

?>