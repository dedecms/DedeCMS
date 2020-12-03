<?php
require_once(dirname(__FILE__)."/config.php");
require_once(DEDEINC."/datalistcp.class.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
$sql  = "Select co.nid,co.channelid,co.notename,co.sourcelang,co.uptime,co.cotime,co.pnum,ch.typename";
$sql .= " From `#@__co_note` co left join `#@__channeltype` ch on ch.id=co.channelid order by co.nid desc";
$dlist = new DataListCP();
$dlist->SetTemplet(DEDEADMIN."/templets/co_main.htm");
$dlist->SetSource($sql);
$dlist->display();

function GetDatePage($mktime)
{
	return $mktime=='0' ? '从未采集过' : MyDate('Y-m-d',$mktime);
}

function TjUrlNum($nid)
{
	global $dsql;
	$row = $dsql->GetOne("Select count(*) as dd From `#@__co_htmls` where nid='$nid' ");
	return $row['dd'];
}
?>