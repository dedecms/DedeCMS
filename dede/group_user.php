<?php
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/pub_datalist_dm.php");
require_once(dirname(__FILE__)."/../include/inc_functions.php");
CheckPurview('group_Edit');
if(!isset($ID)) $ID = NULL;
$ID = ereg_replace("[^0-9]","",$ID);
if(!isset($gid)) $gid = NULL;
$gid = ereg_replace("[^0-9]","",$gid);
if($gid < 1){
	ShowMsg("含有非法操作!.","-1");
	exit();
}
function filter($var){ 
if($var == '') return false; 
return true; 
}
$db = new DedeSql(false);
$row = $db->GetOne("SELECT ismaster,uid FROM #@__groups WHERE groupid='{$gid}'");
$ismaster     = $row['ismaster'];
$ismasterid		= $row['uid'];
function GetMaster($user){
	global $ismaster;
	$master = explode(",",$ismaster);
	if(in_array($user,$master)) return "<img src='img/adminuserico.gif'> 管理员";
	else return "普通会员";
}
//操作
if(!isset($action)) $action = NULL;
if($action=="del"){
	if($ismasterid == $ID){
		$db->Close();
		ShowMsg("圈主不能脱离群关系!","-1");	
		exit();
	}
	$row = $db->GetOne("SELECT username FROM #@__group_user WHERE uid='$ID' AND gid='$gid'");
	if(is_array($row)){
		$username = $row['username'];
		$master = explode(",",$ismaster);
		if(in_array($username,$master)){
			//如果会员存管理员字段将移出
			$k = array_search($username,$master);   
  		unset($master[$k]);
		}
		$master = array_filter($master, "filter");
		$ismaster = implode(",",$master);
		$db->ExecuteNoneQuery("UPDATE #@__groups SET ismaster='{$ismaster}' WHERE groupid='{$gid}'");
	}
	if($ID > 0)	$db->ExecuteNoneQuery("DELETE FROM #@__group_user WHERE uid='$ID' AND gid='$gid'");
	$db->Close();
	ShowMsg("已将该会员移出本群!.","-1");	
	exit();
}else if($action=="admin"){
	if($ismasterid == $ID){
		$db->Close();
		ShowMsg("圈主应同时有管理权!","-1");	
		exit();
	}
	$row = $db->GetOne("SELECT username FROM #@__group_user WHERE uid='$ID' AND gid='$gid'");
	if(is_array($row)){
		$username = $row['username'];
		$master = explode(",",$ismaster);
		if(in_array($username,$master)){
			//如果会员存管理员字段将移出
			$k = array_search($username,$master);   
  		unset($master[$k]);
  		$msg = "已将 {$username},设为普通会员!";
		}else{
			//否则加入到管理员数组
			array_push($master,$username);
			$msg = "已将 {$username},设为管理员!";
		}
		$master = array_filter($master, "filter");
		$ismaster = implode(",",$master);
		$db->ExecuteNoneQuery("UPDATE #@__groups SET ismaster='{$ismaster}' WHERE groupid='{$gid}'");
	}
	$db->Close();
	ShowMsg("{$msg}","-1");
	exit();
}else if($action=="add"){
	$uname = cn_substr($uname,15);
	if(empty($uname)){
		$db->Close();
		ShowMsg("请填写用户名!.","-1");
		exit();
	}
	$rs = $db->GetOne("SELECT COUNT(*) AS c FROM #@__group_user WHERE username like '$uname' AND gid='$gid'");
	if($rs['c'] > 0){
		$db->Close();
		ShowMsg("用户已加入该圈子!.","-1");
		exit();
	}
	$row = $db->GetOne("SELECT userid,ID FROM #@__member WHERE userid like '$uname'");
	if(!is_array($row)){
		$db->Close();
		ShowMsg("站内不存在该用户!.","-1");
		exit();
	}else{
		$db->ExecuteNoneQuery("INSERT INTO #@__group_user(uid,username,gid,jointime) VALUES('".$row['ID']."','".$row['userid']."','$gid','".time()."');");
		//如果设成管理员
		if($setmaster){
			$master = explode(",",$ismaster);
			array_push($master,$uname);
			$master = array_filter($master, "filter");
			$ismaster = implode(",",$master);
			$db->ExecuteNoneQuery("UPDATE #@__groups SET ismaster='{$ismaster}' WHERE groupid='{$gid}'");
		}
	}
	$db->Close();
	ShowMsg("成功添加用户:{$uname}","-1");
	exit();
}
if(!isset($username)) $username = NULL;
//列表加载模板
$wheresql = "WHERE gid='{$gid}'";
if(!empty($username)) $wheresql .= " AND username like '%".$username."%'";
$sql = "SELECT * FROM #@__group_user $wheresql ORDER BY jointime DESC";
$dlist = new DataList();
$dlist->pageSize = 20;
$dlist->SetParameter("username",$username);
$dlist->SetParameter("ID",$ID);
$dlist->SetParameter("gid",$gid);
$dlist->SetSource($sql);
include(dirname(__FILE__)."/templets/group_user.htm");
$dlist->Close();
$db->Close();
ClearAllLink();
?>