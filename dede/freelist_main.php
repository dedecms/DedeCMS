<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('temp_One');
require_once(DEDEINC."/datalistcp.class.php");
require_once(DEDEINC."/common.func.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
$sql = "Select aid,title,ismake,uptime,filename From #@__sgpage order by aid desc";
$dlist = new DataListCP();
$dlist->SetTemplet(DEDEADMIN."/templets/freelist_main.htm");
$dlist->SetSource($sql);
$dlist->display();

function GetIsMake($im)
{
	if($im==1) return "需编译";
	else  return "不编译";
}
?>