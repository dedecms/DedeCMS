<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/pub_datalist_dm.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");

function GetSta($sta)
{
	if($sta==1) return "";
	else return " checked";
}

if(empty($keyword)){ $keyword = ""; $addquery = ""; }
else $addquery = " where keyword like '%$keyword%'";

$sql = "Select * from #@__keywords $addquery order by rank desc";
$dlist = new DataList();
$dlist->Init();
$dlist->pageSize = 30;
$dlist->SetParameter("keyword",$keyword);
$dlist->SetSource($sql);
include(dirname(__FILE__)."/templets/article_keywords_main.htm");
$dlist->Close();

ClearAllLink();
?>
