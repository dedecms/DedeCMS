<?php
require_once(dirname(__FILE__)."/config.php");
require_once(DEDEINC."/datalistcp.class.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
$sql = "Select aid,votename,starttime,endtime,totalcount From #@__vote order by aid desc";
$dlist = new DataListCP();
$dlist->SetTemplet(DEDEADMIN."/templets/vote_main.htm");
$dlist->SetSource($sql);
$dlist->display();

?>