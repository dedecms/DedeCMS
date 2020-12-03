<?php 
require_once(dirname(__FILE__)."/config.php");

require_once(dirname(__FILE__)."/../include/pub_datalist.php");
require_once(dirname(__FILE__)."/../include/inc_functions.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");

$sql = "";
$sql = "Select aid,votename,starttime,endtime,totalcount From #@__vote order by aid desc";
$dlist = new DataList();
$dlist->Init();
$dlist->SetSource($sql);
$dlist->SetTemplet(dirname(__FILE__)."/templets/vote_main.htm");
$dlist->display();
$dlist->Close();
?>