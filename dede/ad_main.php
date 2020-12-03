<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/pub_datalist.php");
require_once(dirname(__FILE__)."/../include/inc_functions.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");

function TestType($tname)
{
	if($tname=="") return "所有栏目";
	else return $tname;
}

function TimeSetValue($ts)
{
	if($ts==0) return "不限时间";
	else return "限时标记";
}

$sql = "Select 
#@__myad.aid,#@__myad.tagname,#@__arctype.typename,#@__myad.adname,#@__myad.timeset,#@__myad.endtime
From #@__myad
left join #@__arctype on #@__arctype.ID=#@__myad.typeid
order by #@__myad.aid desc
";

$dlist = new DataList();
$dlist->Init();
$dlist->SetSource($sql);
$dlist->SetTemplet(dirname(__FILE__)."/templets/ad_main.htm");
$dlist->display();
$dlist->Close();
?>