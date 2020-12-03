<?php
/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @version  $Id: mygroup_manage.php,v 1.1 2009/08/04 04:07:30 blt Exp $
 */
require_once(dirname(__FILE__)."/system/config.php");
require_once(DEDEINC."/oxwindow.class.php");
require_once(DEDEMEMBER."/inc/inc_archives_functions.php");

$gid = $id = isset($id) && is_numeric($id) ? $id : 0;

if($id < 1)
{
	ShowMsg("含有非法操作!.","-1");
	exit();
}

//取出圈子信息
$_GROUPS = $row = $db->GetOne("SELECT * FROM #@__groups WHERE groupid='{$id}'");
if(!is_array($row))
{
	ShowMsg("圈子不存在!","-1");
	exit();
}
$groupsname = $row['groupname'];
$groupstoreid = $row['storeid'];
$groupishidden = $row['ishidden'];
$groupissystem = $row['issystem'];
$groupcreater = $row['creater'];
$groupimg     = $row['groupimg'];
$ismaster     = $row['ismaster'];
$groupdes     = $row['des'];
$groupuid     = $row['uid'];
$groupisindex = $row['isindex'];
$groupsmalltype = $row['smalltype'];
if($cfg_ml->M_ID!=$groupuid)
{

	ShowMsg("该圈子不在你的管辖范围内!","-1");
	exit();
}

//编译小分类成数组
$smalltypes    = @explode(",",$row['smalltype']);
if(!isset($action)) $action = '';
if($action=="save")
{
	$groupname = cn_substrR($groupname,75);
	$storeid = ereg_replace("[^0-9]","",$store);
	$description = cn_substrR($des,100);
	$row = $db->GetOne("SELECT tops FROM #@__store_groups WHERE storeid='{$storeid}'");
	if($row['tops'] >0 )
	{
		$rootstoreid = $row['tops'];
	}
	else
	{
		$rootstoreid = $storeid;
	}

//处理上传的缩略图
$litpic = MemberUploads('litpic','',$cfg_ml->M_ID,'image','',100,70,false);
if($litpic!='')
{
	SaveUploadInfo($groupname,$litpic,1);
}


	$inQuery = "UPDATE #@__groups SET groupname='".$groupname."',des='".$description."',groupimg='".$litpic."',rootstoreid='{$rootstoreid}',storeid='{$storeid}' WHERE groupid='{$id}' AND uid='".$cfg_ml->M_ID."';";
	$db->SetQuery($inQuery);
	if(!$db->ExecuteNoneQuery())
	{
		echo $db->GetError();
		ShowMsg("把数据更新到数据库groups表时出错，请检查！","-1");
		exit();
	}
	else
	{
		ShowMsg("成功更改圈子设置！","-1");
		exit();
	}
}

//类目递归
$db->SetQuery("SELECT * FROM #@__store_groups WHERE tops=0 ORDER BY orders ASC");
$db->Execute(1);
$option = '';
while($rs = $db->GetArray(1))
{
	$selected = "";
	if($rs['storeid']==$groupstoreid)
	{
		$selected = "selected='selected'";
	}
	$option .= "<option value='".$rs['storeid']."' ".$selected.">".$rs['storename']."</option>\n";
	$v = $rs['storeid'];
	$db->SetQuery("SELECT * FROM #@__store_groups WHERE tops='{$v}' ORDER BY orders ASC");
	$db->Execute(2);
	while($rs = $db->GetArray(2))
	{
		$selected = "";
		if($rs['storeid']==$groupstoreid)
		{
			$selected = "selected='selected'";
		}
		$option .= "<option value='".$rs['storeid']."' ".$selected.">--".$rs['storename']."</option>\n";
	}
}
require_once(_SYSTEM_."/mygroup_manage.htm");

?>