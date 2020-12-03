<?php
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);
$menutype = 'config';
if(!isset($dopost))
{
	$dopost = '';
}

if($dopost=='save')
{
	$oldspacelogo=(empty($oldspacelogo))? "" : $oldspacelogo;
	$spacelogo=(empty($spacelogo))? "" : $spacelogo;
	$pagesize=(empty($pagesize))? "" : $pagesize;
	$sign=(empty($sign))? "" : $sign;
	$spacenews =(empty($spacenews))? "" : $spacenews;
	$spacename =(empty($spacename))? "" : $spacename;
	$maxlength = $cfg_max_face * 1024;
	$userdir = $cfg_user_dir.'/'.$cfg_ml->M_ID;
	if(!ereg('^'.$userdir, $oldspacelogo))
	{
		$oldspacelogo = '';
	}
	if(is_uploaded_file($spacelogo))
	{
			if(@filesize($_FILES['spacelogo']['tmp_name']) > $maxlength)
			{
				ShowMsg("你上传的Logo文件超过了系统限制大小：{$cfg_max_face} K！", '-1');
				exit();
			}
			//删除旧图片（防止文件扩展名不同，如：原来的是gif，后来的是jpg）
			if(eregi("\.(jpg|gif|png)$", $oldspacelogo) && file_exists($cfg_basedir.$oldspacelogo))
			{
				@unlink($cfg_basedir.$oldspacelogo);
			}
			//上传新工图片
			$spacelogo = MemberUploads('spacelogo','',$cfg_ml->M_ID,'image','mylogo', 200, 50);
	}
	else
	{
			$spacelogo = $oldspacelogo;
	}
	$pagesize = intval($pagesize);
	if($pagesize<=0)
	{
		ShowMsg('每页文档数不能小于0！','edit_space_info.php');
	    exit();
	}
	$spacename = cn_substrR(HtmlReplace($spacename,2),50);
	$sign = cn_substrR(HtmlReplace($sign),100);
	$spacenews = HtmlReplace($spacenews,-1);
	$query = "update `#@__member_space` set `pagesize` = '$pagesize',`spacename`='$spacename' , spacelogo='$spacelogo', `sign` = '$sign' ,`spacenews`='$spacenews' where mid='{$cfg_ml->M_ID}' ";
	$dsql->ExecuteNoneQuery($query);
	if($cfg_ml->M_Spacesta >= 0) {
			$dsql->ExecuteNoneQuery("update `#@__member` set spacesta=1 where mid='{$cfg_ml->M_ID}' And spacesta < 1 ");
		}
	ShowMsg('成功更新空间信息！','edit_space_info.php');
	exit();
}
else
{
		$row = $dsql->GetOne("select * from `#@__member_space` where mid='".$cfg_ml->M_ID."'");
		if(!is_array($row))
		{
			$inquery = "Insert Into `#@__member_space`(`mid` ,`pagesize` ,`matt` ,`spacename` ,`spacelogo` , `sign` ,`spacenews`)
			    Values('{$cfg_ml->M_ID}', '10', '0', '{$cfg_ml->M_UserName}的空间', '', '', ''); ";
			$row['spacename'] = '';
			$row['sign'] = '';
			$row['pagesize'] = 10;
			$row['spacestyle'] = 'person';
			$row['spacenews'] = '';
		}
		extract($row);
		include(dirname(__FILE__)."/templets/edit_space_info.htm");
		exit();
}

?>