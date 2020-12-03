<?php
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/pub_datalist_dm.php");
if(!isset($ID)) $ID = 0;
$ID = ereg_replace("[^0-9]","",$ID);
if(!isset($gid)) $gid = 0;
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
$row = $db->GetOne("SELECT ismaster FROM #@__groups WHERE groupid='{$gid}' AND uid='".$cfg_ml->M_ID."'");
if(!is_array($row)){
	ShowMsg("无当前管理权!.","-1");
	exit();
}
$ismaster     = $row['ismaster'];
function GetMaster($user){
	global $ismaster;
	$master = explode(",",$ismaster);
	if(in_array($user,$master)) return "<img src='img/adminuserico.gif' title='管理员'>";
	else return "";
}
if(!isset($action)) $action = '';
//操作
if($action=="del"){
	if($cfg_ml->M_ID == $ID){
		$db->Close();
		ShowMsg("亲爱的圈主您不能走!","-1");	
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
	if($cfg_ml->M_ID == $ID){
		$db->Close();
		ShowMsg("您身为圈主应同时有管理权!","-1");	
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
}else if($action="join"){
	$row = $db->GetOne("SELECT isjoin FROM #@__group_user WHERE uid='$ID' AND gid='$gid' AND isjoin='0'");
	if(is_array($row)) $db->ExecuteNoneQuery("UPDATE #@__group_user SET isjoin='1' WHERE uid='$ID' AND gid='$gid'");
}
$sql = "SELECT * FROM #@__group_user WHERE gid='{$gid}' ORDER BY jointime DESC";
$dlist = new DataList();
$dlist->pageSize = 20;
$dlist->SetParameter("ID",$ID);
$dlist->SetParameter("gid",$gid);
$dlist->SetSource($sql);
require_once(dirname(__FILE__)."/templets/mygroup_member.htm");
$dlist->Close();
?>