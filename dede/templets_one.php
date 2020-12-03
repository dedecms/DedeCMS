<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('temp_One');
require_once(DEDEINC."/datalistcp.class.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
$addquery = '';
$keyword = (!isset($keyword) ? '' : $keyword);
$likeid = (!isset($likeid) ? '' : $likeid);
$addq = $likeid!='' ? " And likeid like '$likeid' " : '';
$sql = "Select aid,title,ismake,uptime,filename,likeid From `#@__sgpage` where title like '%$keyword%' $addq order by aid desc";
$dlist = new DataListCP();
$dlist->SetTemplet(DEDEADMIN."/templets/templets_one.htm");
$dlist->SetSource($sql);
$dlist->display();

function GetIsMake($im)
{
	return $im==1 ? '需编译' : '不编译';
}

?>