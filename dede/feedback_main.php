<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/pub_datalist_dm.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
$bgcolor = "";
if(!isset($keyword)) $keyword="";
if(!isset($typeid)) $typeid="0";

function IsCheck($st)
{
	if($st==1) return "[已审核]";
	else return "<font color='red'>[未审核]</font>";
}


$querystring = "select * from #@__feedback where CONCAT(#@__feedback.msg,#@__feedback.arctitle) like '%$keyword%' order by dtime desc";

$dlist = new DataList();
$dlist->pageSize = 10;
$dlist->Init();
$dlist->SetParameter("typeid",$typeid);
$dlist->SetParameter("keyword",$keyword);
$dlist->SetSource($querystring);
include(dirname(__FILE__)."/templets/feedback_main.htm");
$dlist->Close();

ClearAllLink();
?>