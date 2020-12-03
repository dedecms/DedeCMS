<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(DEDEINC."/datalistcp.class.php");
CheckRank(0,0);
setcookie("ENV_GOBACK_URL",GetCurUrl(),time()+3600,"/");

function GetSta($sta){
	if($sta==0) return '未付款';
	else if($sta==1) return '已付款';
	else return '已完成';
}

$sql = "Select * From #@__member_operation where mid='".$cfg_ml->M_ID."' order by aid desc";
$dlist = new DataListCP();
$dlist->pageSize = 20;
$dlist->SetTemplate(DEDEMEMBER."/templets/operation.htm");    
$dlist->SetSource($sql);
$dlist->Display();  

?>