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
	$userdir = $cfg_user_dir.'/'.$cfg_ml->M_ID;
	if(!ereg('^'.$userdir,$oldface))
	{
		$oldface = '';
	}
	if(is_uploaded_file($face))
	{
		if($faceurl!='')
		{
			if( (ereg(':',$faceurl) && !eregi('^http:',$faceurl)) || !eregi("\.(jpg|png|gif)",$faceurl))
			{
				ShowMsg("你指定的图片网址存在问题！","-1");
				exit();
			}
		}

		//删除旧图片（防止文件扩展名不同，如：原来的是gif，后来的是jpg）
		if($oldface!='' && file_exists($cfg_basedir.$oldface))
		{
			@unlink($cfg_basedir.$oldface);
		}

		//上传新工图片
		$face = MemberUploads('face',$faceurl,$cfg_ml->M_ID,'image','myface',180,180);
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
$face = $cfg_ml->fields['face'];
include(DEDEMEMBER."/templets/edit_face.htm");
?>