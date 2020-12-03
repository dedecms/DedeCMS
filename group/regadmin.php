<?php
/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @version  $Id: regadmin.php,v 1.1 2009/08/04 04:07:30 blt Exp $
 */
require_once(dirname(__FILE__)."/config.php");

$id = isset($id) && is_numeric($id) ? $id : 0;
$do = isset($do) ? trim($do) : '';

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

if($do=="ok"&&isset($uid))
{
	$master = explode(",",$rs['ismaster']);
	$row = $db->GetOne("SELECT username FROM #@__group_user WHERE uid='$uid' AND gid='$id'");
	if(in_array($row['username'],$master))
	{
		ShowMsg("错误,".$row['username']."已经是管理员！","group.php?id=$id");
		exit();
	}
	array_push($master,$row['username']);
	$master = array_filter($master);
	$ismaster = implode(",",$master);
	$db->ExecuteNoneQuery("UPDATE #@__groups SET ismaster='{$ismaster}' WHERE groupid='$id'");
	$msg = "已将 {$username},设为管理员!";
	ShowMsg("{$msg}","group.php?id=$id");
	exit();
}
$title = $rs['groupname'];

//检测管理员
$ismaster    = @explode(",",$rs['ismaster']);
$ismaster	 = in_array($cfg_ml->M_UserName,$ismaster);
if(empty($cfg_ml->M_UserName))
{
	$ismaster = 0;
}
$uid  = $rs['uid'];
if($ismaster)
{
	ShowMsg("您已经是本圈管理员！","-1");
	exit();
}
$rs = $db->GetOne("SELECT * FROM #@__group_user WHERE gid='$id' AND uid='".$cfg_ml->M_ID."' AND isjoin='1'");
if(!is_array($rs))
{
	ShowMsg("您不是该圈子正式会员,无法申请管理员！","group.php?id=$id");
	exit();
}
else
{
	$subject = $cfg_ml->M_UserName.",向您申请为圈子:".$title.",的管理员.";
	$url = "/group/regadmin.php?do=ok&id=".$id."&uid=".$cfg_ml->M_ID;
	$message = $subject." [<font style=\"color:red\"><a href=\"".$url."\">同意通过</a></font>]";
	$db->ExecuteNoneQuery("INSERT INTO #@__pms(msgfrom,msgfromid,msgtoid,folder,new,subject,dateline,message) VALUES('".$cfg_ml->M_LoginID."','".$cfg_ml->M_ID."','".$uid."','inbox',1,'".$subject."','".time()."','".$message."');");

	/*
	//更新目标用户新邮件数目
	$row = $db->GetOne("Select COUNT(*) AS c From #@__pms where msgtoid='$uid' AND folder='inbox' AND `new`='1'");
	if($row['c'] > 0)
	{
		$db->ExecuteNoneQuery("UPDATE #@__member SET newpm='".$row['c']."' WHERE ID='{$uid}'");
	}
	*/
	ShowMsg("系统已为你提交申请","-1");
	exit();
}

?>