<?php 
require_once(dirname(__FILE__)."/config.php");

require_once(dirname(__FILE__)."/../include/pub_datalist.php");
require_once(dirname(__FILE__)."/../include/inc_functions.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");

if(!isset($ischeck)) $ischeck = -1;
if(!isset($keyword)) $keyword = "";
if($ischeck==0) $seloption =  "<option value='0' selected>未审核</option>\r\n<option value='-1'>全部</option>\r\n";
else if($ischeck==1) $seloption = "<option value='1' selected>内页</option>\r\n<option value='-1'>全部</option>\r\n";
else if($ischeck==2) $seloption = "<option value='2' selected>首页</option>\r\n<option value='-1'>全部</option>\r\n";
else if($ischeck==3) $seloption = "<option value='3' selected>已审核</option>\r\n<option value='-1'>全部</option>\r\n";
else $seloption = "<option value='-1' selected>全部</option>\r\n";

function GetPic($pic)
{
	if($pic=="") return "无图标";
	else return "<img src='$pic' width='88' height='31' border='0'>";
}

function GetSta($sta)
{
	if($sta==1) return "内页";
	if($sta==2) return "首页";
	else return "未审核";
}

$addquery = " where 1=1 ";

if($ischeck!=-1){
	if($ischeck==3) $addquery .= " And ischeck>0 ";
	else $addquery .= " And ischeck='$ischeck' ";
}

if(!empty($keyword)){
	$addquery .= " And (url like '%$keyword%' OR webname like '%$keyword%') ";
}

$sql = "";
$sql = "Select * From #@__flink $addquery order by dtime desc";
$dlist = new DataList();
$dlist->Init();
$dlist->SetParameter("ischeck",$ischeck);
$dlist->SetParameter("keyword",$keyword);
$dlist->SetSource($sql);
$dlist->SetTemplet(dirname(__FILE__)."/templets/friendlink_main.htm");
$dlist->display();
$dlist->Close();

ClearAllLink();
?>