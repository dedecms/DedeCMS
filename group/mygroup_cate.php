<?php
/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @version  $Id: mygroup_cate.php,v 1.1 2009/08/04 04:07:30 blt Exp $
 */
require_once(dirname(__FILE__)."/system/config.php");
require_once(DEDEMEMBER."/inc/inc_archives_functions.php");
$menutype = 'mydede';

$id = isset($id) && is_numeric($id) ? $id : 0;

if($id < 1)
{
	ShowMsg("含有非法操作!.","-1");
	exit();
}

//取出圈子信息
$_GROUPS =$row = $db->GetOne("SELECT uid,smalltype,groupname FROM #@__groups WHERE groupid=".$id);
if(!is_array($row))
{
	ShowMsg("圈子不存在!","-1");
	exit();
}
if($cfg_ml->M_ID!=$row['uid'])
{
	ShowMsg("该圈子不在你的管辖范围内!","-1");
	exit();
}
$smalltype = @explode(",",$row[smalltype]);

if(!isset($msg))
{
	$msg = '';
}
if(!isset($action))
{
	$action = '';
}
if($action=='add')
{
	if(isset($neworders))
	{
		$neworders = ereg_replace("[^0-9]","",$neworders);
	}
	else
	{
		$neworders = 0;
	}
	if(!isset($categories)||empty($categories))
	{
		$msg = "填写话题分类名称!";
	}
	else
	{
		$categories = cn_substrR(HtmlReplace($categories, 2),15);
		$is_have = $db->GetOne("SELECT `name` FROM #@__group_smalltypes WHERE gid=$id AND userid='".$cfg_ml->M_ID."' AND `name` like '".$categories."'");
		if(!is_array($is_have))
		{
			$result = $db->ExecuteNoneQuery("INSERT INTO #@__group_smalltypes(gid,userid,`name`,disorder)VALUES('$id','".$cfg_ml->M_ID."','$categories','$neworders');");
			if($result)
			{
				UpdateSmalltype($id);
				$msg = "成功新增分类:{$categories}";
			}
		}
		else
		{
			$msg = "错误,分类:{$categories},已存在!";
		}
	}
}
else if($action=='edit')
{
	if(isset($orders))
	{
		$orders = ereg_replace("[^0-9]","",$orders);
	}
	else
	{
		$orders = 0;
	}
	if(isset($categoriesid))
	{
		$categoriesid = ereg_replace("[^0-9]","",$categoriesid);
	}
	else
	{
		$categoriesid = 0;
	}
	$categories = cn_substrR(HtmlReplace($categories, 2),15);
	if($userit=='false' && in_array($categoriesid,$smalltype))
	{
		//移出数组
		$k = array_search($categoriesid,$smalltype);
		unset($smalltype[$k]);
	}
	else if($userit=='true' && !in_array($categoriesid,$smalltype))
	{
		@array_push($smalltype,$categoriesid);
	}
	$smalltype = @array_filter($smalltype);
	$smalltypetxt = @implode(",",$smalltype);
	$db->ExecuteNoneQuery("UPDATE #@__groups SET smalltype='$smalltypetxt' WHERE groupid=".$id);
	$db->ExecuteNoneQuery("UPDATE #@__group_smalltypes SET `name`='$categories',disorder='$orders' WHERE id=".$categoriesid);
	$msg = "成功修改类别:{$categories}!";
}
else if($action=='del')
{
	$db->ExecuteNoneQuery("DELETE FROM #@__group_smalltypes WHERE id='$categoriesid'");
	UpdateSmalltype($id);
}
$smalltypes = array();
$db->SetQuery("SELECT id,gid,userid,`name`,disorder FROM #@__group_smalltypes WHERE gid=$id AND userid='".$cfg_ml->M_ID."' ORDER BY disorder ASC");
$db->Execute();
while($rs = $db->GetArray())
{
	array_push ($smalltypes,$rs);
}
require_once(_SYSTEM_."/mygroup_cate.htm");

//更新目标圈子小分类
function UpdateSmalltype($groupid)
{
	global $cfg_ml,$db;
	$id = $groupid;
	$db->SetQuery("SELECT `id` FROM `#@__group_smalltypes` WHERE gid=$id AND `userid`='{$cfg_ml->M_ID}' ORDER BY `disorder` ASC");
	$db->Execute();
	$smalltype_array = array();
	while($rs = $db->GetArray())
	{
		array_push($smalltype_array,$rs['id']);
	}
	$smalltypetxt = @implode(",",$smalltype_array);
	if(!empty($smalltype_array))
	{
		$db->ExecuteNoneQuery("UPDATE #@__groups SET smalltype='$smalltypetxt' WHERE groupid={$id}");
		return 1;
	}
	else
	{
		return 0;
	}
}

?>