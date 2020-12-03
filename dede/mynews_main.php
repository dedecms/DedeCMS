<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
require_once(dirname(__FILE__)."/../include/pub_datalist.php");
require_once(dirname(__FILE__)."/../include/inc_functions.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");


$sql = "";
$sql = "Select
 #@__mynews.aid,#@__mynews.title,#@__mynews.writer,
 #@__mynews.senddate,#@__mynews.typeid,
 #@__arctype.typename 
 From #@__mynews 
 left join #@__arctype on #@__arctype.ID=#@__mynews.typeid
 order by aid desc";
$dlist = new DataList();
$dlist->Init();
$dlist->SetSource($sql);
$dlist->SetTemplet(dirname(__FILE__)."/templets/mynews_main.htm");
$dlist->display();
$dlist->Close();

ClearAllLink();
?>