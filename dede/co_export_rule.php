<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('co_NewRule');
require_once(dirname(__FILE__)."/../include/pub_datalist.php");
require_once(dirname(__FILE__)."/../include/inc_functions.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");

$sql  = "
Select
aid,rulename,etype,dtime
From #@__co_exrule
order by aid desc
";

$dlist = new DataList();
$dlist->Init();
$dlist->SetSource($sql);
$dlist->SetTemplet(dirname(__FILE__)."/templets/co_export_rule.htm");
$dlist->display();
$dlist->Close();

ClearAllLink();
?>