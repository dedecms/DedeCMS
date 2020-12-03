<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Log');
require_once(dirname(__FILE__)."/../include/pub_datalist_dm.php");
require_once(dirname(__FILE__)."/../include/inc_functions.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");

$where = "";
$sql = "";
$where = "";
if(empty($adminid)) $adminid = 0;
if(empty($cip)) $cip = ""; 
if(empty($dtime)) $dtime = 0;

if($adminid>0) $where .= " And #@__log.adminid='$adminid' ";
if($cip!="") $where .= " And #@__log.cip like '%$cip%' ";
if($dtime>0){
	$nowtime = time();
	$starttime = $nowtime - ($dtime*24*3600);
	$where .= " And #@__log.dtime>'$starttime' ";
}

$sql = "Select #@__log.*,#@__admin.userid From #@__log
     left join #@__admin on #@__admin.ID=#@__log.adminid 
     where 1=1 $where order by #@__log.lid desc";
 
$adminlist = "";
$dsql = new DedeSql(false);
$dsql->SetQuery("Select ID,uname From #@__admin");
$dsql->Execute('admin');
while($myrow = $dsql->GetObject('admin')){
	$adminlist .="<option value='{$myrow->ID}'>{$myrow->uname}</option>\r\n";
}
$dsql->Close();    

$dlist = new DataList();
$dlist->Init();
$dlist->pageSize = 20;
$dlist->SetParameter("adminid",$adminid);
$dlist->SetParameter("cip",$cip);
$dlist->SetParameter("dtime",$dtime);
$dlist->SetSource($sql);
include(dirname(__FILE__)."/templets/log_list.htm");
$dlist->Close();

ClearAllLink();
?>
