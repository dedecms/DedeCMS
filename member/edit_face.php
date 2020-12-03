<?php
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);
if(!isset($dopost))
{
	$dopost = '';
}
if(!isset($backurl))
{
	$backurl = 'edit_face.php';
}
if($dopost=='save')
{
	$maxlength = $cfg_max_face * 1024;
	$userdir = $cfg_user_dir.'/'.$cfg_ml->M_ID;
	if(!ereg('^'.$userdir,$oldface))
	{
		$oldface = '';
	}
	if(is_uploaded_file($face))
	{
		if(@filesize($_FILES['face']['tmp_name']) > $maxlength)
		{
			ShowMsg("你上传的头像文件超过了系统限制大小：{$cfg_max_face} K！", '-1');
			exit();
		}
		//删除旧图片（防止文件扩展名不同，如：原来的是gif，后来的是jpg）
		if(eregi("\.(jpg|gif|png)$", $oldface) && file_exists($cfg_basedir.$oldface))
		{
			@unlink($cfg_basedir.$oldface);
		}
		//上传新工图片
		$face = MemberUploads('face', $oldface, $cfg_ml->M_ID, 'image', 'myface', 180, 180);
	}
	else
	{
		$face = $oldface;
	}
	$query = "update `#@__member` set `face` = '$face' where mid='{$cfg_ml->M_ID}' ";
	$dsql->ExecuteNoneQuery($query);
	ShowMsg('成功更新头像信息！', $backurl);
	exit();
}
else if($dopost=='delold')
{
	if(empty($oldface))
	{
		ShowMsg("没有可删除的头像！", "-1");
		exit();
	}
	$userdir = $cfg_user_dir.'/'.$cfg_ml->M_ID;
	if(!ereg('^'.$userdir, $oldface))
	{
		$oldface = '';
	}
	if(eregi("\.(jpg|gif|png)$", $oldface) && file_exists($cfg_basedir.$oldface))
	{
		@unlink($cfg_basedir.$oldface);
	}
	$query = "update `#@__member` set `face` = '' where mid='{$cfg_ml->M_ID}' ";
	$dsql->ExecuteNoneQuery($query);
	ShowMsg('成功删除原来的头像！', $backurl);
	exit();
}

$face = $cfg_ml->fields['face'];
include(DEDEMEMBER."/templets/edit_face.htm");
exit();
?>