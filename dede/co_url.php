<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/pub_datalist_dm.php");
require_once(dirname(__FILE__)."/../include/inc_functions.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
$where = "";
$sql = "";
if(!isset($nid)) $nid="";
if(!empty($nid)) $where = " where #@__courl.nid='$nid' "; 
if(empty($small)) $small = 0;

function IsDownLoad($isd){
	if($isd=="0") return "未下载";
	else return "已下载";
}

function IsExData($isex){
	if($isex==0) return "未导出";
	else return "已导出";
}

if($nid!=""){
	$exportbt = "<input type='button' name='b0' value='导出采集内容'  class='inputbut' style='width:100' onClick=\"location.href='co_export.php?nid=$nid';\">&nbsp;";
}
else{
	$exportbt = "";
}

$sql .= "Select #@__courl.aid,#@__courl.nid,#@__courl.isex,#@__courl.title,#@__courl.url,";
$sql .= "#@__courl.dtime,#@__courl.isdown,#@__conote.gathername From #@__courl  ";
$sql .= " left join #@__conote on #@__conote.nid=#@__courl.nid $where";
$sql .= " order by #@__courl.aid desc";

$dlist = new DataList();
$dlist->Init();
$dlist->SetParameter("nid",$nid);
$dlist->SetParameter("small",$small);
$dlist->SetSource($sql);
if($small==0) include(dirname(__FILE__)."/templets/co_url.htm");
else include(dirname(__FILE__)."/templets/co_url_2.htm");
$dlist->Close();

ClearAllLink();
?>
