<?php
/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @version  $Id: reg.php,v 1.1 2009/08/04 04:07:30 blt Exp $
 */
require_once(dirname(__FILE__)."/config.php");

$id = isset($id) && is_numeric($id) ? $id : 0;

if(!$cfg_ml->IsLogin())
{
	ShowMsg("未登录前不充许该操作！","-1");
	exit();
}

if($id < 1)
{
	ShowMsg("错误,未定义的操作！","-1");
	exit();
}

$rs = $db->GetOne("SELECT * FROM #@__groups WHERE groupid='$id'");
if(!is_array($rs))
{
	ShowMsg("圈子不存在,或被删除！","-1");
	exit();
}
else if($rs['ishidden'])
{
	ShowMsg("圈子被管理员屏蔽中！","-1");
	exit();
}
$title = $rs['groupname'];
$uid = $rs['uid'];
$rs = $db->GetOne("SELECT * FROM #@__group_user WHERE gid='$id' AND uid='".$cfg_ml->M_ID."'");
if(is_array($rs))
{
	$msgs = '状态:';
	if(!$rs['isjoin'])
	{
		$msgs .= '未通过审核.';
	}
	else
	{
		$msgs .= '正式会员';
	}
	unset($rs);
	ShowMsg("您已经是本圈成员！".$msgs,"-1");
	exit();
}
else
{
	$usergroups = $db->GetOne("SELECT COUNT(*) AS c FROM #@__group_user WHERE gid='$id'");
	if($usergroups >= $cfg_group_maxuser && $cfg_group_maxuser > 0)
	{
		ShowMsg("该圈子已超过系统设定上限！","-1");
		exit();
	}
	if($cfg_group_click)
	{
		$isjoin = 0;
	}
	else
	{
		$isjoin = 1;
	}
	$SetQuery = "INSERT INTO #@__group_user(uid,username,gid,jointime,isjoin) VALUES('".$cfg_ml->M_ID."','".$cfg_ml->M_UserName."','$id','".time()."','$isjoin');";
	if($db->ExecuteNoneQuery($SetQuery))
	{
		Upcountgroups($id);
	}
	if($isjoin==0)
	{
		$subject = $cfg_ml->M_UserName.",申请加入{$title}圈子.";
		$url = $cfg_member_dir."/mygroup_member.php?gid=".$id;
		$message = $subject." [<font style=\"color:red\"><a href=\"".$url."\">管理圈子用户</a></font>]";
		$db->ExecuteNoneQuery("INSERT INTO #@__pms(msgfrom,msgfromid,msgtoid,folder,new,subject,dateline,message) VALUES('".$cfg_ml->M_LoginID."','".$cfg_ml->M_ID."','".$uid."','inbox',1,'".$subject."','".time()."','".$message."');");

		/*
		//更新目标用户新邮件数目
		$row = $db->GetOne("Select COUNT(*) AS c From #@__pms where msgtoid='$uid' AND folder='inbox' AND `new`='1'");
		if($row['c'] > 0)
		{
			$db->ExecuteNoneQuery("UPDATE #@__member SET newpm='".$row['c']."' WHERE ID='{$uid}'");
		}
		*/
	}
}

ShowMsg("欢迎加入！{$title}圈","-1");

?>