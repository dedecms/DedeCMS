<?php
//------------------------------------
//计划任务配置及一些函数封装
//------------------------------------
//系统设置为维护状态后仍可访问后台
$cfg_IsCanView = true;
define('DEDEADMIN',dirname(__FILE__));
require_once(DEDEADMIN."/../include/config_base.php");
header("Cache-Control:private");
//获得当前脚本
$dedeNowurl = "";
$s_scriptName="";
$dedeNowurl = GetCurUrl();
$dedeNowurls = explode("?",$dedeNowurl);
$s_scriptName = $dedeNowurls[0];
//-------------------------------------------
//在验证页本身(在引用此文件前用  $_checkPage 标识)不执行下面验证操作，其它页面执行
if(!isset($_checkPage))
{
	//证书文件不存在，通知客户端重新验证
	if(!file_exists($cfg_basedir.'/data/rmcert.php'))
	{
		return '0';
		exit();
  }
  require_once($cfg_basedir.'/data/rmcert.php');
  $rmcertip = GetIP();
  //IP发生变化，通知客户端重新验证用户
  if($rmcertip!=$cfg_rmcert_ip)
  {
  	return '0';
		exit();
  }
  //证书不正确，客户端拒绝执行任务
  if($sgcode!=$cfg_rmcert_value)
  {
  	return '-1';
		exit();
  }
}
?>