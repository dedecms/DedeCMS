<?php
require_once(dirname(__FILE__)."/config.php");
require_once(DEDEINC."/datalistcp.class.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
$sql = "Select * From #@__flink order by dtime desc";
$dlist = new DataListCP();
$dlist->SetTemplet(DEDEADMIN."/templets/friendlink_main.htm");
$dlist->SetSource($sql);
$dlist->display();

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

?>