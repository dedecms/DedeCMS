<?php
require_once(dirname(__FILE__)."/../include/common.inc.php");
require_once(DEDEINC."/channelunit.class.php");
if(!isset($action)) $action = '';

if(isset($arcID)) $aid = $arcID;
$arcID = $aid = (isset($aid) && is_numeric($aid) ? $aid : 0);

if(empty($aid)) {
	ShowMsg("文档ID不能为空!","-1");
	exit();
}

//读取文档信息
if($action=='')
{
	//读取文档信息
	$arcRow = GetOneArchive($aid);
	if($arcRow['aid']=='') {
		ShowMsg("无法把未知文档推荐给好友!","-1");
		exit();
	}
	extract($arcRow, EXTR_SKIP);
}
//发送推荐信息
//-----------------------------------
else if($action=='send')
{
	if(!eregi("^[0-9a-z][a-z0-9\.-]{1,}@[a-z0-9-]{1,}[a-z]\.[a-z\.]{1,}[a-z]$",$email))
	{
		echo "<script>alert('Email格式不正确!');history.go(-1);</script>";
		exit();
	}
	$mailbody = '';
	$msg = htmlspecialchars($msg);
	$mailtitle = "你的好友给你推荐了一篇文章";
	$mailbody .= "$msg \r\n\r\n";
	$mailbody .= "Power by http://www.dedecms.com 织梦内容管理系统！";

	$headers = "From: ".$cfg_adminemail."\r\nReply-To: ".$cfg_adminemail;

	if($cfg_sendmail_bysmtp == 'Y' && !empty($cfg_smtp_server))
	{
		$mailtype = 'TXT';
		require_once(DEDEINC.'/mail.class.php');
		$smtp = new smtp($cfg_smtp_server,$cfg_smtp_port,true,$cfg_smtp_usermail,$cfg_smtp_password);
		$smtp->debug = false;
		$smtp->sendmail($email, $cfg_smtp_usermail, $mailtitle, $mailbody, $mailtype);
	}
	else
	{
		@mail($email, $mailtitle, $mailbody, $headers);
	}

	ShowMsg("成功推荐一篇文章!","-1");
	exit();
}

//显示模板(简单PHP文件)
include(DEDEROOT.$cfg_templets_dir.'/plus/recommend.htm');

?>