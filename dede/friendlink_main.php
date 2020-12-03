<?
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/pub_datalist.php");
require_once(dirname(__FILE__)."/../include/inc_functions.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");

function GetPic($pic)
{
	if($pic=="") return "нчм╪╠Й";
	else return "<img src='$pic' width='88' height='31' border='0'>";
}

function GetSta($sta)
{
	if($sta==1) return "ряиС╨к";
	else return "н╢иС╨к";
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