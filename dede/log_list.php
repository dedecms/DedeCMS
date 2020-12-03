<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Log');
require_once(DEDEINC."/datalistcp.class.php");
require_once(DEDEINC."/common.func.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
$sql = $where = "";
if(empty($adminid))
{
	$adminid = 0;
}
if(empty($cip))
{
	$cip = "";
}
if(empty($dtime))
{
	$dtime = 0;
}
if($adminid>0)
{
	$where .= " And #@__log.adminid='$adminid' ";
}
if($cip!="")
{
	$where .= " And #@__log.cip like '%$cip%' ";
}
if($dtime>0)
{
	$nowtime = time();
	$starttime = $nowtime - ($dtime*24*3600);
	$where .= " And #@__log.dtime>'$starttime' ";
}
$sql = "Select #@__log.*,#@__admin.userid From #@__log
     left join #@__admin on #@__admin.id=#@__log.adminid
     where 1=1 $where order by #@__log.lid desc";
$adminlist = "";
$dsql->SetQuery("Select id,uname From #@__admin");
$dsql->Execute('admin');
while($myrow = $dsql->GetObject('admin'))
{
	$adminlist .="<option value='{$myrow->id}'>{$myrow->uname}</option>\r\n";
}
$dlist = new DataListCP();
$dlist->pageSize = 20;
$dlist->SetParameter("adminid",$adminid);
$dlist->SetParameter("cip",$cip);
$dlist->SetParameter("dtime",$dtime);
$dlist->SetTemplate(DEDEADMIN."/templets/log_list.htm");
$dlist->SetSource($sql);
$dlist->Display();

?>