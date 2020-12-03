<?php
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);
require_once(DEDEINC."/datalistcp.class.php");
setcookie("ENV_GOBACK_URL",GetCurUrl(),time()+3600,"/");
$query = "Select * From `#@__member_operation` where mid='".$cfg_ml->M_ID."' order by aid desc";
$dlist = new DataListCP();
$dlist->pageSize = 20;
$dlist->SetTemplate(DEDEMEMBER."/templets/mypay.htm");
$dlist->SetSource($query);
$dlist->Display();
?>