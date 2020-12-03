<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/pub_datalist.php");
require_once(dirname(__FILE__)."/../include/inc_functions.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
function GetDatePage($mktime)
{
	if($mktime=="0") return "从未采集过";
	return strftime("%Y-%m-%d",$mktime);
}

$dsql = new DedeSql(false);
$untjs = array();
function GetTJ($nid)
{
	global $dsql;
	global $untjs;
	if(!isset($untjs[$nid]))
	{
		$dsql->SetSql("Select count(aid) as kk From #@__courl where nid='$nid'");
		$dsql->Execute();
		$row = $dsql->GetObject();
		$kk = $row->kk;
		$untjs[$nid] = $kk." ";
	}
	return $untjs[$nid];
}

$where = "";
if(!isset($typeid)) $typeid="";
if(!empty($typeid)) $where = " where #@__conote.typeid='$typeid' "; 

$sql  = "Select #@__conote.nid,#@__conote.typeid,#@__conote.gathername,#@__conote.language,";
$sql .= "#@__conote.savetime,#@__conote.lasttime,#@__co_exrule.rulename as typename From #@__conote ";
$sql .= "left join #@__co_exrule on #@__co_exrule.aid=#@__conote.typeid ";
$sql .= " $where order by nid desc";

$dlist = new DataList();
$dlist->Init();
$dlist->SetParameter("typeid",$typeid);
$dlist->SetSource($sql);
$dlist->SetTemplet(dirname(__FILE__)."/templets/co_main.htm");
$dlist->display();
$dlist->Close();
ClearAllLink();
?>