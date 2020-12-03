<?php
require_once(dirname(__FILE__).'/config.php');
require_once(DEDEINC.'/datalistcp.class.php');
require_once(DEDEINC.'/common.func.php');
setcookie('ENV_GOBACK_URL',$dedeNowurl,time()+3600,'/');

$sql = "Select ad.aid,ad.tagname,tp.typename,ad.adname,ad.timeset,ad.endtime
From `#@__myad` ad left join `#@__arctype` tp on tp.id=ad.typeid
order by ad.aid desc";

$dlist = new DataListCP();
$dlist->SetTemplet(DEDEADMIN."/templets/ad_main.htm");
$dlist->SetSource($sql);
$dlist->display();

function TestType($tname)
{
	if($tname=="")
	{
		return "所有栏目";
	}
	else
	{
		return $tname;
	}
}

function TimeSetValue($ts)
{
	if($ts==0)
	{
		return "不限时间";
	}
	else
	{
		return "限时标记";
	}
}

?>