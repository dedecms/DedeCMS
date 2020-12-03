<?php
/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @version  $Id: digest.php,v 1.1 2009/08/04 04:07:29 blt Exp $
 */
require_once(dirname(__FILE__)."/config.php");
require_once(DEDEINC.'/datalistcp.class.php');

$id = isset($id) && is_numeric($id) ? $id : 0;
$tid = isset($tid) && is_numeric($tid) ? $tid : 0;
$do = isset($do) ? trim($do) : '';

if($id < 1 || $tid < 1)
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
elseif($rs['ishidden'])
{
	ShowMsg("圈子被管理员屏蔽中！","-1");
	exit();
}
$master = $rs['ismaster'];

//检测管理员
$ismaster    = @explode(",",$master);
$ismaster	 = in_array($cfg_ml->M_UserName,$ismaster);
if(empty($cfg_ml->M_UserName))
{
	$ismaster = 0;
}
if(!$ismaster)
{
	ShowMsg("对不起,您尚未该操作权限！","-1");
	exit();
}
$rs = $db->GetOne("SELECT * FROM #@__group_threads WHERE tid='$tid' AND closed='0'");
if(!is_array($rs))
{
	ShowMsg("对不起,主题已被删除或已被关闭！","-1");
	exit();
}

if($do=="digest")
{
	$db->ExecuteNoneQuery("UPDATE #@__group_threads SET digest='1' WHERE tid='$tid';");
	ShowMsg("该主题已被加精！","-1");
	exit();
}
else if($do=="undigest")
{
	$db->ExecuteNoneQuery("UPDATE #@__group_threads SET digest='0' WHERE tid='$tid';");
	ShowMsg("该主题已被去精！","-1");
	exit();
}
else if($do=="top")
{
	$db->ExecuteNoneQuery("UPDATE #@__group_threads SET displayorder='1' WHERE tid='$tid';");
	ShowMsg("该主题已被置顶！","-1");
	exit();
}
else if($do=="untop")
{
	$db->ExecuteNoneQuery("UPDATE #@__group_threads SET displayorder='0' WHERE tid='$tid';");
	ShowMsg("该主题已被消顶！","-1");
	exit();
}
ShowMsg("对不起您执行的是无效操作！","-1");

?>