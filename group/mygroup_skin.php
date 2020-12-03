<?php
/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @version  $Id: mygroup_skin.php,v 1.1 2009/08/04 04:07:30 blt Exp $
 */
require_once(dirname(__FILE__)."/system/config.php");
$menutype = 'mydede';
$id = isset($id) && is_numeric($id) ? $id : 0;
$action = isset($action) ? trim($action) : '';
$skin = isset($skin) ? trim($skin) : 'default';
if($id < 1)
{
	ShowMsg("含有非法操作!.","-1");
	exit();
}
//取出圈子信息
$_GROUPS = $row = $db->GetOne("SELECT uid,groupname,theme FROM #@__groups WHERE groupid=".$id);
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
if($action=="save")
{
	if(!in_array($skin,array('default','green','pink')))
	{
		ShowMsg("无效主题!","-1");
		exit();
	}
	$db->ExecuteNoneQuery("UPDATE #@__groups SET `theme`='$skin' WHERE groupid='$id';");
	ShowMsg("主题已改变!", '-1',0,'1500');
	exit();
}
unset($row);
require_once(_SYSTEM_."/mygroup_skin.htm");
?>