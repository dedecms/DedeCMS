<?php
require_once(dirname(__FILE__).'/config.php');
CheckPurview('temp_Other');
require_once(DEDEINC.'/datalistcp.class.php');
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,'/');

$sql = "Select myt.aid,myt.tagname,tp.typename,myt.timeset,myt.endtime
        From `#@__mytag` myt left join `#@__arctype` tp on tp.id=myt.typeid order by myt.aid desc ";
$dlist = new DataListCP();
$dlist->SetTemplet(DEDEADMIN.'/templets/mytag_main.htm');
$dlist->SetSource($sql);
$dlist->display();

function TestType($tname)
{
	return $tname=='' ? '所有栏目' : $tname;
}

function TimeSetValue($ts)
{
	return $ts==0 ? '不限时间' : '限时标记';
}
?>