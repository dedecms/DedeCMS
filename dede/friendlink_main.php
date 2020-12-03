<?
require_once(dirname(__FILE__)."/config.php");

require_once(dirname(__FILE__)."/../include/pub_datalist.php");
require_once(dirname(__FILE__)."/../include/inc_functions.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");

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

$sql = "";
$sql = "Select * From #@__flink order by dtime desc";
$dlist = new DataList();
$dlist->Init();
$dlist->SetSource($sql);
$dlist->SetTemplet(dirname(__FILE__)."/templets/friendlink_main.htm");
$dlist->display();
$dlist->Close();
?>