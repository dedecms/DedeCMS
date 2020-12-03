<?php
/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @version  $Id: mygroup_member.php,v 1.1 2009/08/04 04:07:30 blt Exp $
 */
require_once(dirname(__FILE__)."/system/config.php");
require_once(DEDEINC."/datalistcp.class.php");

$id = isset($id) && is_numeric($id) ? $id : 0;
$uid = isset($id) && is_numeric($id) ? $uid : 0;

$action = isset($action) ? trim($action) : '';

if($id < 1)
{
	ShowMsg("含有非法操作!.","-1");
	exit();
}
$_GROUPS = $row = $db->GetOne("SELECT ismaster,groupname FROM #@__groups WHERE groupid='{$id}' AND uid='".$cfg_ml->M_ID."'");
if(!is_array($row))
{
	ShowMsg("无当前管理权!.","-1");
	exit();
}

$ismaster = $row['ismaster'];

if($action=="del")
{
	if($cfg_ml->M_ID == $uid)
	{
		ShowMsg("亲爱的圈主您不能走!","-1");
		exit();
	}
	$row = $db->GetOne("SELECT username FROM #@__group_user WHERE uid='$uid' AND gid='$id'");
	if(is_array($row))
	{
		$username = $row['username'];
		$master = explode(",",$ismaster);
		if(in_array($username,$master))
		{
			//如果会员存管理员字段将移出
			$k = array_search($username,$master);
			unset($master[$k]);
		}
		$master = array_filter($master, "filter");
		$ismaster = implode(",",$master);
		$db->ExecuteNoneQuery("UPDATE #@__groups SET ismaster='{$ismaster}' WHERE groupid='{$id}'");
	}
	if($uid > 0)
	{
		$db->ExecuteNoneQuery("DELETE FROM #@__group_user WHERE uid='$uid' AND gid='$id'");
	}
	ShowMsg("已将该会员移出本群!.","-1");
	exit();
}
else if($action=="admin")
{
	if($cfg_ml->M_ID == $uid)
	{
		ShowMsg("您身为圈主应同时有管理权!","-1");
		exit();
	}
	$row = $db->GetOne("SELECT username FROM #@__group_user WHERE uid='$uid' AND gid='$id'");
	if(is_array($row))
	{
		$username = $row['username'];
		$master = explode(",",$ismaster);
		if(in_array($username,$master))
		{
			//如果会员存管理员字段将移出
			$k = array_search($username,$master);
			unset($master[$k]);
			$msg = "已将 {$username},设为普通会员!";
		}
		else
		{
			//否则加入到管理员数组
			array_push($master,$username);
			$msg = "已将 {$username},设为管理员!";
		}
		$master = array_filter($master, "filter");
		$ismaster = implode(",",$master);
		$db->ExecuteNoneQuery("UPDATE #@__groups SET ismaster='{$ismaster}' WHERE groupid='{$id}'");
	}
	ShowMsg("{$msg}","-1");
	exit();
}
else if($action="join")
{
	$row = $db->GetOne("SELECT isjoin FROM #@__group_user WHERE uid='$uid' AND gid='$id' AND isjoin='0'");
	if(is_array($row))
	{
		$db->ExecuteNoneQuery("UPDATE #@__group_user SET isjoin='1' WHERE uid='$uid' AND gid='$id'");
	}

}
$sql = "SELECT * FROM #@__group_user WHERE gid='{$id}' ORDER BY jointime DESC";


$dl = new DataListCP();
$dl->pageSize = 20;
$dl->SetParameter("id",$id);
$dl->SetParameter("gid",$gid);

//这两句的顺序不能更换
$dl->SetTemplate(_SYSTEM_."/mygroup_member.htm");      //载入模板
$dl->SetSource($sql);            //设定查询SQL
$dl->Display();                  //显示

function filter($var)
{
	return $var == '' ? false : true;
}

function GetMaster($user)
{
	global $ismaster;
	$master = explode(",",$ismaster);
	if(in_array($user,$master))
	{
		return "<img src='img/adminuserico.gif' title='管理员'>";
	}
	else
	{
		return "";
	}
}

?>