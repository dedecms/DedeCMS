<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/pub_collection.php");
if($nid=="") 
{
	ShowMsg("参数无效!","-1");	
	exit();
}
$co = new DedeCollection();
$co->Init();
$co->LoadFromDB($nid);
$dsql = new DedeSql(false);
$dsql->SetSql("Select count(aid) as dd From #@__courl where nid='$nid'");
$dsql->Execute();
$row = $dsql->GetObject();
$dd = $row->dd;
$dsql->Close();
if($dd==0)
{
	$unum = "没有记录或从来没有采集过这个节点！";
}
else
{
	$unum = "共有 $dd 个历史种子网址！<a href='javascript:SubmitNew();'>[<u>更新种子网址，并采集</u>]</a>";
}
require_once(dirname(__FILE__)."/templets/co_gather_start.htm");
$co->Close();

ClearAllLink();
?>
