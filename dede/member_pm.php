<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('member_Pm');
require_once(dirname(__FILE__)."/../include/pub_datalist.php");
require_once(dirname(__FILE__)."/../include/inc_functions.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");

if(!isset($folder)) $folder = '';
if(!isset($username)) $username = '';
if(!isset($keyword)) $keyword = '';

if(isset($dopost)){
	$ID = ereg_replace("[^0-9]","",$ID);
	if($dopost=="del"&&!empty($ID)){
		$db = new DedeSql(false);
		$db->ExecuteNoneQuery("DELETE FROM #@__pms WHERE pmid='$ID'");
		$db->Close();
	}
}


$whereSql = "WHERE folder='inbox' AND isadmin='0'";
if(!empty($folder)) $whereSql = "WHERE folder='$folder'";
$postuser = "收件人";
if($folder=="inbox"||$folder=='') $postuser = "发件人";

if(!empty($keyword)) $whereSql .= " AND (subject like '%".$keyword."%' OR message like '%".$keyword."%')";

if(!empty($username)) $whereSql .= " AND msgfrom like '%".$username."%'";

function  GetFolders($me){
	if($me=="track") return "发件箱";
	else if($me=="inbox") return "收件箱";
	else if($me=="outbox") return "草搞箱";
}

function IsReader($me){
	$me = ereg_replace("[^0-1]","",$me);
	if($me) return "<font color='green'>×</font>";
	else return "<font color='red'>√</font>";
}

$sql = "SELECT * FROM #@__pms $whereSql ORDER BY dateline desc";
$dlist = new DataList();
$dlist->Init();
$dlist->SetParameter("folder",$folder);
$dlist->SetParameter("username",$username);
$dlist->SetParameter("keyword",$keyword);
$dlist->SetSource($sql);
$dlist->SetTemplet(dirname(__FILE__)."/templets/member_pm.htm");
$dlist->display();
$dlist->Close();

ClearAllLink();
?>